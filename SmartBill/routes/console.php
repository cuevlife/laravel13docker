<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;
use App\Models\AuditLog;
use App\Models\TokenLog;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:prune-logs', function () {
    $days = 90;
    $date = now()->subDays($days);

    $auditCount = AuditLog::where('created_at', '<', $date)->delete();
    $tokenCount = TokenLog::where('created_at', '<', $date)->delete();

    $this->info("Pruned {$auditCount} audit logs and {$tokenCount} token logs older than {$days} days.");
})->purpose('Prune old audit and token logs to keep the database lean');

Schedule::command('app:prune-logs')->daily();
