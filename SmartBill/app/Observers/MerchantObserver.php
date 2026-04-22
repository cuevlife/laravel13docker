<?php

namespace App\Observers;

use App\Models\Merchant;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class MerchantObserver
{
    /**
     * Handle the Merchant "created" event.
     */
    public function created(Merchant $merchant): void
    {
        $this->log($merchant, 'folder_created', "New folder created: {$merchant->name}");
    }

    /**
     * Handle the Merchant "updated" event.
     */
    public function updated(Merchant $merchant): void
    {
        $this->log($merchant, 'folder_updated', "Folder details updated: {$merchant->name}");
    }

    /**
     * Handle the Merchant "deleting" event.
     */
    public function deleting(Merchant $merchant): void
    {
        // Trigger individual slip deletions to invoke SlipObserver (and delete files)
        $merchant->slips()->each(function ($slip) {
            $slip->delete();
        });
    }

    /**
     * Handle the Merchant "deleted" event.
     */
    public function deleted(Merchant $merchant): void
    {
        $this->log($merchant, 'folder_deleted', "Folder permanently removed: {$merchant->name}");
    }

    protected function log(Merchant $merchant, string $event, string $description): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => Merchant::class,
            'auditable_id' => $merchant->id,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
