<?php

namespace concrete5\HandleManager;

use PDO;
use PDOException;

class HandleStore
{
    /**
     * @var PDO
     */
    protected $connection;

    /**
     * @var string
     */
    protected $quotedTableName;

    /**
     * @var string[]|null
     */
    protected $systemIdentifiers;

    /**
     * Initialize the instance.
     *
     * @param PDO $connection
     * @param string $quotedTableName
     */
    public function __construct(PDO $connection, $quotedTableName)
    {
        $this->connection = $connection;
        $this->quotedTableName = $quotedTableName;
    }

    /**
     * Check if a handle is used in a system.
     *
     * @param string $handle The handle to be checked
     * @param string $systemIdentifier The identifier of the system
     *
     * @throws Exception\InvalidHandleException throws an InvalidHandleException if $handle is not a valid handle
     * @throws Exception\InvalidSystemIdentifierException throws an InvalidSystemIdentifierException if $systemIdentifier is not a valid system identifier
     * @throws PDOException throws a PDOException in case of generic database connection issues
     *
     * @return bool
     */
    public function isHandleUsedInSystem($handle, $systemIdentifier)
    {
        $this->checkHandle($handle);
        $this->checkSystemIdentifier($systemIdentifier);
        $usage = $this->getHandleUsage($handle);
        if ($usage === null) {
            $result = false;
        } else {
            $result = in_array($systemIdentifier, $usage, true);
        }

        return $result;
    }

    /**
     * Assign the handle to a specific system, optionally checking that it's not already assigned to other systems.
     *
     * @param string $handle The handle to be assigned
     * @param string $systemIdentifier The identifier of the system to assign the handle to
     * @param array $notUsedInSystems a list of system identifiers where the handle must not be already assigned to (use ['*'] to specify no other system)
     *
     * @throws Exception\InvalidHandleException throws an InvalidHandleException if $handle is not a valid handle
     * @throws Exception\InvalidSystemIdentifierException throws an InvalidSystemIdentifierException if $systemIdentifier is not a valid system identifier
     * @throws Exception\InvalidSystemIdentifierException throws an InvalidSystemIdentifierException if $systemIdentifier is included in the $notUsedInSystems
     * @throws Exception\InvalidSystemIdentifierException throws an InvalidSystemIdentifierException if $notUsedInSystems contains invalid system identifiers
     * @throws Exception\HandleAlreadyUsedInSystemsException throws an HandleAlreadyUsedInSystemsException if the handle is already assigned to the systems specified in the $notUsedInSystems parameter
     */
    public function setHandleUsage($handle, $systemIdentifier, array $notUsedInSystems = ['*'])
    {
        $this->checkHandle($handle);
        $this->checkSystemIdentifier($systemIdentifier);
        if (in_array($systemIdentifier, $notUsedInSystems, true)) {
            throw new Exception\InvalidSystemIdentifierException($systemIdentifier);
        }
        if (in_array('*', $notUsedInSystems, true)) {
            $notUsedInSystems = array_diff($this->getSystemIdentifiers(), [$systemIdentifier]);
        } else {
            foreach ($notUsedInSystems as $notUsedInSystem) {
                $this->checkSystemIdentifier($notUsedInSystem);
            }
        }
        $currentUsage = $this->getHandleUsage($handle);
        if ($currentUsage === null) {
            $sth = $this->connection->prepare("INSERT INTO {$this->quotedTableName} (handle, system_{$systemIdentifier}) VALUES (:handle, 1)");
            $sth->execute([
                ':handle' => $handle,
            ]);
        } else {
            $conflicts = array_intersect($currentUsage, $notUsedInSystems);
            if (count($conflicts) > 0) {
                throw new Exception\HandleAlreadyUsedInSystemsException($handle, $conflicts);
            }
            if (!in_array($systemIdentifier, $currentUsage, true)) {
                $sth = $this->connection->prepare("UPDATE {$this->quotedTableName} SET system_{$systemIdentifier} = 1 WHERE handle = :handle");
                $sth->execute([
                    ':handle' => $handle,
                ]);
            }
        }
    }

    /**
     * Unassign a handle from a system.
     *
     * @param string $handle The handle to be unassigned
     * @param string $systemIdentifier The identifier of the system to remove the handle from
     *
     * @throws Exception\InvalidHandleException throws an InvalidHandleException if $handle is not a valid handle
     * @throws Exception\InvalidSystemIdentifierException throws an InvalidSystemIdentifierException if $systemIdentifier is not a valid system identifier
     */
    public function unsetHandleUsage($handle, $systemIdentifier)
    {
        $this->checkHandle($handle);
        $this->checkSystemIdentifier($systemIdentifier);
        $currentUsage = $this->getHandleUsage($handle);
        if ($currentUsage !== null && in_array($systemIdentifier, $currentUsage, true)) {
            $sth = $this->connection->prepare("UPDATE {$this->quotedTableName} SET system_{$systemIdentifier} = 0 WHERE handle = :handle");
            $sth->execute([
                ':handle' => $handle,
            ]);
        }
    }

    /**
     * Is the transaction currently active?
     *
     * @return bool returns TRUE if a transaction is currently active, and FALSE if not
     */
    public function transactionActive()
    {
        return (bool) $this->connection->inTransaction();
    }

    /**
     * Initiate a transaction.
     *
     * @throws PDOException throws a PDOException if there is already a transaction started or the driver does not support transactions
     */
    public function transactionBegin()
    {
        $this->connection->beginTransaction();
    }

    /**
     * @throws PDOException throws a PDOException if there is no active transaction
     */
    public function transactionCommit()
    {
        $this->connection->commit();
    }

    /**
     * @throws PDOException throws a PDOException if there is no active transaction
     */
    public function transactionRollback()
    {
        $this->connection->rollBack();
    }

    /**
     * Check if a variable contains a valid handle.
     *
     * @param mixed $handle the handle to be checked
     *
     * @return bool
     */
    public function isHandleValid($handle)
    {
        return is_string($handle) && preg_match('/^[A-Za-z][A-Za-z0-9_\-]{0,63}$/', $handle);
    }

    /**
     * Check if a variable contains a valid system identifier.
     *
     * @param mixed $systemIdentifier the system identifier to be checked
     *
     * @return bool
     */
    protected function isSystemIdentifierValid($systemIdentifier)
    {
        return is_string($systemIdentifier) && in_array($systemIdentifier, $this->getSystemIdentifiers(), true);
    }

    /**
     * Get a list of system identifiers where the handle is currently used.
     *
     * @param string $handle The handle to be checked
     *
     * @throws PDOException throws a PDOException in case of generic database connection issues
     *
     * @return string[]|null return NULL if the handle is not present in the table, a list of system identifiers otherwise
     */
    protected function getHandleUsage($handle)
    {
        $sth = $this->connection->prepare("SELECT * FROM {$this->quotedTableName} WHERE handle = :handle");
        $sth->execute([
            ':handle' => $handle,
        ]);
        $row = $sth->fetch();
        if ($row === false) {
            $result = null;
        } else {
            $result = [];
            foreach ($this->getSystemIdentifiers() as $systemIdentifier) {
                if (!empty($row["system_{$systemIdentifier}"])) {
                    $result[] = $systemIdentifier;
                }
            }
        }

        return $result;
    }

    /**
     * @return string[]
     */
    protected function getSystemIdentifiers()
    {
        if ($this->systemIdentifiers === null) {
            $columnNames = [];
            $sth = $this->connection->query("SELECT * FROM {$this->quotedTableName} LIMIT 0");
            $numColumns = $sth->columnCount();
            for ($columnIndex = 0; $columnIndex < $numColumns; ++$columnIndex) {
                $column = $sth->getColumnMeta($columnIndex);
                $columnNames[] = $column['name'];
            }
            $systemIdentifiers = [];
            foreach ($columnNames as $columnName) {
                if (preg_match('/^system_(\w+)$/', $columnName, $matches)) {
                    $systemIdentifiers[] = $matches[1];
                }
            }
            $this->systemIdentifiers = $systemIdentifiers;
        }

        return $this->systemIdentifiers;
    }

    /**
     * @param mixed $handle
     *
     * @throws Exception\InvalidHandleException
     */
    protected function checkHandle($handle)
    {
        if (!$this->isHandleValid($handle)) {
            throw new Exception\InvalidHandleException($handle);
        }
    }

    /**
     * @param mixed $systemIdentifier
     *
     * @throws Exception\InvalidSystemIdentifierException
     */
    protected function checkSystemIdentifier($systemIdentifier)
    {
        if (!is_string($systemIdentifier) || !in_array($systemIdentifier, $this->getSystemIdentifiers(), true)) {
            throw new Exception\InvalidSystemIdentifierException($systemIdentifier);
        }
    }
}
