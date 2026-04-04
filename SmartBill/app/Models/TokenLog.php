<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'slip_id', 'delta', 'balance_after', 'type', 'description', 'meta'])]
class TokenLog extends Model
{
    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function slip()
    {
        return $this->belongsTo(Slip::class);
    }
}
