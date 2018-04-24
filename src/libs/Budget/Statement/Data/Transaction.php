<?php

namespace Budget\Statement\Data;

class Transaction
{

    /**
     * Month
     *
     * @var int
     */
    protected $month;

    /**
     * Year
     *
     * @var int
     */
    protected $year;

    /**
     * Full date
     * 
     * @var string
     */
    protected $date;

    /**
     * Debit or credit [-1, 1]
     *
     * @var int
     */
    protected $type;

    /**
     * Assigned category alias
     *
     * @var string
     */
    protected $category;

    /**
     * Description
     *
     * @var string
     */
    protected $title;

    /**
     * Amount
     *
     * @var float
     */
    protected $sum;

    /**
     * Constructor
     *
     * @param array $a data
     */
    public function __construct(array $a = array())
    {
        if (!empty($a)) {
            $this->fromArray($a);
        }
    }

    /**
     * Initializes an object from an array
     *
     * @param array $a data
     *
     * @return void
     */
    public function fromArray(array $a)
    {
        if (isset($a['month'])) {
            $this->month = $a['month'];
        }
        if (isset($a['year'])) {
            $this->year = $a['year'];
        }
        if (isset($a['date'])) {
            $this->date = $a['date'];
        }
        if (isset($a['title'])) {
            $this->title = $a['title'];
        }
        if (isset($a['sum'])) {
            $this->sum = $a['sum'];
        }
        if (isset($a['type'])) {
            $this->type = $a['type'];
        }
        if (isset($a['category'])) {
            $this->category = $a['category'];
        }
    }

    /**
     * Returns an object as an array
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->month !== null) {
            $a['month'] = $this->month;
        }
        if ($this->year !== null) {
            $a['year'] = $this->year;
        }
        if ($this->date !== null) {
            $a['date'] = $this->date;
        }
        if ($this->type !== null) {
            $a['type'] = $this->type;
        }
        if ($this->category !== null) {
            $a['category'] = $this->category;
        }
        if ($this->title !== null) {
            $a['title'] = $this->title;
        }
        if ($this->sum !== null) {
            $a['sum'] = $this->sum;
        }
        return $a;
    }

    /**
     * Sets a category
     *
     * @param string $category category
     *
     * @return void
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * Returns a category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Sets a date
     *
     * @param string $date timestamp
     *
     * @return void
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Returns a date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets a month
     *
     * @param int $month month
     *
     * @return void
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * Returns a month
     *
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Sets an amount
     *
     * @param float $sum amount
     *
     * @return void
     */
    public function setSum($sum)
    {
        $this->sum = $sum;
    }

    /**
     * Returns an amount
     *
     * @return float
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * Sets a title
     *
     * @param string $title title
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns a title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets a type: credit/debet
     *
     * @param int $type -1/1
     *
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns a type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets an year
     *
     * @param int $year year
     *
     * @return void
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * Returns an year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }
}
