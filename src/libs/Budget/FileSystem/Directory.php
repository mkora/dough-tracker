<?php

namespace Budget\FileSystem;

class Directory
{

  const DEFAULT_DIR = './data/';

  protected $dir;

  protected $files = [];


  public function __construct($dir = self::DEFAULT_DIR)
  {
    $this->dir = $dir;
  }
  /**
  *
  * @throws ParserException
  * @return void
  */

  public function readDir()
  {
    if (!$this->files) {
    	$files = @scandir($this->dir);
    	foreach ($files as $k=>$d) {
        if (strpos($d, 'csv')!== false)
          $this->files[] = $d;
    	}

    } else {

      foreach ($this->files as $file) {
        if (!file_exists($file))
          throw new ParserException("Can't find file $file");
      }
    }


    if (!$this->files)
      throw new ParserException("Can't find any files in {$this->dir}");

  }

  /**
  *
  * @params array $files
  */
  public function setFiles(array $files)
  {
    $this->files = $files;
  }

  public function getFiles()
  {
    return $this->files;
  }

  public function getDir()
  {
    return $this->dir;
  }

  public function setDir(string $d)
  {
    $this->dir = $d;
  }



}
