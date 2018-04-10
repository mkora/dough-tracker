<?php
/**
 * php cli.php /cli/parse-files GET
 * php cli.php /cli/parse-files GET file=*.CSV
 *
 * Service utils:
 *  php cli.php /cli/recategorize GET
 *  php cli.php /cli/output-titles GET
 *  php cli.php /cli/mock-file GET
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require "../vendor/autoload.php";

$config = include "../src/config/index.php";
if (!$config) {
    echo "Something went wrong! Please, check out " .
        " for config.php or config.local.php";
    exit;
}

$app = new \Slim\App(
    ["settings" => $config]
);
$container = $app->getContainer();
$container['logger'] = function ($c) {
    $logger = new \Monolog\Logger('cli');
    $handler = new \Monolog\Handler\StreamHandler(
        'php://stdout',
        \Monolog\Logger::DEBUG
    );
    $handler->setFormatter(
        $formatter = new \Monolog\Formatter\LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message%\n"
        )
    );
    $logger->pushHandler($handler);
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['mongo'];
    return new \Budget\Db\ToDB(
        $db['connection'],
        $db['dbname'],
        $db['dbcollection']
    );
};

$app->add(new \pavlakis\cli\CliRequest());

$container['errorHandler'] = function ($container) {
    return function (
        $request,
        $response,
        $exception
    ) use ($container) {
        $response
            ->getBody()
            ->rewind();
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/text')
            ->write(
                "An Error Occured: Exception (500): " .
                "{$exception->getMessage()} in {$exception->getFile()}"
            );
    };
};

$container['phpErrorHandler'] = function ($container) {
    return function (
        $request,
        $response,
        $error
    ) use ($container) {
        $response
            ->getBody()
            ->rewind();
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/text')
            ->write(
                "An Error Occured: PHP Error: {$error->getMessage()}! " .
                "File (line: {$error->getLine()}) {$error->getFile()}"
            );
    };
};

$container['notFoundHandler'] = function ($container) {
    return function (
        $request,
        $response
    ) use ($container) {
        $response
            ->getBody()
            ->rewind();
        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/text')
            ->write("An Error Occured: Not Found(404): Command not found\n");
    };
};

$app->map(
    ['GET'],
    "/cli/parse-files",
    App\CliController::class . ":parse"
);

$app->map(
    ['GET'],
    "/cli/recategorize",
    App\CliController::class . ":recategorize"
);

$app->map(
    ['GET'],
    "/cli/output-titles",
    App\CliController::class . ":outputTitles"
);

$app->map(
    ['GET'],
    "/cli/mock-file",
    App\CliController::class .":genMockDataFile"
);

$app->run();
