<?php

namespace Budget\Statement;

use Budget\Statement\{
    Data\TransactionStorage,
    Service\ServiceException
};
use Budget\FileSystem\File;

class StatementFile
{

    /**
     * File name
     *
     * @var string
     */
    protected $filename;

    /**
     * Directory
     *
     * @var string
     */
    protected $dir;

    /**
     * Statement type
     *
     * @var string
     */
    protected $statementType;

    /**
     * Data
     *
     * @var TransactionStorage
     */
    protected $data;

    public function __construct(string $filename, $dir = '')
    {
        $this->filename = $filename;
        $this->dir = $dir;
        $this->data = new TransactionStorage();
    }

    /**
     * Parser
     *
     * @throws ServiceException -> File doesn't exist || File is empty
     * @throws ErrorException -> Can't define a statement type
     *      || Can't create an object. Given type doesn't exists
     * @return int Number of ignored lines
     */
    public function parse(): int
    {
        $type = $this->getStatementType();

        $file = new File($this->filename, $this->dir);
        $rows = $file->parse();

        $ignored = 0;
        $factory = ServiceFactory::getInstance($type);
        foreach ($rows as $i => $row) {
            try {
                $this->data[] = $factory->getStatement($row);
            } catch (ServiceException $e) {
                // Ignore this line || Can't parse an empty line
                // So go to the next line
                $ignored++;
            }
        }
        return $ignored;
    }

    /**
     * Determines a statement type
     * by file name
     *
     * @throws ErrortExeception
     * @return string
     */
    public function getStatementType(): string
    {
        if ($this->statementType) {
            return $this->statementType;
        }

        $prefix = substr(lcfirst($this->filename), 0, 3);
        if (ServiceFactory::hasType($prefix)) {
            $this->statementType = $prefix;
            return $this->statementType;
        }

        throw new \ErrorException("Can't define a statement type");
    }

    /**
     * Sets a statement type
     *
     * @param string $name statement type
     *
     * @throws Error
     * @return void
     */
    public function setStatementType($name)
    {
        throw new \Error("You can't set a statement file type!");
    }

    /**
     * Sets data
     *
     * @param TransactionStorage $data data
     *
     * @return void
     */
    public function setData(TransactionStorage $data)
    {
        $this->data = $data;
    }

    /**
     * Returns data
     *
     * @return TransactionStorage
     */
    public function getData(): TransactionStorage
    {
        return $this->data;
    }
}
