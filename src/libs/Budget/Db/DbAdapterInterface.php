<?php

namespace Budget\Db;

use Budget\Statement\Data\Transaction as T;

interface DbAdapterInterface
{
  	public function getItems(array $c = array(), $sort = array()) : array;

  	public function insertItem(T $a) : int;

  	public function saveItem(T $a) : int;

  	public function aggregateItems(array $c = array());

  	public function countItems(array $c = array());

  	public function existItem(array $c);

}
