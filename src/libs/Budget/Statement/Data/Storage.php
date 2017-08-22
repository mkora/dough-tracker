<?php


namespace Budget\Statement\Data;


class Storage implements \ArrayAccess, \Iterator, \Countable
{

  protected $data = [];

  public function offsetSet($offset, $value)
  {
    if (is_null($offset))
      $this->data[] = $value;
    else
      $this->data[$offset] = $value;
  }

  public function offsetExists($offset)
  {
    return isset($this->data[$offset]);
  }

  public function offsetUnset($offset)
  {
    unset($this->data[$offset]);
  }

  public function offsetGet($offset)
  {
    return isset($this->data[$offset]) ? $this->data[$offset] : null;
  }

  public function rewind() {
    reset($this->data);
  }

  public function current() {
    return current($this->data);
  }

  public function key() {
    return key($this->data);
  }

  public function next() {
    return next($this->data);
  }

  public function valid() {
    return $this->current() !== false;
  }

  public function count() {
   return count($this->data);
  }


}
