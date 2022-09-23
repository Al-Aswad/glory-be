<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Throwable;

trait Uuid
{
    public function getKeyType()
    {
        return 'string';
    }

    public function getIncrementing()
    {
        return false;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->id = (string) Str::uuid(); // generate uuid
                // Change id with your primary key
            } catch (Throwable $e) {
                abort(500, $e->getMessage());
            }
        });
    }
}
