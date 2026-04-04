<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'name', 'subdomain', 'status', 'address', 'tax_id', 'phone', 'config'])]
class Merchant extends Model
{
    protected function casts(): array
    {
        return [
            'config' => 'array',
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

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
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
        return $this->hasManyThrough(Slip::class, SlipTemplate::class);
    }

    public function slipBatches()
    {
        return $this->hasMany(SlipBatch::class);
    }
}