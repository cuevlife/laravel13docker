<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $usageThisMonth = \App\Models\TokenLog::where('user_id', $user->id)
            ->where('type', 'usage')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('delta');

        $rawLogs = \App\Models\TokenLog::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30)) // Only last 30 days
            ->latest()
            ->limit(100)
            ->get();

        // Grouping Logic: Usage slips scanned within the same hour are combined
        $tokenLogs = $rawLogs->groupBy(function($log) {
            return $log->type . '_' . $log->created_at->format('Y-m-d-H');
        })->map(function($group) {
            $first = $group->first(); // Most recent in group
            $count = $group->count();
            
            return (object)[
                'type' => $first->type,
                'description' => ($first->type === 'usage' && $count > 1) 
                    ? "Scan Slips ({$count} items)" 
                    : ($first->description ?: 'Token Adjustment'),
                'delta' => $group->sum('delta'),
                'balance_after' => $first->balance_after,
                'created_at' => $first->created_at,
                'is_batch' => $count > 1,
                'count' => $count
            ];
        })->values()->take(15); // Show only top 15 grouped entries

        return view('profile.edit', [
            'user' => $user,
            'usageThisMonth' => abs((int) $usageThisMonth),
            'tokenLogs' => $tokenLogs,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        // Merge JSON Settings if present
        if (isset($validated['settings'])) {
            $user->settings = array_merge((array) $user->settings, $validated['settings']);
            unset($validated['settings']);
        }

        // Fill remaining properties (name, email)
        if (!empty($validated)) {
            $user->fill($validated);
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
