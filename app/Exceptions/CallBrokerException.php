<?php

namespace App\Exceptions;

use Exception;

class CallBrokerException extends Exception
{
    /**
     * CallBrokerException constructor.
     *
     * @param  string  $message
     */
    public function __construct(string $message = 'Call broker failed.')
    {
        parent::__construct($message);
    }
}
