<?php


namespace Budget\Statement\Data;

use Budget\Categorization\Categorization as C;

use Budget\Db\{
    ToDB as DB,
    ToDBException as DBException,
    DbSetterInterface as DBInterface
};

class TransactionStorage extends Storage implements DBInterface
{
    private $db;


    public function categorize($reset = false)
    {
        $c = new C;
        foreach ($this->data as $d) {
            $c->setItem($d);
            $c->set($reset);
        }
    }


    public function insert() : int
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


    public function save() : int
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


    public function setDbAdapter(DB $db) 
    {
        $this->db = $db;
    }

}
