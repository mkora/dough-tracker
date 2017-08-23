<?php

namespace Budget\Statement\Service;
use Budget\Statement\Data\Transaction;

/**
 * Class that responsible for creating an instance
 * of Transaction object from a Discover statement
 *
 * @package    Finance
 * @author
*/
class Discover extends ServiceAbstract
{

  /**
  * Creates an instance of a row from a Discover statement
  *
  * The initial data clues are:
  *	1 => date (mm/dd/yyyy)
  *	2 => title
  *	==PAYMENT - THANK YOU then ignore it
  *	3 => sum (+) = (-), so if (-) => it's a return [upside down!]
  *
  * @param array $row
  * @throws ServiceException if don't need to cteate an object
  * @return Transaction
  */
  public function getStatement(array $row): Transaction
  {

     if (empty($row))
       throw new ServiceException("Can't parse an empty line. ");

    $title = $row[2] ?? '';
    if ($this->isIgnoredLine($title))
      throw new ServiceException("Ignore this line");

    $date = $row[1] ?? date("m/d/Y");
    $sum = $row[3] ?? 0;

    list ($m, $d, $y) = explode("/", $date);

    $type = ($sum > 0) ?
      parent::TRANSACTION_TYPE_DEBIT : parent::TRANSACTION_TYPE_CREDIT;

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
    return array("PAYMENT - THANK YOU");
  }
}