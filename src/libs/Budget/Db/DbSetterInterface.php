<?php

namespace Budget\Db;

interface DbSetterInterface
{
  public function setDbAdapter(ToDB $db);
}
