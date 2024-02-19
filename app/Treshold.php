<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Treshold extends Model
{
    protected $fillable = [
        'amount'
    ];

    public static function current()
    {
        return self::latest()->first();
    }
}
