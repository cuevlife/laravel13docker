<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'name', 'address', 'tax_id', 'phone', 'config'])]
class Merchant extends Model
{
    protected function casts(): array
    {
        return [
            'config' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function templates()
    {
        return $this->hasMany(SlipTemplate::class);
    }

    public function slips()
    {
        return $this->hasManyThrough(Slip::class, SlipTemplate::class);
    }
}
