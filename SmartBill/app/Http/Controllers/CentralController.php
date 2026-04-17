<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CentralController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Show ALL active folders for everyone in the company
        $stores = Merchant::where('status', 'active')
            ->withCount('slips')
            ->get();

        return view('main.central-dashboard', compact('stores'));
    }

    public function openFolder(Request $request, Merchant $folder)
    {
        $user = Auth::user();

        $hasAccess = $user->isSuperAdmin()
            || $user->merchants()->where('merchant_id', $folder->id)->exists()
            || (int) $folder->user_id === (int) $user->id;

        abort_unless($hasAccess, 403);
        abort_if(!$folder->isActive() && !$user->isSuperAdmin(), 423, 'This workspace is archived and currently unavailable.');

        $request->session()->put('active_folder_id', $folder->id);

        $next = trim((string) $request->query('next', 'slips'), '/');

        $target = match ($next) {
            'templates' => route('workspace.templates.index', absolute: false),
            default => route('workspace.slip.index', absolute: false),
        };

        return redirect()->to($target);
    }
}