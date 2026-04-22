<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slip extends Model
{
    protected $fillable = [
        'uid',
        'user_id',
        'merchant_id',
        'image_path',
        'image_hash',
        'extracted_data',
        'workflow_status',
    ];

    protected $casts = [
        'extracted_data' => 'array',
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

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    // Workflow Constants
    const WORKFLOW_PENDING = 'pending';
    const WORKFLOW_REVIEWED = 'reviewed';
    const WORKFLOW_APPROVED = 'approved';
    const WORKFLOW_EXPORTED = 'exported';

    public static function workflowOptions()
    {
        return [
            self::WORKFLOW_PENDING => 'รอการประมวลผล',
            self::WORKFLOW_REVIEWED => 'แสกนแล้ว (AI)',
            self::WORKFLOW_APPROVED => 'ยืนยันความถูกต้องแล้ว',
            self::WORKFLOW_EXPORTED => 'ส่งออก Excel แล้ว',
        ];
    }
}
