<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'name', 'subdomain', 'status', 'address', 'tax_id', 'phone', 'config', 'max_slips'])]
class Merchant extends Model
{
    protected function casts(): array
    {
        return [
            'config' => 'array',
            'max_slips' => 'integer',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'archived' => 'Archived',
            default => 'Active',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function templates()
    {
        return $this->hasMany(SlipTemplate::class);
    }

    public function slips()
    {
        return $this->hasMany(Slip::class);
    }
}
