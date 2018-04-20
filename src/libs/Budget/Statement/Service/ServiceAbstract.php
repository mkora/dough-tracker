<?php

namespace Budget\Statement\Service;

use Budget\Statement\Data\Transaction;

abstract class ServiceAbstract
{
    /**
     * Type is of a transcaction
     * (row in a statement) is credit
     *
     * @var int
     */
    const TRANSACTION_TYPE_CREDIT = 1;

    /**
     * Type is of a transcaction
     * (row in a statement) is debit
     * 
     * @var int
     */
    const TRANSACTION_TYPE_DEBIT = -1;

    /**
     * Creates an instance of a row
     *
     * @param array $row line
     * 
     * @abstract
     * 
     * @return Transaction
     */
    abstract public function getStatement(array $row): Transaction;

    /**
     * Returns array of titles that shouldn't be saved
     *
     * @abstract
     *
     * @return array
     */
    abstract protected function getIgnoredLines(): array;

    /**
     * Checkes whether a row shouldn't be saved
     *
     * @param string $title text
     *
     * @return bool
     */
    protected function isIgnoredLine($title): bool
    {
        foreach ($this->getIgnoredLines() as $i) {
            if (stripos($title, $i) !== false) {
                return true;
            }
        }
        return false;
    }
}
