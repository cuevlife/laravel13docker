<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'file_name',
    'file_format',
    'export_mode',
    'slips_count',
    'template_ids',
    'search',
    'date_from',
    'date_to',
    'filters',
    'exported_at',
])]
class SlipExport extends Model
{
    protected function casts(): array
    {
        return [
            'template_ids' => 'array',
            'filters' => 'array',
            'date_from' => 'date',
            'date_to' => 'date',
            'exported_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
