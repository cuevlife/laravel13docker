<?php

namespace App\Livewire;

use App\Models\Merchant;
use App\Support\WorkspaceUrl;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProjectHub extends Component
{
    use WithFileUploads;

    public $createOpen = false;
    public $deleteOpen = false;
    public $loading = false;
    public $deleteLoading = false;
    public $search = '';
    public $errorMessage = '';
    public $deleteErrorMessage = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        // No pagination yet, but good for future
    }

    public $name = '';
    public $logo;
    public $deleteId = null;
    public $deleteName = '';
    public $deleteConfirmation = '';

    public function openCreateModal()
    {
        $this->createOpen = true;
        $this->errorMessage = '';
        $this->name = '';
        $this->logo = null;
    }

    public function closeCreateModal()
    {
        if ($this->loading) return;
        $this->createOpen = false;
        $this->errorMessage = '';
    }

    public function openDeleteModal($id, $name)
    {
        $this->deleteOpen = true;
        $this->deleteErrorMessage = '';
        $this->deleteId = $id;
        $this->deleteName = $name;
        $this->deleteConfirmation = '';
    }

    public function closeDeleteModal()
    {
        if ($this->deleteLoading) return;
        $this->deleteOpen = false;
    }

    public function submitCreate()
    {
        $this->validate([
            'name' => 'required|max:255',
            'logo' => 'nullable|image|max:10240', // Max 10MB
        ]);

        $this->loading = true;
        $this->errorMessage = '';

        try {
            $user = Auth::user();
            $config = [];
            
            if ($this->logo) {
                // Store logo directly to storage/app/public/project-logos
                $logoPath = $this->logo->store('project-logos', 'public');
                $config['logo'] = $logoPath;
            }

            $project = Merchant::create([
                'user_id' => $user->id,
                'name' => trim($this->name),
                'status' => 'active',
                'config' => empty($config) ? null : $config,
            ]);

            // Link user to project
            $user->merchants()->attach($project->id, ['role' => 'owner']);

            $this->loading = false;
            $this->createOpen = false;

            return redirect()->to(WorkspaceUrl::workspace(request(), $project, 'dashboard'));
        } catch (\Exception $e) {
            $this->errorMessage = 'Unable to create project: ' . $e->getMessage();
            $this->loading = false;
        }
    }

    public function submitDelete()
    {
        if ($this->deleteConfirmation !== $this->deleteName) {
            $this->deleteErrorMessage = 'Project name does not match.';
            return;
        }

        $this->deleteLoading = true;

        try {
            $project = Merchant::findOrFail($this->deleteId);
            
            // Authorization check
            $user = Auth::user();
            $hasAccess = $user->isSuperAdmin() 
                || (int)$project->user_id === (int)$user->id 
                || $project->users()->where('users.id', $user->id)->wherePivot('role', 'owner')->exists();

            if (!$hasAccess) {
                abort(403);
            }

            $project->delete();

            $this->deleteLoading = false;
            $this->deleteOpen = false;
            
            $this->dispatch('notify', 'Project deleted successfully.');
        } catch (\Exception $e) {
            $this->deleteErrorMessage = 'Unable to delete project.';
            $this->deleteLoading = false;
        }
    }

    public function render()
    {
        $user = Auth::user();
        $query = $user->accessibleMerchants()
            ->with([
                'users' => fn ($query) => $query->where('users.id', $user->id),
            ]);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $stores = $query->get();

        return view('livewire.project-hub', [
            'stores' => $stores
        ]);
    }
}
