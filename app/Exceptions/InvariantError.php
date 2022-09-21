<?php

namespace App\Exceptions;

use Exception;

class InvariantError extends ClientError
{
    public function __construct($message)
    {
        $this->message = $message;
        $this->code = 400;
        $this->name = 'InvariantError';
    }
}
