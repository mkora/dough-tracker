<?php

namespace App;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\{
    ResponseInterface as IResponse,
    ServerRequestInterface as IRequest
};

use \Monolog\Logger as Log;

use \Budget\FileSystem\{
    Directory as DirectoryParser,
    ParserException
};

use \Budget\Statement\{
    Data\TransactionStorage as Storage,
    StatementFile,
    Service\ServiceException
};

class CliController
{

    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Parse all statments in the directory
     *
     * @param IRequest  $request  request
     * @param IResponse $response response
     * @param array     $args     parameters
     *
     * @return void
     */
    public function parse(
        IRequest $request,
        IResponse $response,
        array $args
    ): void {
        $files = $request->getQueryParam('file', array());

        $dir = '../data/';
        $parser = new DirectoryParser($dir);
        if ($files) {
            $parser->setFiles([$options['file']]);
        }

        try {
            $parser->readDir();
        } catch (ParserException $e) {
            $this->logIt(
                "{$e->getMessage()}! Exit",
                Log::ERROR
            );
            exit;
        }

        $files = $parser->getFiles();
        foreach ($files as $i => $filename) {

            $this->logIt("Reading file {$filename}\n");

            $statement = new StatementFile($filename, $dir);
            try {
                $ignored = $statement->parse();
            } catch (\Exception $e) {
                $this->logIt(
                    "{get_class($e)}: {$e->getMessage()}! Continue",
                    Log::WARNING
                );
                continue; // go to the next file
            }

            /* @param $data TransactionStorage */
            $data = $statement->getData();
            $data->setDbAdapter($this->container->get('db'));
            $data->categorize();

            try {
                $saved = $data->insert();
            } catch (Throwable $e) {
                $this->logIt(
                    "{$e->getMessage()}! Continue",
                    Log::ERROR
                );
                continue; // go to the next file
            }

            $this->logIt(
                "{$data->count()} data lines received, $saved were saved, " .
                    "$ignored were ignored and first line was skipped",
                Log::INFO
            );
            $this->logIt("...");
        }
    }

    /**
     * Update categories for all transactions
     *
     * @param IRequest  $request  request
     * @param IResponse $response response
     * @param array     $args     parameters
     *
     * @return void
     */
    public function recategorize(
        IRequest $request,
        IResponse $response,
        array $args
    ): void {
        $this->logIt("Update categories for all items in DB...");

        $data = new Storage;
        $data->setDbAdapter($this->container->get('db'));

        $items = $this->container->get('db')->getItems();
        foreach ($items as $d) {
            $data[] = $d;
        }
        $data->categorize(true);

        try {
            $saved = $data->save();
        } catch (Throwable $e) {
            $this->logIt(
                "{$e->getMessage()}! Can't save. Exit!",
                Log::ERROR
            );
            exit;
        }

        $this->logIt(
            "{$data->count()} data found, " .
                "$saved were updated"
        );
    }

    /**
     * Find all descriptions from statements
     *
     * @param IRequest  $request  request
     * @param IResponse $response response
     * @param array     $args     parameters
     *
     * @return void
     */
    public function outputTitles(
        IRequest $request,
        IResponse $response,
        array $args
    ): void {
        $this->logIt("Find and write titles in a file");

        $dir = '../data/';
        $parser = new DirectoryParser($dir);
        try {
            $parser->readDir();
        } catch (ParserException $e) {
            $this->logIt(
                "{$e->getMessage()}! Exit",
                Log::ERROR
            );
            exit;
        }

        $strs = array();
        $files = $parser->getFiles();
        foreach ($files as $i => $filename) {

            $this->logIt("Reading file {$filename} ...");
            $statement = new StatementFile($filename, $dir);

            try {
                $ignored = $statement->parse();
            } catch (\Exception $e) {
                $this->logIt(
                    "{get_class($e)}: {$e->getMessage()}! Continue",
                    Log::WARNING
                );
                continue;
            }

            $checked = 0;
            $data = $statement->getData();
            foreach ($data as $k => $o) {
                $checked++;
                if (in_array($o->getTitle(), $strs) === false) {
                    $strs[] = $o->getTitle();
                }
            }

            $this->logIt(
                "{$checked} lines were checked, " .
                    "{$ignored} were ignored and first line was skipped"
            );
        }

        if (!empty($strs)) {
            $str = implode("\n", $strs);
            $filename = "{$dir}Summary-Of-Titles.txt";
            file_put_contents($filename, $str);
            $this->logIt(
                "Writing to {$filename}... Finished!"
            );
        }
    }

    /**
     * Util: Wtire text to a log
     *
     * @param string $txt  message
     * @param int    $type type
     *
     * @return void
     */
    protected function logIt(
        string $txt,
        int $type = Log::INFO
    ): void {
        $this->container
            ->get('logger')
            ->log($type, $txt);
    }
}
