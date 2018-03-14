<?php
$config = file_exists(__DIR__ . '/config.php') 
    ? require('config.php') 
    : array();
$localConfig = file_exists(__DIR__ . '/config.local.php') 
    ? require('config.local.php') 
    : array();
return array_replace_recursive($config, 
  $localConfig);
