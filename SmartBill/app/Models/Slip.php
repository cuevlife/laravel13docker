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

    public function merchant()
    {
        return $this->hasOneThrough(
            Merchant::class,
            SlipTemplate::class,
            'id', // Foreign key on slip_templates table...
            'id', // Foreign key on merchants table...
            'slip_template_id', // Local key on slips table...
            'merchant_id' // Local key on slip_templates table...
        );
    }
}
