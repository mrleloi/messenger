<?php

namespace App\Exceptions;

use Illuminate\Http\Exceptions\ThrottleRequestsException;

class KnockException extends ThrottleRequestsException
{
    /**
     * KnockException constructor.
     *
     * @param  string  $message
     */
    public function __construct(string $message = 'Knock knock denied.')
    {
        parent::__construct($message);
    }
}
