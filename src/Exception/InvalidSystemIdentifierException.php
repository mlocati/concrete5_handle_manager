<?php

namespace concrete5\HandleManager\Exception;

use concrete5\HandleManager\Exception;

class InvalidSystemIdentifierException extends Exception
{
    /**
     * @var mixed
     */
    protected $invalidSystemIdentifier;

    /**
     * @param mixed $invalidSystemIdentifier The invalid system identifier
     */
    public function __construct($invalidSystemIdentifier)
    {
        $this->invalidSystemIdentifier = $invalidSystemIdentifier;
        parent::__construct('The system identifier specified is not valid.');
    }

    /**
     * Get the invalid system identifier.
     *
     * @return mixed
     */
    public function getInvalidSystemIdentifier()
    {
        return $this->invalidSystemIdentifier;
    }
}
