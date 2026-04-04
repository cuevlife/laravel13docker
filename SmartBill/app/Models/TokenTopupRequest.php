<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'reviewed_by',
    'requested_tokens',
    'amount_paid',
    'currency',
    'status',
    'payment_slip_path',
    'note',
    'admin_note',
    'reviewed_at',
])]
class TokenTopupRequest extends Model
{
    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
