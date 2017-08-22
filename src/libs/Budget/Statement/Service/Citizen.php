<?php

namespace Budget\Statement\Service;
use Budget\Statement\Data\Transaction;

/**
 * Class that responsible for creating an instance
 * of Transaction object from a Citizen statement
 *
 * @package    Finance
 * @author
*/
class Citizen extends ServiceAbstract
{

  /**
  * Creates an instance of a row from a Citizen statement
  *
  * The initial data clues are:
  * 0 => ==CHECK && title == '' then it is rent
  *	==DIRECT DEPOSIT then type = + || in title 'DIRECT DEP'
  *
  * 1 => date(m/d/yy)
  * 2 => ==Checking
  * 3 => title
  *		TO SAVINGS then ignore it
  *		DDA DEBIT - coins
  * 4 => sum (-) it's a payment, (+) = credit
  *
  * @param array $row
  * @throws ServiceException if don't need to cteate an object
  * @return Transaction
  */
  public function getStatement(array $row): Transaction
  {
    if (empty($row))
      throw new ServiceException("Can't parse an empty line");

    if (isset($row[2]) && stripos($row[2], 'Checking') === false)
      throw new ServiceException("Ignore this line");

    $title = $row[3] ?? '';

    if ($this->isIgnoredLine($title))
      throw new ServiceException("Ignore this line");

    $sum = $row[4] ?? 0;
    $type = ($sum > 0) ?
      parent::TRANSACTION_TYPE_CREDIT : parent::TRANSACTION_TYPE_DEBIT;

    $desc = $row[0] ?? '';

    if (stripos($desc, "CHECK") !== false && $title == ''
      && $type == parent::TRANSACTION_TYPE_DEBIT && abs($sum) >= 1000)
      $title = 'Rent check';

    if (stripos($desc, "CHECK") !== false && $title == ''
      && $type == parent::TRANSACTION_TYPE_DEBIT)
      $title = 'Payment by check';

    if (stripos($desc, "DEPOSIT") !== false  && $title == ''
      && $type == parent::TRANSACTION_TYPE_CREDIT)
      $title = 'Cash a check';

    if (stripos($desc, "DIRECT DEPOSIT") !== false)
      $type = parent::TRANSACTION_TYPE_CREDIT;

    if (stripos($title, "DIRECT DEP") !== false)
      $type = parent::TRANSACTION_TYPE_CREDIT;

    $date = $row[1] ?? date("m/d/Y");

    list ($m, $d, $y) = explode("/", $date);
    $y = $y + 2000;

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
  protected function getIgnoredLines() : array
  {
    return array(
      "TO SAVINGS",
      "BANK OF AMERICA",
      "DISCOVER CARD",
      "DCU",
    );
  }
}
