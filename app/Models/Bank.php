<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'id',
        'bank_code',
        'bank_name',
        'type',
    ];
}