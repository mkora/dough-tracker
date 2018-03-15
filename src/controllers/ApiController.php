<?php
namespace App;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface as IResponse,
    ServerRequestInterface as IRequest
};

use Budget\Categorization\Categorization as Categs;

class ApiController
{
    const FIRST_YEAR = 2014;

    protected $container;

    protected $logger;

    protected $db;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $this->container->get('logger');
        $this->db = $this->container->get('db');
    }

    public function getCategories(IRequest $request, IResponse $response, array $args)
    {
        $this->logger->addInfo("Call the api method", [__METHOD__]);

        $categories = Categs::getConfigLabels();
        $result = json_encode(['success' => true, 'data' => $categories]);
        $this->logger->addInfo("Sending the response with data", [$result]);

        $response->getBody()->write($result);
        return $response;
    }

    public function getDataDetails(IRequest $request, IResponse $response, array $args)
    {
        $this->logger->addInfo("Call the api method", [__METHOD__]);

        // get required params
        $month  = (int)$request->getQueryParam('m', date('m'));
        $year   = (int)$request->getQueryParam('y', date('Y'));
        $this->logger->addInfo("Get data from db",
            ["month" => $month, "year" => $year]);

        // get data
        try {
            $result = $this->db->getItems(
              	$query = ["month" => $month, "year" => $year],
              	['category'=>1, 'date'=>-1]
            );        
            foreach ($result as &$obj) {
              $obj = $obj->toArray();
            }

            if ($result) $query['result'] = $result;
            $query = ['success' => true, 'data' => $query];
        } catch (\Exception $e) {
            $query = ['success' => false, 'msg' => "Something went wrong! Check out logs/app.log"];
            $this->logger->addError('Error occured.', [$e]);
        }

        // send requested data
        $query = json_encode($query);
        $this->logger->addInfo("Sending the response with data", [$query]);
        $response->getBody()->write($query);
        return $response;
    }

    public function getGroupedData(IRequest $request, IResponse $response, array $args)
    {
        $this->logger->addInfo("Call the api method", [__METHOD__]);

        // get required params
        $month  = (int)$request->getQueryParam('m', date('m'));
        $year = (int)$request->getQueryParam('y', date('Y'));
        // get unnessesary params
        $category = $request->getQueryParam('cg', null);
        $type = $request->getQueryParam('tp', null);

        $query = ["month" => $month, "year" => $year];

        // requerst params sanitation
        $categories = array_keys(Categs::getConfigLabels());
        if (!in_array($category, $categories)) $category = null;
        if ($category !== null) $query["category"] = $category;

        if (($type != -1) && ($type != 1)) $type = null;
        if ($type !== null) $query["type"] = (int)$type;

        // get data
        try {
            $this->logger->addInfo("Get data from db", $query);
            $result = $this->db->aggregateItems([
                ['$match' => $query],
                ['$group' => [
                    '_id' => null,
                    'sum' => [
                      '$sum' => ['$multiply' => ['$sum', '$type']]
                    ]
                ]]
            ]);
            if (isset($result[0]) && $result[0]['sum'])
                $query['sum'] = $result[0]['sum'];

            $query['result'] = $query;
            $query = ['success' => true, 'data' => $query];
        } catch (\Exception $e) {
            $query = ['success' => false, 'msg' => "Something went wrong! Check out logs/app.log"];
            $this->logger->addError('Error occured.', [$e]);
        }

        // send requested data
        $query = json_encode($query);
        $this->logger->addInfo("Sending the response with data", [$query]);
        $response->getBody()->write($query);
        return $response;
    }

    public function getTabledData(IRequest $request, IResponse $response, array $args)
    {
        $this->logger->addInfo("Call the api method", [__METHOD__]);

        // required params
        $month = $request->getQueryParam('m', null);
        $year = $request->getQueryParam('y', null);

        if ($month !== null && $year !== null) {
            $query = ["month" => (int)$month, "year" => (int)$year];
            $group = ["category" => '$category'];
        }
        if ($month === null && $year !== null) {
            $query = ["year" => (int)$year];
            $group = ["category" => '$category', "month" => '$month'];
        }
        if ($month === null && $year === null) {
            $query = ["year" => ['$gte' => self::FIRST_YEAR]];
            $group = ["category" => '$category', "year" => '$year'];
        }

        try {
            // get data
            $this->logger->addInfo("Get data from db", ['$match' => $query, '$group' => $group]);
            $result = $this->db->aggregateItems([
                ['$match' => $query],
                ['$group' => [
                    '_id' => $group,
                    'sum'=> [
                      '$sum' => ['$multiply' => ['$sum', '$type']]
                    ]
                ]]
            ]);
            if ($result) {
              $query['result'] = $result;
            }
            $query = ['success' => true, 'data' => $query];
        } catch (\Exception $e) {
            $query = ['success' => false, 'msg' => "Something went wrong! Check out logs/app.log"];
            $this->logger->addError('Error occured.', [$e]);
        }

        // send requested data
        $query = json_encode($query);
        $this->logger->addInfo("Sending the response with data", [$query]);
        $response->getBody()->write($query);
        return $response;
    }

}
