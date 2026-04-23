<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    protected $fillable = [
        'config_key',
        'config_value',
        'is_encrypted',
    ];
}
