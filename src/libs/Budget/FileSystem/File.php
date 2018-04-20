<?php

namespace Budget\FileSystem;

class File
{

    /**
     * Directory
     *
     * @var string
     */
    protected $dir;

    /**
     * File
     *
     * @var string
     */
    protected $file;

    /**
     * Constructor
     *
     * @param string $filename file
     * @param string $dir      directory
     * 
     * @return void
     */
    public function __construct($filename, $dir = Directory::DEFAULT_DIR)
    {
        if ($dir) {
            $this->dir = $dir;
        }
        $this->file = $filename;
        ini_set('auto_detect_line_endings', true);
    }

    /**
     * Parse a statement file
     *
     * @throws ParseExeception
     * @return array
     */
    public function parse(): array
    {
        $fullname = $this->dir . $this->file;

        if (!file_exists($fullname)) {
            $msg = "File " . $fullname . " doesn't exist!";
            throw new ParserException($msg);
        }

        $handle = fopen($fullname, 'r');

        $line = 0;
        $result = [];
        while (($row = fgetcsv($handle)) !== false) {
            $line++;
            if ($line==1) {
                continue;
            }
            $result[] = $row;
        }

        if (!$result) {
            $msg = "File " . $fullname . " is empty!";
            throw new ParserException($msg);
        }
        return $result;
    }
}
