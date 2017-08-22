<?php

namespace Budget\Statement;

use Budget\Statement\Service\{
  ServiceAbstract,
  ServiceExeption,
  BOA,
  Citizen,
  Discover
};

/**
 * Generates an instance of a class from the given type
 *
 * @package    Finance
 * @author
*/
class ServiceFactory
{
  /**
  * type is Bank of America statement (by prefix)
  * @param string
  */
  const STATEMENT_BOA = 'boa';

  /**
  * type is Citizen statement (by prefix)
  * @param string
  */
  const STATEMENT_CITIZEN = 'cit';

  /**
  * type is Discover statement (by prefix)
  * @param string
  */
  const STATEMENT_DISCOVER = 'dis';


  private static $statementTypes = array(
    self::STATEMENT_BOA,
    self::STATEMENT_CITIZEN,
    self::STATEMENT_DISCOVER,
  );

  /**
  * If type of a statement belongs to existed ones
  *
  * @static
  * @param string $type
  * @return bool
  */
  public static function hasType($type) : bool
  {
    return in_array($type, self::$statementTypes);
  }

  /**
  * Returns an instance of a class which would parse a statement line
  *
  * @static
  * @throws ErrorException
  * @param string $type
  * @return SIntarface
  */
  public static function getInstance($type) : ServiceAbstract
  {
    if (!self::hasType($type))
      throw new \ErrorException("Can't create an object. Given type doesn't exists");

    if ($type == self::STATEMENT_BOA) return new BOA();
    if ($type == self::STATEMENT_CITIZEN) return new Citizen();
    if ($type == self::STATEMENT_DISCOVER) return new Discover();

  }

}
