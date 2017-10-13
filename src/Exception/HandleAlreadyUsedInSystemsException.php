<?php

namespace concrete5\HandleManager\Exception;

use concrete5\HandleManager\Exception;

class HandleAlreadyUsedInSystemsException extends Exception
{
    /**
     * @var string
     */
    protected $handle;

    /**
     * @var string[]
     */
    protected $systemIdentifiers;

    /**
     * @param string $handle The handle
     * @param string[] $systemIdentifiers the system identifiers where the handle is already in use
     */
    public function __construct($handle, array $systemIdentifiers)
    {
        $this->handle = $handle;
        $this->systemIdentifiers = array_values($systemIdentifiers);
        switch (count($this->systemIdentifiers)) {
            case 1:
                parent::__construct("The handle '{$handle}' is already in use in system '{$this->systemIdentifiers[0]}'.");
                break;
            default:
                parent::__construct("The handle '{$handle}' is already in use in these systems: '".implode("', '", $this->systemIdentifiers)."'");
                break;
        }
    }

    /**
     * Get the handle.
     *
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Get the system identifier where the handle is already in use.
     *
     * @return string[]
     */
    public function getSystemIdentifiers()
    {
        return $this->systemIdentifiers;
    }
}
