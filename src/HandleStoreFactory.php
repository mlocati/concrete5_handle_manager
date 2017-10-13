<?php

namespace concrete5\HandleManager;

use PDO;

class HandleStoreFactory
{
    /**
     * Name of the environment variable containing the DSN for the PDO connection.
     *
     * @var string
     */
    const ENVIRONAME_DB_DSN = 'C5_HANDLEMANAGER_DB_DSN';

    /**
     * Name of the environment variable containing the username for the PDO connection.
     *
     * @var string
     */
    const ENVIRONAME_DB_USERNAME = 'C5_HANDLEMANAGER_DB_USERNAME';

    /**
     * Name of the environment variable containing the password for the PDO connection.
     *
     * @var string
     */
    const ENVIRONAME_DB_PASSWORD = 'C5_HANDLEMANAGER_DB_PASSWORD';

    /**
     * Name of the environment variable containing the a JSON-encoded options array for the PDO connection.
     *
     * @var string
     */
    const ENVIRONAME_DB_OPTIONS = 'C5_HANDLEMANAGER_DB_OPTIONS';

    /**
     * Name of the environment variable containing the name of the table.
     *
     * @var string
     */
    const ENVIRONAME_DB_TABLENAME = 'C5_HANDLEMANAGER_DB_TABLENAME';

    /**
     * The name of the database table to be used when it's not specified via environment options.
     *
     * @var string
     */
    const DEFAULT_TABLE_NAME = 'HandleUsages';

    /**
     * Create a new HandleStore instance.
     *
     * @throws Exception\InvalidEnvironmentVariableException throws an InvalidEnvironmentVariableException in case of invalid environment variable values
     * @throws \PDOException throws a PDOException if the attempt to connect to the requested database fails
     *
     * @return HandleStore
     */
    public function create()
    {
        $pdo = $this->buildConnection();
        $tableName = $this->getTableName();

        return new HandleStore($pdo, "`{$tableName}`");
    }

    /**
     * @throws Exception\InvalidEnvironmentVariableException
     *
     * @return array
     */
    protected function getConnectionParameters()
    {
        $dsn = getenv(static::ENVIRONAME_DB_DSN);
        if (!$dsn) {
            throw new Exception\InvalidEnvironmentVariableException(static::ENVIRONAME_DB_DSN);
        }
        $username = getenv(static::ENVIRONAME_DB_USERNAME) ?: '';
        $passwd = getenv(static::ENVIRONAME_DB_PASSWORD) ?: '';
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8',
        ];
        $customOptionsStrings = getenv(static::ENVIRONAME_DB_OPTIONS);
        if ($customOptionsStrings) {
            $customOptionsStrings = @json_decode($optionsStrings, true);
            if (!is_array($customOptionsStrings)) {
                throw new Exception\InvalidEnvironmentVariableException(static::ENVIRONAME_DB_OPTIONS);
            }
            $options = $customOptionsStrings + $options;
        }

        return [$dsn, $username, $passwd, $options];
    }

    /**
     * @throws Exception\InvalidEnvironmentVariableException
     * @throws \PDOException
     *
     * @return PDO
     */
    protected function buildConnection()
    {
        list($dsn, $username, $passwd, $options) = $this->getConnectionParameters();

        return new PDO($dsn, $username, $passwd, $options);
    }

    /**
     * @throws Exception\InvalidEnvironmentVariableException
     *
     * @return string
     */
    protected function getTableName()
    {
        $result = getenv(static::ENVIRONAME_DB_TABLENAME);
        if ($result) {
            if (!preg_match('/^\w+$/', $result)) {
                throw new Exception\InvalidEnvironmentVariableException(static::ENVIRONAME_DB_OPTIONS);
            }
        } else {
            $result = static::DEFAULT_TABLE_NAME;
        }
    }
}
