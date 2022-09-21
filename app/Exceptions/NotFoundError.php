<?php

namespace App\Exceptions;

use Exception;

class NotFoundError extends ClientError
{
    public function __construct($message)
    {
        $this->message = $message;
        $this->code = 404;
        $this->name = 'NotFoundError';
    }
}
