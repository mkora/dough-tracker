<?php

namespace Budget\Db;

use Budget\Statement\Data\Transaction as T;

interface DbAdapterInterface
{
    /**
     * Gets results from collection
     *
     * @param array $c    criterion
     * @param array $sort critetion
     *
     * @return array
     */
    public function getItems(
        array $c = array(),
        $sort = array()
    ): array;

    /**
     * Inserts Transaction to a collection
     *
     * @param T $a Transaction
     *
     * @return integer
     */
    public function insertItem(T $a): int;

    /**
     * Updates Transaction in a collection
     *
     * @param T $a Transaction
     *
     * @return integer
     */
    public function saveItem(T $a): int;

    /**
     * Returns aggregated data
     *
     * @param array $c creterion
     *
     * @return void
     */
    public function aggregateItems(array $c = array());

    /**
     * Returns number of records
     *
     * @param array $c creterion
     *
     * @return void
     */
    public function countItems(array $c = array());

    /**
     * Returns if item exists
     *
     * @param T $a Transaction
     *
     * @return boolean
     */
    public function existItem(T $a): bool;
}
