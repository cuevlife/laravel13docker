<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Support\WorkspaceUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FolderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Direct check for role ID to be absolutely sure
        if ((int) $user->role === 9 || $user->isSuperAdmin()) {
            return redirect()->to(\App\Support\OwnerUrl::path($request, 'users'));
        }

        // Show only folders where the user has explicit access (Owned or Member)
        $query = $user->accessibleMerchants()
            ->with([
                'users' => fn ($query) => $query->where('users.id', $user->id),
            ])
            ->withCount('slips');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $stores = $query->get();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $stores->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'logo_url' => isset($s->config['logo']) ? asset('storage/' . $s->config['logo']) : null,
                    'role' => $s->users->first()->pivot->role ?? 'Member',
                    'is_owner' => (int)$s->user_id === (int)$user->id || $user->isSuperAdmin(),
                    'open_url' => \App\Support\WorkspaceUrl::workspace($request, $s, 'dashboard'),
                    'slips_count' => (int) $s->slips_count,
                    'max_slips' => (int) ($s->max_slips ?? 10000),
                ]),
                'meta' => [
                    'owned_folders_count' => Merchant::where('user_id', $user->id)->count(),
                    'max_folders' => (int) ($user->max_folders ?? 3),
                ]
            ]);
        }

        return view('main.central-dashboard', compact('stores'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Check Folder Creation Limit (User level)
        $ownedFoldersCount = Merchant::where('user_id', $user->id)->count();
        if ($ownedFoldersCount >= ($user->max_folders ?? 3)) {
            return response()->json([
                'status' => 'error',
                'message' => __('Folder creation limit reached. Maximum :max folders allowed.', ['max' => $user->max_folders ?? 3]),
            ], 403);
        }
        
        $data = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255',
                // Unique for this user
                \Illuminate\Validation\Rule::unique('merchants', 'name')->where(fn($q) => $q->where('user_id', $user->id))
            ],
            'logo' => 'nullable|image|max:10240',
        ], [
            'name.unique' => __('Folder name exists')
        ]);

        try {
            $config = [];

            if ($request->hasFile('logo')) {
                \App\Support\ImageOptimizer::optimizeUpload($request->file('logo'), 400, 400, 85);
                $logoPath = $request->file('logo')->store('folder-logos', 'public');
                $config['logo'] = $logoPath;
            }

            // Generate a unique subdomain based on the name
            $subdomain = Str::slug($data['name']);
            
            // Fallback for Thai names (slug will be empty)
            if (empty($subdomain)) {
                $subdomain = 'folder-' . Str::lower(Str::random(5));
            }

            $baseSubdomain = $subdomain;
            $counter = 1;
            while (Merchant::where('subdomain', $subdomain)->exists()) {
                $subdomain = $baseSubdomain . '-' . $counter;
                $counter++;
            }

            $folder = Merchant::create([
                'user_id' => $user->id,
                'name' => trim($data['name']),
                'subdomain' => $subdomain,
                'status' => 'active',
                'config' => empty($config) ? null : $config,
            ]);

            $user->merchants()->attach($folder->id, ['role' => 'owner']);

            return response()->json([
                'status' => 'success',
                'redirect' => WorkspaceUrl::workspace($request, $folder, 'dashboard')
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $folder = Merchant::findOrFail($id);
        $user = Auth::user();

        if ($request->confirmation !== $folder->name) {
            return response()->json(['status' => 'error', 'message' => 'Folder name does not match.'], 422);
        }

        $hasAccess = $user->isSuperAdmin() 
            || (int)$folder->user_id === (int)$user->id 
            || $folder->users()->where('users.id', $user->id)->wherePivot('role', 'owner')->exists();

        if (!$hasAccess) abort(403);

        $folder->delete();

        return response()->json(['status' => 'success', 'message' => 'Folder deleted successfully.']);
    }

    public function getTokenBalance()
    {
        return response()->json(['balance' => Auth::user()->tokens ?? 0]);
    }
}
