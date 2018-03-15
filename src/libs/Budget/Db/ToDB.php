<?php

namespace Budget\Db;

use Budget\Statement\Data\Transaction as T;
use \Budget\Db\ToDBException as DBException;

use \MongoDB\Driver\{
	Manager as MongoManager,
	Query as MongoQuery,
	Command as MongoCommand,
	BulkWrite as MongoWriter
};
use \MongoDB\Driver\Exception\{
	ConnectionException as MongoConnectionException,
	Exception as MongoException
};

class ToDB implements DbAdapterInterface
{

	private $manager;

	private $dbname;

	private $dbcollection;

	public function __construct($connection, $dbname, $dbcollection)
	{
		$this->dbname = $dbname;
		$this->dbcollection = $dbcollection;

		try {
			$this->manager = new MongoManager($connection);
		} catch (MongoConnectionException $e) {
			throw new ToDBException(get_class($e) . ': ' . $e->getMessage());
		}
	}


	public function getItems(array $c = array(), $sort = array()) : array
	{
		$result = array();

		$cursor = $this->manager->executeQuery("$this->dbname.$this->dbcollection",
			new MongoQuery($c, ['sort' => $sort]));

		foreach ($cursor as $d) {
			$result[] = new T((array)$d);
		}
		return $result;
	}


	public function insertItem(T $a) : int
	{
		$bulk = new MongoWriter();
		$id = $bulk->insert($a->toArray());

		try {
			$result = $this->manager->executeBulkWrite("$this->dbname.$this->dbcollection",
				$bulk);
		} catch (MongoException $e) {
		    throw new ToDBException(get_class($e) . ': ' . $e->getMessage());
		}
		return $result->getInsertedCount();

	}


	public function saveItem(T $a) : int
	{

		$v = $a->toArray();
		if (isset($v['category'])) unset($v['category']);
		$filter = $v;

		$update = ['$set' => $a->toArray()];

		$bulk = new MongoWriter();
		$bulk->update($filter, $update, ['multiple' => true]);

		try {
    		$result = $this->manager->executeBulkWrite("$this->dbname.$this->dbcollection",
				$bulk);
		} catch (MongoException $e) {
			throw new ToDBException(get_class($e) . ': ' . $e->getMessage());
		}

    	return $result->getModifiedCount();

	}


	public function aggregateItems(array $c = array())
	{
		/*
		 *
		.aggregate(
		[ {$match: {
				 $and: [
				{ category: "rent" },
						{ month : 6 },
				{ year: 2015},
					]
			}
		 },
		 {
			$group: { _id : null, sum : { $sum: "$sum" } }
		 }
		]);
		 */

		$cursor = $this->manager->executeCommand($this->dbname,
			new MongoCommand([
				'aggregate' => $this->dbcollection,
				'pipeline' => $c,
				'cursor' => new \stdClass,
			])
		);
		
		$result = [];
		foreach ($cursor as $item) {
			$result[] = (array)$item;
		}
		
		return $result ?? false;
	}

	public function existItem(T $a) : bool
	{
		$v = $a->toArray();
		$cursor = $this->manager->executeQuery("$this->dbname.$this->dbcollection",
			new MongoQuery($v));
		return count($cursor->toArray());

	}


	public function countItems(array $c = array())
	{
		throw new DBException('ToDB::countItems has not been implemented');
	}
}
