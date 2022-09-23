<?php

namespace App\Exceptions;

use App\Helpers\ResponseFormatter;

class NotFoundError extends ClientError
{
    public function __construct($message)
    {
        parent::__construct($message, 404);
        $this->name = 'NotFoundError';
    }

    public function render()
    {
        return ResponseFormatter::error(null, $this->getMessage(), $this->code);
    }
}
