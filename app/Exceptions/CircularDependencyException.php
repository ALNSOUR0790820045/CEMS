<?php

namespace App\Exceptions;

use Exception;

class CircularDependencyException extends Exception
{
    /**
     * Create a new circular dependency exception instance.
     *
     * @param  string  $message
     * @return void
     */
    public function __construct($message = 'Circular dependency detected between activities')
    {
        parent::__construct($message);
    }

    /**
     * Get the exception's context information.
     *
     * @return array
     */
    public function context()
    {
        return [
            'error_type' => 'circular_dependency',
            'message' => $this->getMessage(),
        ];
    }
}
