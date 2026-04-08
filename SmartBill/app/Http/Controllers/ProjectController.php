<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Support\WorkspaceUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
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

        $query = $user->accessibleMerchants()
            ->with([
                'users' => fn ($query) => $query->where('users.id', $user->id),
            ]);

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
                ])
            ]);
        }

        return view('main.central-dashboard', compact('stores'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
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
                $logoPath = $request->file('logo')->store('project-logos', 'public');
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

            $project = Merchant::create([
                'user_id' => $user->id,
                'name' => trim($data['name']),
                'subdomain' => $subdomain,
                'status' => 'active',
                'config' => empty($config) ? null : $config,
            ]);

            $user->merchants()->attach($project->id, ['role' => 'owner']);

            return response()->json([
                'status' => 'success',
                'redirect' => WorkspaceUrl::workspace($request, $project, 'dashboard')
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $project = Merchant::findOrFail($id);
        $user = Auth::user();

        if ($request->confirmation !== $project->name) {
            return response()->json(['status' => 'error', 'message' => 'Project name does not match.'], 422);
        }

        $hasAccess = $user->isSuperAdmin() 
            || (int)$project->user_id === (int)$user->id 
            || $project->users()->where('users.id', $user->id)->wherePivot('role', 'owner')->exists();

        if (!$hasAccess) abort(403);

        $project->delete();

        return response()->json(['status' => 'success', 'message' => 'Project deleted successfully.']);
    }

    public function getTokenBalance()
    {
        return response()->json(['balance' => Auth::user()->tokens ?? 0]);
    }
}
