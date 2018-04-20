<?php

namespace Budget\FileSystem;

class Directory
{

    const DEFAULT_DIR = './data/';

    /**
     * Directory
     *
     * @var string
     */
    protected $dir;

    /**
     * List of files to parse
     *
     * @var array
     */
    protected $files = [];

    /**
     * Constructor
     *
     * @param string $dir directory
     *
     * @return void
     */
    public function __construct($dir = self::DEFAULT_DIR)
    {
        $this->dir = $dir;
    }

    /**
     * Reads the directory
     * for files to parse
     *
     * @throws ParserException
     * @return void
     */
    public function readDir()
    {
        if (!$this->files) {
            $files = @scandir($this->dir);
            foreach ($files as $k=>$d) {
                if (strpos($d, 'csv')!== false) {
                    $this->files[] = $d;
                }
            }
        } else {
            foreach ($this->files as $file) {
                if (!file_exists($file)) {
                    $msg = "Can't find file $file";
                    throw new ParserException($msg);
                }
            }
        }
        if (!$this->files) {
            $msg = "Can't find any files in {$this->dir}";
            throw new ParserException($msg);
        }
    }

    /**
     * Sets list of files to parse
     *
     * @param array $files list of files
     *
     * @return void
     */
    public function setFiles(array $files)
    {
        $this->files = $files;
    }

    /**
     * Returns a list of files
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Returns the directory
     * of files to parse
     *
     * @return void
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Sets the directory
     * of files to parse
     *
     * @param string $d directory
     *
     * @return void
     */
    public function setDir(string $d)
    {
        $this->dir = $d;
    }
}
