<?php

namespace concrete5\HandleManager\Exception;

use concrete5\HandleManager\Exception;

class InvalidEnvironmentVariableException extends Exception
{
    /**
     * @var string
     */
    protected $environmentVariableName;

    /**
     * Initialize the instance.
     *
     * @param string $environmentVariableName The environment variable name
     */
    public function __construct($environmentVariableName)
    {
        $this->environmentVariableName = $environmentVariableName;
        parent::__construct("The {$environmentVariableName} environment variable contains invalid data.");
    }

    /**
     * Get the environment variable name.
     *
     * @return string
     */
    public function getEnvironmentVariableName()
    {
        return $this->environmentVariableName;
    }
}
