<?php

namespace Budget\Statement\Service;
use Budget\Statement\Data\Transaction;

/**
 * Abstract class which children create an Transaction object
 * for given type of a statement (from what bank it came from)
 *
 * @package    Finance
 * @author
*/
abstract class ServiceAbstract
{
  /**
  * type is of a transcaction (row in statement) is credit
  * @param int
  */
  const TRANSACTION_TYPE_CREDIT = 1;

  /**
  * type is of a transcaction (row in statement) is debit
  * @param int
  */
  const TRANSACTION_TYPE_DEBIT = -1;

  /**
  * Creates an instance of a row
  *
  * @abstract
  * @param array $row
  * @return Transaction
  */
  abstract public function getStatement(array $row) : Transaction;

  /**
  * Returns array of titles that shouldn't be saved
  *
  * @abstract
  * @return array
  */
  abstract protected function getIgnoredLines() : array;

  /**
  * Checkes whether a row shouldn't be saved
  *
  * @param string $title
  * @return bool
  */
  protected function isIgnoredLine($title) : bool
  {
  	foreach ($this->getIgnoredLines() as $i)
  	{
  		if (stripos($title, $i) !== false)
  			return true;
  	}
    return false;
  }

}
