<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slip extends Model
{
    protected $fillable = [
        'uid',
        'user_id',
        'slip_template_id',
        'slip_batch_id',
        'image_path',
        'image_hash',
        'extracted_data',
        'status',
        'workflow_status',
        'labels',
        'processed_at',
        'reviewed_at',
        'approved_at',
        'exported_at',
        'archived_at',
    ];

    protected $casts = [
        'extracted_data' => 'array',
        'labels' => 'array',
        'processed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'exported_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($slip) {
            if (!$slip->uid) {
                $slip->uid = static::generateUid();
            }
        });
    }

    public static function generateUid(): string
    {
        $prefix = 'SB-' . now()->format('ym') . '-';
        $latest = static::where('uid', 'like', $prefix . '%')
            ->orderBy('uid', 'desc')
            ->first();

        $number = 1;
        if ($latest) {
            $lastNumber = (int) substr($latest->uid, -5);
            $number = $lastNumber + 1;
        }

        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(SlipTemplate::class, 'slip_template_id');
    }

    public function batch()
    {
        return $this->belongsTo(SlipBatch::class, 'slip_batch_id');
    }

    public function merchant()
    {
        return $this->hasOneThrough(
            Merchant::class,
            SlipTemplate::class,
            'id',
            'id',
            'slip_template_id',
            'merchant_id'
        );
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    // Workflow Constants
    const WORKFLOW_PENDING = 'pending';
    const WORKFLOW_REVIEWED = 'reviewed';
    const WORKFLOW_APPROVED = 'approved';
    const WORKFLOW_EXPORTED = 'exported';
    const WORKFLOW_ARCHIVED = 'archived';

    public static function workflowOptions()
    {
        return [
            self::WORKFLOW_PENDING => 'รอการประมวลผล',
            self::WORKFLOW_REVIEWED => 'แสกนแล้ว (AI)',
            self::WORKFLOW_APPROVED => 'ยืนยันความถูกต้องแล้ว',
            self::WORKFLOW_EXPORTED => 'ส่งออก Excel แล้ว',
            self::WORKFLOW_ARCHIVED => 'ย้ายเข้ากรุแล้ว',
        ];
    }
}
