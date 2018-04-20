<?php

namespace Budget\Statement\Service;

use Budget\Statement\Data\Transaction;

class BOA extends ServiceAbstract
{

    /**
     * Creates an instance of a row from a BOA statement
     *
     * The initial data clues are:
     *  0 => date (mm/dd/yyyy)
     *  2 => title
     *      ANNUAL FEE,
     *      == PAYMENT - ELECTRONIC then ignore it
     *  4 => sum (-) , if with (+) it's a return
     *
     * @param array $row line
     *
     * @throws ServiceException if don't need to cteate an object
     * @return Transaction
     */
    public function getStatement(array $row): Transaction
    {
        if (empty($row)) {
            $msg = "Can't parse an empty line";
            throw new ServiceException($msg);
        }

        $title = $row[2] ?? '';
        if ($this->isIgnoredLine($title)) {
            throw new ServiceException("Ignore this line");
        }

        $date = $row[0] ?? date("m/d/Y");
        $sum = $row[4] ?? 0;

        list ($m, $d, $y) = explode("/", $date);

        $type = ($sum > 0)
            ? parent::TRANSACTION_TYPE_CREDIT
            : parent::TRANSACTION_TYPE_DEBIT;

        $data = array(
            'title' => $title,
            'date'  => mktime(0, 0, 0, (int)$m, (int)$d, $y),
            'month' => (int)$m,
            'year'  => (int)$y,
            'sum'   => (float)abs($sum),
            'type'  => $type,
        );

        return new Transaction($data);
    }

    /**
     * Returns array of titles that shouldn't be saved
     *
     * @return array
     */
    protected function getIgnoredLines(): array
    {
        return array("PAYMENT - ELECTRONIC");
    }
}
