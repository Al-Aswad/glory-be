<?php

namespace App\Models;

use App\Helpers\Uuid;
use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRedeem extends Model
{
    use HasFactory,Uuid, Timestamp, SoftDeletes;
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'product_id',
        'redeem_date'
    ];
}
