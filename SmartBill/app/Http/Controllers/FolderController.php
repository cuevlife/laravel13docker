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

        // Show 'All' folders only if user is Super Admin and requested it
        $showAll = $request->boolean('all', false) && $user->isSuperAdmin();

        if ($showAll) {
            $query = Merchant::query()
                ->where(fn($q) => $q->where('status', 'active')->orWhereNull('status'))
                ->withCount('slips')
                ->latest();
        } else {
            // Default: Show ONLY folders owned by the user (even for Super Admin)
            $query = $user->merchants()
                ->where(fn($q) => $q->where('status', 'active')->orWhereNull('status'))
                ->withCount('slips')
                ->latest();
        }

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
                    'logo_url' => $s->logo_url,
                    'is_owner' => (int)$s->user_id === (int)$user->id || $user->isSuperAdmin(),
                    'open_url' => \App\Support\WorkspaceUrl::workspace($request, $s, 'dashboard'),
                    'slips_count' => (int) $s->slips_count,
                    'max_slips' => (int) ($s->max_slips ?? 10000),
                ]),
                'meta' => [
                    'owned_folders_count' => Merchant::where('user_id', $user->id)->count(),
                    'max_folders' => (int) ($user->max_folders ?? 3),
                    'is_super_admin' => $user->isSuperAdmin(),
                ]
            ]);
        }

        $user = Auth::user();
        $initialData = [
            'status' => 'success',
            'data' => $stores->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'logo_url' => $s->logo_url,
                'is_owner' => (int)$s->user_id === (int)$user->id || $user->isSuperAdmin(),
                'open_url' => \App\Support\WorkspaceUrl::workspace($request, $s, 'dashboard'),
                'slips_count' => (int) $s->slips_count,
                'max_slips' => (int) ($s->max_slips ?? 10000),
            ]),
            'meta' => [
                'owned_folders_count' => Merchant::where('user_id', $user->id)->count(),
                'max_folders' => (int) ($user->max_folders ?? 3),
                'is_super_admin' => $user->isSuperAdmin(),
            ]
        ];
        return view('main.central-dashboard', compact('stores', 'initialData'));
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
            'logo' => 'nullable|image|max:2048',
        ], [
            'name.unique' => __('Folder name exists')
        ]);

        try {
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
            }

                        $defaultFields = \App\Models\SystemConfig::where('config_key', 'default_fields')->value('config_value');
            $decodedDefaults = json_decode($defaultFields, true) ?: [];
            
            $folder = Merchant::create([
                'user_id' => $user->id,
                'name' => trim($data['name']),
                'logo_path' => $logoPath,
                'status' => 'active',
                'config' => [
                    'logo_path' => $logoPath,
                    'ai_fields' => $decodedDefaults // ใส่ฟิลด์มาตรฐานให้ทันที
                ]
            ]);

            return response()->json([
                'status' => 'success',
                'redirect' => WorkspaceUrl::workspace($request, $folder, 'dashboard')
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $folder = Merchant::findOrFail($id);
        $user = Auth::user();

        // Check access
        if (!$user->isSuperAdmin() && (int)$folder->user_id !== (int)$user->id) {
            abort(403);
        }

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique for this user (except current folder)
                \Illuminate\Validation\Rule::unique('merchants', 'name')
                    ->where(fn($q) => $q->where('user_id', $folder->user_id))
                    ->ignore($folder->id)
            ],
            'logo' => 'nullable|image|max:2048',
        ], [
            'name.unique' => __('Folder name exists')
        ]);

        try {
            $logoPath = $folder->logo_path;
            if ($request->hasFile('logo')) {
                // Delete old logo
                if ($logoPath) {
                    Storage::disk('public')->delete($logoPath);
                }
                $logoPath = $request->file('logo')->store('logos', 'public');
            }

            $config = $folder->config ?: [];
            $config['logo_path'] = $logoPath;

            $folder->update([
                'name' => trim($data['name']),
                'logo_path' => $logoPath,
                'config' => $config
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('Folder updated successfully')
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

        $hasAccess = $user->isSuperAdmin() || (int)$folder->user_id === (int)$user->id;

        if (!$hasAccess) abort(403);

        // Delete logo if exists
        if ($folder->logo_path) {
            Storage::disk('public')->delete($folder->logo_path);
        }

        $folder->delete();

        return response()->json(['status' => 'success', 'message' => 'Folder deleted successfully.']);
    }

    public function openFolder(Request $request, Merchant $folder)
    {
        $request->session()->put('active_folder_id', $folder->id);
        return redirect()->route('workspace.dashboard');
    }

    public function getTokenBalance()
    {
        return response()->json(['balance' => Auth::user()->tokens ?? 0]);
    }
}
