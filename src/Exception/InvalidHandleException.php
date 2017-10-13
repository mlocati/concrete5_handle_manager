<?php

namespace concrete5\HandleManager\Exception;

use concrete5\HandleManager\Exception;

class InvalidHandleException extends Exception
{
    /**
     * @var mixed
     */
    protected $invalidHandle;

    /**
     * @param mixed $invalidHandle The invalid system identifier
     */
    public function __construct($invalidHandle)
    {
        $this->invalidHandle = $invalidHandle;
        parent::__construct('The handle specified is not valid.');
    }

    /**
     * Get the invalid handle.
     *
     * @return mixed
     */
    public function getInvalidHandle()
    {
        return $this->invalidHandle;
    }
}
