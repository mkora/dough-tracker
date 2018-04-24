<?php

namespace Budget\Statement;

use Budget\Statement\Service\{
    ServiceAbstract,
    ServiceExeption,
    BOA,
    Citizen,
    Discover
};

class ServiceFactory
{
    /**
     * Type is Bank of America statement
     * (by prefix)
     *
     * @var string
     */
    const STATEMENT_BOA = 'boa';

    /**
     * Type is Citizen statement
     * (by prefix)
     *
     * @var string
     */
    const STATEMENT_CITIZEN = 'cit';

    /**
     * Type is Discover statement
     * (by prefix)
     *
     * @var string
     */
    const STATEMENT_DISCOVER = 'dis';

    /**
     * Array of statement types
     *
     * @static
     *
     * @var array
     */
    protected static $statementTypes = array(
        self::STATEMENT_BOA,
        self::STATEMENT_CITIZEN,
        self::STATEMENT_DISCOVER,
    );

    /**
     * Checks if type of a statement
     * belongs to existed ones
     *
     * @param string $type type
     *
     * @static
     * @return bool
     */
    public static function hasType($type): bool
    {
        return in_array($type, self::$statementTypes);
    }

    /**
     * Returns an instance of a class
     * which is responsible of parsing
     * a statement line by line
     *
     * @param string $type type
     *
     * @static
     * @throws ErrorException
     * @return SIntarface
     */
    public static function getInstance($type): ServiceAbstract
    {
        if (!self::hasType($type)) {
            $msg = "Can't create an object. Given type doesn't exists";
            throw new \ErrorException($msg);
        }
        if ($type == self::STATEMENT_BOA) {
            return new BOA();
        }
        if ($type == self::STATEMENT_CITIZEN) {
            return new Citizen();
        }
        if ($type == self::STATEMENT_DISCOVER) {
            return new Discover();
        }
    }
}
