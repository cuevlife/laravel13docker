<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'merchant_id', 'name', 'main_instruction', 'ai_fields', 'export_layout'])]
class SlipTemplate extends Model
{
    protected function casts(): array
    {
        return [
            'ai_fields' => 'array',
            'export_layout' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function slips()
    {
        return $this->hasMany(Slip::class);
    }
}
