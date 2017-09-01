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
        	$this->logIt($e->getMessage() . "! Exit", Log::ERROR);
        	exit;
        }

        $files = $parser->getFiles();
        foreach ($files as $i => $filename) {

          	$this->logIt("Reading file " . $filename . "\n");

          	$statement = new StatementFile($filename, $dir);
          	try	{
          		  $ignored = $statement->parse();
          	} catch (\Exception $e) {
          		  $this->logIt(get_class($e) . ": " . $e->getMessage() . "! Continue", Log::WARNING);
          		  continue; // go to the next file
          	}

          	/* @param $data TransactionStorage */
          	$data = $statement->getData();

            $data->setDbAdapter($this->container->get('db'));
          	$data->categorize();

          	try {
          		  $saved = $data->insert();
          	} catch (Throwable $e) {
          		  $this->logIt($e->getMessage() . "! Continue", Log::ERROR);
          		  continue; // go to the next file
          	}

          	$this->logIt("{$data->count()} data lines received, $saved were saved, ".
          			"$ignored were ignored and first line was skipped", Log::INFO);
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
            $this->logIt($e->getMessage() . "! Can't save. Exit!", Log::ERROR);
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
            $this->logIt($e->getMessage() . "! Exit", Log::ERROR);
            exit;
        }

        $strs = array();
        foreach ($parser->getFiles() as $i => $filename) {

            $this->logIt("Reading file " . $filename . '...');
            $statement = new StatementFile($filename, $dir);

            try	{
                $ignored = $statement->parse();
            } catch (\Exception $e) {
                $this->logIt(get_class($e) . ": " . $e->getMessage() . "! Continue", Log::WARNING);
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


    protected function logIt($txt, $type = Log::INFO)
    {
        $this->container->get('logger')->log($type, $txt);
    }

    public function genMockDataFile(IRequest $request, IResponse $response, array $args) {
        $this->logIt("Generating mock data");
        $magicNumbers = [
          'rent' => [
            'count' => 1,
            'min'   => 1200,
            'max'   => 1200,
          ],
          'utilities' => [
            'count' => 3,
            'min'   => 50,
            'max'   => 100,
          ],
          'groceries' => [
            'count' => 10,
            'min'   => 5,
            'max'   => 100,
          ],
          'stuff' => [
            'count' => 2,
            'min'   => 50,
            'max'   => 300,
          ],
          'unexpected' => [
            'count' => 1,
            'min'   => 5,
            'max'   => 1200,
          ],
          'income' => [
            'count' => 2,
            'min'   => 1000,
            'max'   => 1500,
          ]
        ];

        $str = '';
        for ($year = 2014; $year <= (int)date('Y'); $year++) {
            $startMonth = ($year == 2014) ? 7 : 1;
            $endMonth = ($year == (int)date('Y')) ? (int)date('m'): 12;
            for ($month = $startMonth; $month <= $endMonth; $month++) {
                foreach ($magicNumbers as $categ => $nums)
                    for ($i = 0; $i < $nums['count']; $i++) {
                      $date = mktime(0, 0, 0, $month, rand(1, 28), $year);
                      $title = ucfirst($categ) . ' title #' . ($i + 1);
                      $type = ($categ === 'income') ? 1 : -1;

                      if ($categ === 'unexpected') {
                        $sum = rand(0, 1) * rand($nums['min'], $nums['max']);
                      } else {
                        $sum = rand($nums['min'], $nums['max']);
                      }

                      $tpl = "{\"month\": $month, \"year\": $year, \"date\" : $date, \"type\" : $type, \"category\" : \"$categ\", \"title\" : \"$title\", \"sum\" : $sum }\n";

                      $str .= $tpl;
                    }
              }
          }

          if (!$str) {
            $this->logIt('No data found. Nothing to write! Exit', Log::ERROR);
            exit;
          }
          $dir = '../data/';
          file_put_contents($dir.'mock-data.json', $str);
          $this->logIt("Writing to {$dir}mock-data.json... Finished!");
    }

 }
