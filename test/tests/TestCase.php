<?php

namespace concrete5\HandleManager\Test;

use PDO;
use PHPUnit_Framework_TestCase;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    const TABLENAME = 'test';

    /**
     * @var PDO|null
     */
    protected static $defaultConnection;

    /**
     * @var HandleStore|null
     */
    protected static $defaultStore;

    /**
     * @var callable|null
     */
    private static $connectionCreator;

    protected function setUp()
    {
        if (self::$defaultConnection->inTransaction()) {
            self::$defaultConnection->rollBack();
        }
        self::$defaultConnection->exec('delete from '.self::TABLENAME);
    }

    /**
     * @param string $databasePath
     */
    public static function initialize(callable $connectionCreator)
    {
        self::$connectionCreator = $connectionCreator;
        self::$defaultConnection = call_user_func(self::$connectionCreator);
        self::$defaultStore = new HandleStore(self::$defaultConnection, self::TABLENAME);
    }

    /**
     * @return HandleStore
     */
    protected static function createNewStore()
    {
        return new HandleStore(call_user_func(self::$connectionCreator), self::TABLENAME);
    }
}
