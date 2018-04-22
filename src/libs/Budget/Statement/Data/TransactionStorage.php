<?php


namespace Budget\Statement\Data;

use Budget\Categorization\Categorization as C;

use Budget\Db\{
    ToDB as DB,
    ToDBException as DBException,
    DbAdapterInterface,
    DbSetterInterface as DBInterface
};

class TransactionStorage extends Storage implements DBInterface
{
    /**
     * DB Adapater
     *
     * @var DbAdapterInterface
     */
    protected $db;

    /**
     * Finds and sets a category
     * for all transactions
     *
     * @param boolean $reset overload the existed
     *
     * @return void
     */
    public function categorize($reset = false)
    {
        $c = new C;
        foreach ($this->data as $d) {
            $c->setItem($d);
            $c->set($reset);
        }
    }

    /**
     * Inserts all transactions
     * from storage to db
     *
     * @return integer
     */
    public function insert(): int
    {
        if (!$this->db) {
            throw new DBException('Can\'t find DB Adapter');
        }
        $saved = 0;
        try {
            foreach ($this->data as $d) {
                if (!$this->db->existItem($d)) {
                    $saved += $this->db->insertItem($d);
                }
            }
        } catch (DBException $e) {
            throw $e;
        }
        return $saved;
    }

    /**
     * Saves all transactions
     * from storage to db
     *
     * @return integer
     */
    public function save(): int
    {
        if (!$this->db) {
            throw new DBException('Can\'t find DB Adapter');
        }
        $saved  = 0;
        try {
            foreach ($this->data as $d) {
                $saved += (int)$this->db->saveItem($d);
            }
        } catch (DBException $e) {
            throw $e;
        }
        return $saved;
    }

    /**
     * Sets DB Adapter
     *
     * @param DbAdapterInterface $db adapter
     *
     * @return void
     */
    public function setDbAdapter(DbAdapterInterface $db)
    {
        $this->db = $db;
    }
}
