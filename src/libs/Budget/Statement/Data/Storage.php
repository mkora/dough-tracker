<?php

namespace Budget\Statement\Data;

class Storage implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * Data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Sets an offset
     *
     * @param int   $offset offset
     * @param mixed $value  value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * Checks if an offset exists
     *
     * @param int $offset offset
     *
     * @return boolean
     */    
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Unsets an offset
     *
     * @param int $offset offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Gets an offset
     *
     * @param int $offset offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
     * Returns a first element
     *
     * @return mixed
     */
    public function rewind()
    {
        reset($this->data);
    }

    /**
     * Returns a current element
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * Returns a key of data array
     *
     * @return int
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Returns a next element
     *
     * @return void
     */
    public function next()
    {
        return next($this->data);
    }

    /**
     * Returns if an element is valid
     *
     * @return bool
     */
    public function valid()
    {
        return $this->current() !== false;
    }

    /**
     * Returns a count of elements
     *
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }
}
