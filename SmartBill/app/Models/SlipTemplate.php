<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'name', 'main_instruction', 'ai_fields'])]
class SlipTemplate extends Model
{
    protected function casts(): array
    {
        return [
            'ai_fields' => 'array',
        ];
    }

    public function slips()
    {
        return $this->hasMany(Slip::class);
    }
}
