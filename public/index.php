<?php
/**
 * Output{}: _an_ amount where (params)
 *   <server>/api/data-groupby?m=1&y=2017[&cg=rent][&type=-1]
 *
 * Output{}: a list
 *  <server>/api/data-details?m=1&y=2017
 *
 * Output{}: a table of a grouped data where (params)
 *  <server>/api/data-tableby[?m=1][&y=2017]
 *
 * Output{}: a list
 *  <server>/api/categories
*/

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require "../vendor/autoload.php";

$config = include "../src/config/index.php";
if (!$config) {
    $response = [
        'success' => false,
        'msg' => "Something went wrong! Please, check out " .
            "for config.php or config.local.php"
    ];
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    echo json_encode($response);
    exit;
}

$app = new \Slim\App(
    ["settings" => $config]
);
$container = $app->getContainer();
$container['logger'] = function ($c) {
    $logger = new \Monolog\Logger('api');
    $handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $handler->setFormatter(
        $formatter = new \Monolog\Formatter\LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context%\n"
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

$container['errorHandler']
    = $container['phpErrorHandler'] = function ($container) {
        return function (
            $request,
            $response,
            $error
        ) use ($container) {
            $container
                ->get('logger')
                ->addError(
                    "An Error Occured (500):",
                    [$error]
                );
            $response
                ->getBody()
                ->rewind();
            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(
                    json_encode(
                        [
                            'success' => false,
                            'msg' => "Unknown Error Occured"
                        ]
                    )
                );
        };
    };

$container['notFoundHandler'] = function ($container) {
    return function (
        $request,
        $response
    ) use ($container) {
        $container
            ->get('logger')
            ->addError(
                "Not Found(404): Command not found",
                [
                    'URI' => $request->getUri(),
                    'header' => $request->getHeaders(),
                    'body' => $request->getParsedBody()
                ]
            );
        $response
            ->getBody()
            ->rewind();
        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json')
            ->write(
                json_encode(
                    [
                        'success' => false,
                        'msg' => "Resource Not Found"
                    ]
                )
            );
    };
};

$app->add(
    function ($req, $res, $next) {
        $response = $next($req, $res);
        return $response
            ->withHeader(
                'Access-Control-Allow-Origin',
                "*"
            )
            ->withHeader(
                'Access-Control-Allow-Headers',
                "X-Requested-With, Content-Type, Accept, Origin, Authorization"
            )
            ->withHeader(
                'Access-Control-Allow-Methods',
                "GET, POST, PUT, DELETE, OPTIONS"
            );
    }
);

// --/api/data-details?m=1&y=2017
$app->get(
    '/api/data-details',
    App\ApiController::class . ':getDataDetails'
);

// --/api/data-groupby?m=1&y=2017[&cg=rent][&type=-1]
$app->get(
    '/api/data-groupby',
    App\ApiController::class . ':getGroupedData'
);

// --/api/data-tableby[?m=1][&y=2017]
$app->get(
    '/api/data-tableby',
    App\ApiController::class . ':getTabledData'
);

// --/api/categories
$app->get(
    '/api/categories',
    App\ApiController::class . ':getCategories'
);

$app->run();
