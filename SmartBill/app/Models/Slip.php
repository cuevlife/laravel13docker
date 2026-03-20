<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'slip_template_id', 'image_path', 'extracted_data', 'status', 'processed_at'])]
class Slip extends Model
{
    protected function casts(): array
    {
        return [
            'extracted_data' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(SlipTemplate::class, 'slip_template_id');
    }
}
