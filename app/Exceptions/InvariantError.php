<?php

namespace App\Exceptions;

use App\Helpers\ResponseFormatter;

class InvariantError extends ClientError
{
    public function __construct($message)
    {
        parent::__construct($message, 400);
        $this->name = 'InvariantError';
    }

    public function render()
    {
        return ResponseFormatter::error(null, $this->getMessage(), $this->code);
    }
}
