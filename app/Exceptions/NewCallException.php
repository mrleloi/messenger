<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;

class NewCallException extends AuthorizationException
{
    /**
     * NewCallException constructor.
     *
     * @param  string  $message
     */
    public function __construct(string $message = 'New call failed.')
    {
        parent::__construct($message);
    }
}
