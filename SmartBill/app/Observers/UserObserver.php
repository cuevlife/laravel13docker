<?php

namespace App\Observers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->log($user, 'user_created', "New user account registered: {$user->name} ({$user->username})");
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->isDirty('status')) {
            $this->log($user, 'user_status_updated', "User status changed to {$user->status}: {$user->name}");
        } else {
            $this->log($user, 'user_updated', "User profile updated: {$user->name}");
        }
    }

    /**
     * Handle the User "deleting" event.
     */
    public function deleting(User $user): void
    {
        // Manually trigger deletion of merchants to fire MerchantObserver (and eventually SlipObserver for file cleanup)
        $user->merchants()->each(function ($merchant) {
            $merchant->delete();
        });
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->log($user, 'user_deleted', "User account permanently removed: {$user->name}");
    }

    protected function log(User $user, string $event, string $description): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
