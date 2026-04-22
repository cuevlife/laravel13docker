<?php

namespace App\Observers;

use App\Models\Slip;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class SlipObserver
{
    /**
     * Handle the Slip "created" event.
     */
    public function created(Slip $slip): void
    {
        $this->log($slip, 'slip_processed', "New slip processed: {$slip->uid}");
    }

    /**
     * Handle the Slip "deleted" event.
     */
    public function deleted(Slip $slip): void
    {
        // Automated Storage Cleanup (Phase 2)
        if ($slip->image_path) {
            Storage::disk('public')->delete($slip->image_path);
        }

        $this->log($slip, 'slip_deleted', "Slip record removed: {$slip->uid}");
    }

    protected function log(Slip $slip, string $event, string $description): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => Slip::class,
            'auditable_id' => $slip->id,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
