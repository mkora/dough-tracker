<?php

namespace Budget\FileSystem;

class File
{

  protected $dir;
  protected $file;


  public function __construct($filename, $dir = Directory::DEFAULT_DIR)
  {
    if ($dir) $this->dir = $dir;
    $this->file = $filename;

    ini_set('auto_detect_line_endings', true);

  }

  /**
  *
  * @throws ParseExeception
  * @return array
  */
  public function parse() : array
  {
    $fullname = $this->dir . $this->file;

    if (!file_exists($fullname))
      throw new ParserException("File " . $fullname . " doesn't exist!");

    $handle = fopen($fullname, 'r');

    $line = 0;
    $result = [];

    while (($row = fgetcsv($handle)) !== false) {
      $line++;
      if ($line==1)
        continue;

      $result[] = $row;
    }

    if (!$result)
      throw new ParserException("File " . $fullname . " is empty!");

    return $result;
  }


}
