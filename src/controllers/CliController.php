<?php
/**
 * php cli.php /cli/parse-files GET
 * php cli.php /cli/parse-files GET file=*.CSV
 *
 * php cli.php /cli/recategorize GET
 * php cli.php /cli/output-titles GET
 *
 */

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

    const LOG_DEBUG = Log::DEBUG;
    const LOG_INFO = Log::INFO;
    const LOG_NOTICE = Log::NOTICE;
    const LOG_WARNING = Log::WARNING;
    const LOG_ERROR = Log::ERROR;
    const LOG_CRITICAL = Log::CRITICAL;
    const LOG_ALERT = Log::ALERT;
    const LOG_EMERGENCY = Log::EMERGENCY;

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

    }

    public function parse(IRequest $request, IResponse $response, array $args)
    {
        $files = $request->getQueryParam('file', array());

        $dir = '../data/';
        $parser = new DirectoryParser($dir);
        if ($files) $parser->setFiles([$options['file']]);

        try {
        	$parser->readDir();
        } catch (ParserException $e) {
        	$this->logIt($e->getMessage() . "! Exit", self::LOG_ERROR);
        	exit;
        }

        $files = $parser->getFiles();
        foreach ($files as $i => $filename) {

          	$this->logIt("Reading file " . $filename . "\n");

          	$statement = new StatementFile($filename, $dir);
          	try	{
          		  $ignored = $statement->parse();
          	} catch (\Exception $e) {
          		  $this->logIt(get_class($e) . ": " . $e->getMessage() . "! Continue", self::LOG_WARNING);
          		  continue; // go to the next file
          	}

          	/* @param $data TransactionStorage */
          	$data = $statement->getData();

            $data->setDbAdapter($this->container->get('db'));
          	$data->categorize();

          	try {
          		  $saved = $data->insert();
          	} catch (Throwable $e) {
          		  $this->logIt($e->getMessage() . "! Continue", self::LOG_ERROR);
          		  continue; // go to the next file
          	}

          	$this->logIt("{$data->count()} data lines received, $saved were saved, ".
          			"$ignored were ignored and first line was skipped", self::LOG_INFO);
            $this->logIt("...");
        }

    }

    public function recategorize(IRequest $request, IResponse $response, array $args)
    {
        $this->logIt("Update categories for all items in DB...");

        $data = new Storage;
        $data->setDbAdapter($this->container->get('db'));

        foreach ($this->container->get('db')->getItems() as $d) {
            $data[] = $d;
        }
        $data->categorize(true);

        try {
            $saved = $data->save();
        } catch (Throwable $e) {
            $this->logIt($e->getMessage() . "! Can't save. Exit!", self::LOG_ERROR);
            exit;
        }

        $this->logIt("{$data->count()} data found, $saved were updated");

    }


    public function outputTitles(IRequest $request, IResponse $response, array $args)
    {
        $this->logIt("Find and write titles in a file");

        $dir = '../data/';
        $parser = new DirectoryParser($dir);
        try {
            $parser->readDir();
        } catch (ParserException $e) {
            $this->logIt($e->getMessage() . "! Exit", self::LOG_ERROR);
            exit;
        }

        $strs = array();
        foreach ($parser->getFiles() as $i => $filename) {

            $this->logIt("Reading file " . $filename . '...');
            $statement = new StatementFile($filename, $dir);

            try	{
                $ignored = $statement->parse();
            } catch (\Exception $e) {
                $this->logIt(get_class($e) . ": " . $e->getMessage() . "! Continue", self::LOG_WARNING);
                continue;
            }

            $checked = 0;
            $data = $statement->getData();
            foreach ($data as $k => $o) {
                if (in_array($o->getTitle(), $strs) === false)
            	     $strs[] = $o->getTitle();
                $checked++;
            }

            $this->logIt("{$checked} lines were checked, " .
                "{$ignored} were ignored and first line was skipped");

        }

        if (!empty($strs))
        {
            $str = implode("\n", $strs);
            file_put_contents($dir.'Summary-Of-Titles.txt', $str);
            $this->logIt("Writing to {$dir}Summary-Of-Titles.txt... Finished!");
        }
    }


    protected function logIt($txt, $type = self::LOG_INFO)
    {
        $this->container->get('logger')->log($type, $txt);
    }

 }
