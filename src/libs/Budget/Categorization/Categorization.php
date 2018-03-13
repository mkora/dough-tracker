<?php

namespace Budget\Categorization;

use Statement\Data\Transaction;

class Categorization
{
	const PATH = '/rules/credit_categ_local.php';

	const DEFAULT_DEBIT_CATEGORY = 'groceries';

	const DEFAULT_CREDIT_CATEGORY = 'income';

	/**
	 * @param array
	 */
	protected $config;

	/**
	 * @param T
	 */
	protected $item;

	/**
	 * @param mixed $item
	 */
	public function setItem($item)
	{
		$this->item = $item;
	}

	/**
	 * @return mixed
	 */
	public function getItem()
	{
		return $this->item;
	}


	public function __construct()
	{
		$this->config = self::getConfig();

	}


	public static function getConfig()
	{
		$path = __DIR__ . self::PATH;
		if (!file_exists($path))
			$path = str_replace('_local', '', $path);
		if (!file_exists($path))
			throw new \Exception('Can\'t find rules/credit_config.php!');
		return include($path);
	}


	public static function getConfigLabels()
	{
			$config = self::getConfig();
			$res = [];
			foreach ($config as $val) {
					$res[$val['label']] = $val['title'];
			}
			return $res;
	}


	public function set($reset = false)
	{
		if (!$this->item)
			throw new \Exception('Item doesn\'t set!');

		$title = $this->item->getTitle();

		// whether to reset category or not
		if ($reset === false && $this->item->getCategory())
			return;

		$category = NULL;

		foreach ($this->config as $config)
		{
			if ($this->item->getType() > 0)
				$category = self::DEFAULT_CREDIT_CATEGORY;

			$find = $this->findCategory($config, $title);

			if ($find === true)
			{
				$category = $config['label'];
				break;
			}
		}

		if (is_null($category))
			$category = self::DEFAULT_DEBIT_CATEGORY;

		$this->item->setCategory($category);

	}


	protected function findCategory($config, $title)
	{
		if (array_key_exists('rule', $config) && $config['rule'] !== NULL)
		{
			foreach ($config['rule'] as $rule)
				if ($this->matched($title, $rule))
					return true;
		} else	return null;

		return false;

	}


	protected function matched($str, $rule)
	{
		return preg_match('#'.$rule.'#u', $str);
	}



	public function humanCategoryNames()
	{
		if ($c = $this->findByLabel($this->item->getCategory()))
			$this->item->setCategory($c);
	}


	public function labelCategoryNames()
	{
		if ($c = $this->findByTitle($this->item->getCategory()))
			$this->item->setCategory($c);
	}


	public function findByLabel($label)
	{
		foreach ($this->config as $config)
		{
			if ($config['label'] == $label)
				return $config['title'];

		}

		return false;
	}


	public static function getTitleByLabel($label)
	{
		$n = new self(array());

		return $n->findByLabel($label);
	}


	public function findByTitle($title)
	{
		foreach ($this->config as $config)
		{
			if ($config['title'] == $title)
				return $config['label'];

		}
		return false;
	}


	public static function getLibelByTitle($title)
	{
		$n = new self(array());
		return $n->findByTitle($title);
	}



}
