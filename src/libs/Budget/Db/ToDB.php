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
    /**
     * DB
     *
     * @var MongoManager
     */
    protected $manager;

    /**
     * DB name
     *
     * @var string
     */
    protected $dbname;

    /**
     * DB collection name
     *
     * @var string
     */
    protected $dbcollection;

    /**
     * Constructor
     *
     * @param string $connection   connection params
     * @param string $dbname       name
     * @param string $dbcollection collection name
     */
    public function __construct(
        $connection,
        $dbname,
        $dbcollection
    ) {
        $this->dbname = $dbname;
        $this->dbcollection = $dbcollection;
        try {
            $this->manager = new MongoManager($connection);
        } catch (MongoConnectionException $e) {
            $msg = get_class($e) . ': ' . $e->getMessage();
            throw new ToDBException($msg);
        }
    }

    /**
     * Gets results from collection
     *
     * @param array $c    where criterion
     * @param array $sort sort critetion
     *
     * @return array
     */
    public function getItems(
        array $c = array(),
        $sort = array()
    ): array {
        $result = array();
        $dbcoll = "$this->dbname.$this->dbcollection";
        $cursor = $this
            ->manager
            ->executeQuery(
                $dbcoll,
                new MongoQuery(
                    $c,
                    ['sort' => $sort]
                )
            );
        foreach ($cursor as $d) {
            $result[] = new T((array)$d);
        }
        return $result;
    }

    /**
     * Inserts Transaction to a collection
     *
     * @param T $a Transaction
     *
     * @return integer
     */
    public function insertItem(T $a): int
    {
        $bulk = new MongoWriter();
        $id = $bulk->insert($a->toArray());
        $dbcoll = "$this->dbname.$this->dbcollection";
        try {
            $result = $this
                ->manager
                ->executeBulkWrite(
                    $dbcoll,
                    $bulk
                );
        } catch (MongoException $e) {
            $msg = get_class($e) . ': ' . $e->getMessage();
            throw new ToDBException($msg);
        }
        return $result->getInsertedCount();
    }

    /**
     * Updates Transaction in a collection
     *
     * @param T $a Transaction
     *
     * @return integer
     */
    public function saveItem(T $a): int
    {
        $v = $a->toArray();
        if (isset($v['category'])) {
            unset($v['category']);
        }
        $filter = $v;

        $update = [
            '$set' => $a->toArray()
        ];

        $bulk = new MongoWriter();
        $bulk->update(
            $filter,
            $update,
            ['multiple' => true]
        );

        $dbcoll = "$this->dbname.$this->dbcollection";
        try {
            $result = $this
                ->manager
                ->executeBulkWrite(
                    $dbcoll,
                    $bulk
                );
        } catch (MongoException $e) {
            $msg = get_class($e) . ': ' . $e->getMessage();
            throw new ToDBException($msg);
        }
        return $result->getModifiedCount();
    }

    /**
     * Returns aggregated data
     *
     * @param array $c creterion
     *
     * @return void
     */
    public function aggregateItems(array $c = array())
    {
        /**
         * Example:
        .aggregate([
            {
                $match: {
                    $and: [
                        { category: "rent" },
                        { month: 6 },
                        { year: 2015},
                    ]
                }
            },
            {
                $group: {
                    _id : null,
                    sum : { $sum: "$sum" }
                }
            }
        ]);
         */

        $cursor = $this
            ->manager
            ->executeCommand(
                $this->dbname,
                new MongoCommand(
                    [
                        'aggregate' => $this->dbcollection,
                        'pipeline' => $c,
                        'cursor' => new \stdClass,
                    ]
                )
            );
        $result = [];
        foreach ($cursor as $item) {
            $result[] = (array)$item;
        }
        return $result ?? false;
    }

    /**
     * Returns if item exists
     *
     * @param T $a Transaction
     *
     * @return boolean
     */
    public function existItem(T $a): bool
    {
        $v = $a->toArray();
        $dbcoll = "$this->dbname.$this->dbcollection";
        $cursor = $this
            ->manager
            ->executeQuery(
                $dbcoll,
                new MongoQuery($v)
            );
        return count($cursor->toArray());
    }

    /**
     * Returns number of records
     *
     * @param array $c creterion
     *
     * @return void
     */
    public function countItems(array $c = array())
    {
        $msg = 'ToDB::countItems has not been implemented';
        throw new DBException($msg);
    }
}
