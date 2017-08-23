<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$config = require('../src/config/config.php');
$localConfig = require('../src/config/config.local.php');
$config = array_replace_recursive($config, $localConfig);

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('cli');
    $handler = new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG);
    $handler->setFormatter($formatter = new \Monolog\Formatter\LineFormatter("[%datetime%] %channel%.%level_name%: %message%\n"));
    $logger->pushHandler($handler);
    return $logger;
};

// @todo add settings to DB in parse fileS
$container['db'] = function ($c) {
    $db = $c['settings']['mongo'];
    return new \Budget\Db\ToDB($db['connection'], $db['dbname'], $db['dbcollection']);
};


$app->add(new \pavlakis\cli\CliRequest());

$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        $response->getBody()->rewind();
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'text/text')
            ->write("An Error Occured: Exception (500): " .
                $exception->getMessage() . " in " . $exception->getFile());
    };
};

$container['phpErrorHandler'] = function ($container) {
    return function ($request, $response, $error) use ($container) {
        $response->getBody()->rewind();
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'text/text')
            ->write("An Error Occured: PHP Error: {$error->getMessage()}! " .
                "File (line: {$error->getLine()}) {$error->getFile()} ");
    };
};

$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        $response->getBody()->rewind();
        return $response->withStatus(404)
            ->withHeader('Content-Type', 'text/text')
            ->write("An Error Occured: Not Found(404): Command not found\n");
    };
};

$app->map(['GET'], '/cli/parse-files', App\CliController::class . ':parse');
$app->map(['GET'], '/cli/recategorize', App\CliController::class . ':recategorize');
$app->map(['GET'], '/cli/output-titles', App\CliController::class . ':outputTitles');

$app->run();