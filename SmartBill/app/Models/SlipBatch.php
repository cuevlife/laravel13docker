<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['merchant_id', 'created_by', 'name', 'status', 'note', 'scanned_at', 'archived_at'])]
class SlipBatch extends Model
{
    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function slips()
    {
        return $this->hasMany(Slip::class, 'slip_batch_id');
    }
}