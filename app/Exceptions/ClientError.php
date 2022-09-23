<?php

namespace App\Exceptions;

use App\Helpers\ResponseFormatter;
use Exception;

class ClientError extends Exception
{
    public function __construct($message, $code = 400)
    {
        $this->message = $message;
        $this->code = $code;
    }

    public function render()
    {
        return ResponseFormatter::error(null, $this->getMessage(), $this->code);
    }
}
