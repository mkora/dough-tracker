<?php

namespace Budget\Categorization;

use Statement\Data\Transaction;

class Categorization
{
    const PATH = '/rules/credit_categ_local.php';

    const DEFAULT_DEBIT_CATEGORY = 'groceries';

    const DEFAULT_CREDIT_CATEGORY = 'income';

    /**
     * Config
     *
     * @var array
     */
    protected $config;

    /**
     * Transaction
     *
     * @var Transaction
     */
    protected $item;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = self::getConfig();
    }

    /**
     * Setter for Transaction
     *
     * @param Transaction $item object
     *
     * @return void
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * Getter for Transaction
     *
     * @return Transaction
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Inits and returns config
     * with categories and rules
     *
     * @static
     * @return array
     */
    public static function getConfig()
    {
        $path = __DIR__ . self::PATH;
        if (!file_exists($path)) {
            $path = str_replace('_local', '', $path);
        }
        if (!file_exists($path)) {
            $msg = 'Can\'t find rules/credit_config.php!';
            throw new \Exception($msg);
        }
        return include $path;
    }

    /**
     * Returns category lables from config
     *
     * @return array
     */
    public static function getConfigLabels()
    {
        $config = self::getConfig();
        $res = [];
        foreach ($config as $val) {
            $res[$val['label']] = $val['title'];
        }
        return $res;
    }

    /**
     * Finds and sets a category
     *
     * @param boolean $reset overload the existed
     *
     * @return void
     */
    public function set($reset = false)
    {
        if (!$this->item) {
            $msg = 'Item doesn\'t set!';
            throw new \Exception($msg);
        }

        $title = $this->item->getTitle();

        // whether to reset category or not
        if ($reset === false 
            && $this->item->getCategory()
        ) {
            return;
        }

        $category = null;

        foreach ($this->config as $config) {
            if ($this->item->getType() > 0) {
                $category = self::DEFAULT_CREDIT_CATEGORY;
            }

            $find = $this->findCategory($config, $title);

            if ($find === true) {
                $category = $config['label'];
                break;
            }
        }

        if (is_null($category)) {
            $category = self::DEFAULT_DEBIT_CATEGORY;
        }

        $this->item->setCategory($category);
    }

    /**
     * Finds a category that
     * matches a config rule
     *
     * @param array  $config rule
     * @param string $title  text
     *
     * @return boolean
     */
    protected function findCategory($config, $title)
    {
        $result = false;
        if (array_key_exists('rule', $config) 
            && ($config['rule'] !== null)
        ) {
            foreach ($config['rule'] as $rule) {
                if ($this->matched($title, $rule)) {
                    return $result = true;
                }
            }
        } else {
            return $result = null;
        }
        return $result;
    }

    /**
     * Checks if a text matches a rule
     *
     * @param string $str  text
     * @param string $rule rule
     *
     * @return boolean
     */
    protected function matched($str, $rule)
    {
        return preg_match('#'.$rule.'#u', $str);
    }

    /**
     * Updates a category label
     * in Transaction to a full
     * category name
     *
     * @return void
     */
    public function humanCategoryNames()
    {
        $label = $this->item->getCategory();
        if ($c = $this->findByLabel($label)) {
            $this->item->setCategory($c);
        }
    }

    /**
     * Updates a category full name
     * in Transaction to a category lable
     *
     * @return void
     */
    public function labelCategoryNames()
    {
        $name = $this->item->getCategory();
        if ($c = $this->findByTitle($name)) {
            $this->item->setCategory($c);
        }
    }

    /**
     * Finds a category full name
     * in the config by the label
     *
     * @param string $label label
     *
     * @return string|false
     */
    public function findByLabel($label)
    {
        foreach ($this->config as $config) {
            if ($config['label'] == $label) {
                return $config['title'];
            }
        }
        return false;
    }

    /**
     * Finds a category full name
     * in the config by the label
     * 
     * @param string $label label
     * 
     * @static
     * @return string|false
     */
    public static function getTitleByLabel($label)
    {
        $n = new self(array());
        return $n->findByLabel($label);
    }

    /**
     * Finds a category label
     * in the config by the text name
     *
     * @param string $title text
     *
     * @return string|false
     */
    public function findByTitle($title)
    {
        foreach ($this->config as $config) {
            if ($config['title'] == $title) {
                return $config['label'];
            }
        }
        return false;
    }

    /**
     * Finds a category label
     * in the config by the text name
     *
     * @param string $title text
     * 
     * @static
     * @return string|false
     */
    public static function getLibelByTitle($title)
    {
        $n = new self(array());
        return $n->findByTitle($title);
    }
}
