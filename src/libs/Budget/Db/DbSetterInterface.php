<?php

namespace Budget\Db;

interface DbSetterInterface
{
    /**
     * Sets DB Adapter
     *
     * @param DbAdapterInterface $db adapter
     *
     * @return void
     */
    public function setDbAdapter(DbAdapterInterface $db);
}
