<?php

namespace Budget\Statement\Data;

/**
 * Class for storing data
 *
 * @package    Finance
 * @author
*/
class Transaction
{

	/**
  * Month, for easy quering
	*
  * @param int
  */
	private $month;

	/**
  * Year, for easy quering
	*
  * @param int
  */
	private $year;

	/**
  *
  * @param string
  */
	private $date;

	/**
  * Debit or credit [-1, 1]
	*
  * @param int
  */
	private $type;

	/**
  * Assigned category alias
	*
  * @param string
  */
	private $category;

	/**
  * Description
	*
  * @param string
  */
	private $title;

	/**
  * Amount
	*
  * @param float
  */
	private $sum;


	public function __construct(array $a = array())
	{
		if (!empty($a))
			$this->fromArray($a);
	}

	/**
  * Initializes an object from an array
  *
  * @param array $a
  * @return void
  */
	public function fromArray(array $a)
	{
		if (isset($a['month']))
			$this->month = $a['month'];
		if (isset($a['year']))
			$this->year = $a['year'];
		if (isset($a['date']))
			$this->date = $a['date'];
		if (isset($a['title']))
			$this->title = $a['title'];
		if (isset($a['sum']))
			$this->sum = $a['sum'];
		if (isset($a['type']))
			$this->type = $a['type'];
		if (isset($a['category']))
			$this->category = $a['category'];
	}

	/**
  * Returns an object as an array
  *
  * @param void
  * @return array
  */
	public function toArray()
	{
		$a = array();
		if ($this->month !== NULL)
			$a['month'] = $this->month;
		if ($this->year !== NULL)
			$a['year'] = $this->year;
		if ($this->date !== NULL)
			$a['date'] = $this->date;
		if ($this->type !== NULL)
			$a['type'] = $this->type;
		if ($this->category !== NULL)
			$a['category'] = $this->category;
		if ($this->title !== NULL)
			$a['title'] = $this->title;
		if ($this->sum !== NULL)
			$a['sum'] = $this->sum;
		return $a;
	}

	/**
	 * @param string $category
	 */
	public function setCategory($category)
	{
		$this->category = $category;
	}

	/**
	 * @return string
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * @param string $date
	 */
	public function setDate($date)
	{
		$this->date = $date;
	}

	/**
	 * @return string
	 */
	public function getDate()
	{
		return $this->date;
	}

	/**
	 * @param int $month
	 */
	public function setMonth($month)
	{
		$this->month = $month;
	}

	/**
	 * @return int
	 */
	public function getMonth()
	{
		return $this->month;
	}

	/**
	 * @param float $sum
	 */
	public function setSum($sum)
	{
		$this->sum = $sum;
	}

	/**
	 * @return float
	 */
	public function getSum()
	{
		return $this->sum;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param int $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param int $year
	 */
	public function setYear($year)
	{
		$this->year = $year;
	}

	/**
	 * @return int
	 */
	public function getYear()
	{
		return $this->year;
	}


}
