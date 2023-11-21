<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;

class MessengerComposerException extends AuthorizationException
{
    /**
     * MessengerComposerException constructor.
     *
     * @param  string  $message
     */
    public function __construct(string $message = 'Messenger Composer Failed.')
    {
        parent::__construct($message);
    }
}
