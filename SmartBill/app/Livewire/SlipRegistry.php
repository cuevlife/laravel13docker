<?php

namespace App\Livewire;

use App\Models\Merchant;
use App\Models\Slip;
use App\Models\SlipBatch;
use App\Models\SlipTemplate;
use App\Models\TokenLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class SlipRegistry extends Component
{
    use WithPagination, WithFileUploads;

    // Filters
    public $search = '';
    public $batch_id = '';
    public $workflow_status = '';
    public $date_from = '';
    public $date_to = '';
    public $template_id = '';
    public $label = '';
    public $sort = 'latest';
    public $sortField = 'processed_at';
    public $sortDirection = 'desc';
    public $archive_scope = 'active';

    // State
    public $batchModalOpen = false;
    public $collectionModalOpen = false;
    public $scanModalOpen = false;
    public $detailOpen = false;
    
    // Form Data
    public $batchForm = ['name' => '', 'note' => ''];
    public $collectionForm = ['id' => null, 'name' => '', 'note' => ''];
    
    // Rename to be more specific and initialize as empty array
    public $fileQueue = []; 
    
    public $scanForm = [
        'template_id' => 'auto',
        'batch_id' => '',
        'batch_name' => '',
        'labels' => '',
        'custom_instruction' => ''
    ];

    public $activeSlip = null;
    public $isScanning = false;
    public $scanStatus = 'Idle';
    public $currentScanCount = 0;
    public $totalScanCount = 0;
    public $duplicateCount = 0;
    public $reprocess_instruction = '';

    // Bulk Actions
    public $selectedIds = [];
    public $bulkAction = 'mark_reviewed';

    protected $listeners = [
        'gentle-open-scan' => 'openScanModal',
        'refresh-registry' => '$refresh'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'batch_id' => ['except' => ''],
        'workflow_status' => ['except' => ''],
        'template_id' => ['except' => ''],
        'sort' => ['except' => 'latest'],
        'sortField' => ['except' => 'processed_at'],
        'sortDirection' => ['except' => 'desc'],
        'archive_scope' => ['except' => 'active'],
    ];

    public function mount()
    {
        // archive_scope will be handled by queryString or defaults to 'active'
    }

    private function getTenant()
    {
        $projectId = session('active_project_id');
        if (!$projectId) {
            abort(403, 'No active project selected.');
        }
        return Merchant::findOrFail($projectId);
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingBatchId() { $this->resetPage(); }
    public function updatingWorkflowStatus() { $this->resetPage(); }
    public function updatingArchiveScope() { $this->resetPage(); }

    public function setArchiveScope($scope)
    {
        $this->archive_scope = $scope;
        $this->resetPage();
    }

    public function toggleAll($ids)
    {
        if (count($this->selectedIds) === count($ids)) {
            $this->selectedIds = [];
        } else {
            $this->selectedIds = $ids;
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteSlip($id)
    {
        $slip = Slip::findOrFail($id);
        if (Storage::disk('public')->exists($slip->image_path)) {
            Storage::disk('public')->delete($slip->image_path);
        }
        $slip->delete();
        $this->dispatch('notify', 'Slip deleted successfully.');
    }

    public function openScanModal()
    {
        $this->scanModalOpen = true;
        $this->fileQueue = []; // Reset queue
        $this->scanForm['template_id'] = 'auto';
        $this->dispatch('lucide:refresh');
    }

    public function reprocessSlip($id)
    {
        try {
            $slip = Slip::findOrFail($id);
            $tenant = $this->getTenant();
            $user = Auth::user();
            $fullPath = Storage::disk('public')->path($slip->image_path);

            $this->dispatch('notify', ['type' => 'info', 'title' => 'Reprocessing', 'message' => 'Starting AI re-extraction...']);

            $template = null;
            $dynamicFields = null;
            $dynamicInstruction = null;

            if ($this->scanForm['template_id'] === 'auto') {
                $geminiService = app(\App\Services\GeminiService::class);
                $presetKeys = array_keys(\App\Support\IntelligencePresets::all());
                $detectedType = $geminiService->identifyStoreFromImage($fullPath, $presetKeys) ?: 'retail';
                if (!in_array($detectedType, $presetKeys)) $detectedType = 'retail';
                $preset = \App\Support\IntelligencePresets::all()[$detectedType];
                $dynamicFields = $preset['ai_fields'];
                $dynamicInstruction = $preset['main_instruction'];
                
                $template = SlipTemplate::firstOrCreate(
                    ['merchant_id' => $tenant->id, 'name' => "Auto: {$preset['name']}"],
                    ['user_id' => $user->id, 'main_instruction' => $dynamicInstruction, 'ai_fields' => $dynamicFields]
                );
            } else {
                $template = SlipTemplate::where('merchant_id', $tenant->id)->findOrFail($this->scanForm['template_id']);
                $dynamicFields = $template->ai_fields;
                $dynamicInstruction = $template->main_instruction;
            }

            $geminiService = app(\App\Services\GeminiService::class);
            $extractedData = $geminiService->extractDataFromImage($fullPath, [
                'ai_fields' => $dynamicFields,
                'main_instruction' => ($dynamicInstruction ?? '') . ". " . ($this->reprocess_instruction ?: $this->scanForm['custom_instruction'] ?? ''),
            ]);

            $this->reprocess_instruction = ''; // Clear after use
            $slip->update([
                'slip_template_id' => $template->id,
                'extracted_data' => $extractedData,
                'processed_at' => now(),
            ]);

            $this->dispatch('notify', 'Slip reprocessed successfully.');
            $this->dispatch('lucide:refresh');
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Reprocess Error', 'message' => $e->getMessage()]);
        }
    }

    public function toggleArchive($id, $archive = true)
    {
        $slip = Slip::findOrFail($id);
        if ($archive) {
            $slip->update([
                'workflow_status' => Slip::WORKFLOW_ARCHIVED,
                'archived_at' => now(),
            ]);
            $this->dispatch('notify', 'Slip moved to archive.');
        } else {
            $slip->update([
                'workflow_status' => Slip::WORKFLOW_REVIEWED,
                'archived_at' => null,
            ]);
            $this->dispatch('notify', 'Slip restored to inbox.');
        }
        $this->dispatch('lucide:refresh');
        $this->resetPage();
    }

    public function closeScanModal()
    {
        if ($this->isScanning) return;
        $this->scanModalOpen = false;
        $this->fileQueue = [];
    }

    public function removeFromQueue($index)
    {
        if (isset($this->fileQueue[$index])) {
            unset($this->fileQueue[$index]);
            // Re-index array to prevent Property [$0] issues
            $this->fileQueue = array_values($this->fileQueue);
        }
    }

    public function submitScan()
    {
        $this->validate([
            'fileQueue' => 'required|array|min:1',
            'fileQueue.*' => 'image|max:10240',
            'scanForm.template_id' => 'required',
        ]);

        $this->isScanning = true;
        $this->totalScanCount = count($this->fileQueue);
        $this->currentScanCount = 0;
        $this->duplicateCount = 0;

        $this->dispatch('trigger-next-scan', index: 0);
    }

    public function processSingleReceipt($index)
    {
        try {
            $user = Auth::user();
            $tenant = $this->getTenant();
            
            // Check if file still exists in queue
            if (!isset($this->fileQueue[$index])) {
                $this->finishScanning();
                return;
            }

            $image = $this->fileQueue[$index];
            $this->currentScanCount = $index + 1;
            $this->scanStatus = "Scanning {$this->currentScanCount} of {$this->totalScanCount}...";

            $tempPath = $image->getRealPath();
            $imageHash = hash_file('sha256', $tempPath);

            $isDuplicate = Slip::where('image_hash', $imageHash)
                ->whereHas('template', fn($q) => $q->where('merchant_id', $tenant->id))
                ->exists();

            if (!$isDuplicate) {
                $path = $image->store('slips', 'public');
                $fullPath = Storage::disk('public')->path($path);
                $batch = $this->resolveBatch($tenant, $user);
                
                $template = null;
                $dynamicFields = null;
                $dynamicInstruction = null;

                if ($this->scanForm['template_id'] === 'auto') {
                    // Start with Intelligence Profile Detection
                    $geminiService = app(\App\Services\GeminiService::class);
                    $presetKeys = array_keys(\App\Support\IntelligencePresets::all());
                    $detectedType = $geminiService->identifyStoreFromImage($fullPath, $presetKeys) ?: 'retail';
                    
                    // Fallback to retail if unknown
                    if (!in_array($detectedType, $presetKeys)) $detectedType = 'retail';
                    
                    $preset = \App\Support\IntelligencePresets::all()[$detectedType];
                    $dynamicFields = $preset['ai_fields'];
                    $dynamicInstruction = $preset['main_instruction'];
                    
                    // Assign to a generic template for this tenant or create one
                    $template = SlipTemplate::firstOrCreate(
                        ['merchant_id' => $tenant->id, 'name' => "Auto: {$preset['name']}"],
                        [
                            'user_id' => $user->id,
                            'main_instruction' => $dynamicInstruction,
                            'ai_fields' => $dynamicFields
                        ]
                    );
                } else {
                    $template = SlipTemplate::where('merchant_id', $tenant->id)->findOrFail($this->scanForm['template_id']);
                    $dynamicFields = $template->ai_fields;
                    $dynamicInstruction = $template->main_instruction;
                }

                $geminiService = app(\App\Services\GeminiService::class);
                $extractedData = $geminiService->extractDataFromImage($fullPath, [
                    'ai_fields' => $dynamicFields,
                    'main_instruction' => ($dynamicInstruction ?? '') . ". " . ($this->scanForm['custom_instruction'] ?? ''),
                ]);

                $uid = 'SB-' . now()->format('ym') . '-' . strtoupper(\Illuminate\Support\Str::random(5));

                Slip::create([
                    'uid' => $uid,
                    'user_id' => $user->id,
                    'slip_template_id' => $template->id,
                    'slip_batch_id' => $batch->id,
                    'image_path' => $path,
                    'image_hash' => $imageHash,
                    'extracted_data' => $extractedData,
                    'workflow_status' => Slip::WORKFLOW_REVIEWED,
                    'processed_at' => now(),
                    'reviewed_at' => now(),
                    'labels' => $this->normalizeLabels($this->scanForm['labels']),
                ]);

                $user->decrement('tokens', 1);
                $this->dispatch('token-updated');
            } else {
                $this->duplicateCount++;
            }

            if ($index + 1 < $this->totalScanCount) {
                $this->dispatch('trigger-next-scan', index: $index + 1);
            } else {
                $this->finishScanning();
            }

        } catch (\Exception $e) {
            $this->isScanning = false;
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Scan Error', 'message' => $e->getMessage()]);
        }
    }

    private function finishScanning()
    {
        $successCount = $this->totalScanCount - $this->duplicateCount;
        $message = "Processed {$this->totalScanCount} images: {$successCount} successful";
        if ($this->duplicateCount > 0) $message .= ", {$this->duplicateCount} duplicates skipped";
        $message .= ".";

        $this->isScanning = false;
        $this->scanModalOpen = false;
        $this->fileQueue = [];
        $this->scanForm['template_id'] = 'auto';
        
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Batch Complete', 'message' => $message]);
        $this->dispatch('lucide:refresh');
    }

    private function detectTemplate($tenantId, $filePath)
    {
        $template = SlipTemplate::where('merchant_id', $tenantId)->first();
        if (!$template) {
            $template = SlipTemplate::create([
                'merchant_id' => $tenantId,
                'user_id' => Auth::id(),
                'name' => 'Default Profile',
                'main_instruction' => 'Extract all visible data from this receipt image.',
                'ai_fields' => [
                    ['name' => 'shop_name', 'type' => 'string', 'description' => 'Name of the store'],
                    ['name' => 'date', 'type' => 'string', 'description' => 'Transaction date'],
                    ['name' => 'total_amount', 'type' => 'number', 'description' => 'Final total amount paid'],
                ],
            ]);
        }
        return $template;
    }

    private function resolveBatch($tenant, $user)
    {
        if ($this->scanForm['batch_id']) {
            return SlipBatch::where('merchant_id', $tenant->id)->findOrFail($this->scanForm['batch_id']);
        }
        $batchName = trim($this->scanForm['batch_name']);
        if ($batchName) {
            return SlipBatch::firstOrCreate(
                ['merchant_id' => $tenant->id, 'name' => $batchName],
                ['created_by' => $user->id, 'scanned_at' => now(), 'status' => 'open']
            );
        }
        return SlipBatch::firstOrCreate(
            ['merchant_id' => $tenant->id, 'name' => 'Inbox ' . now()->format('Y-m-d')],
            ['created_by' => $user->id, 'scanned_at' => now(), 'status' => 'open']
        );
    }

    private function normalizeLabels($labels)
    {
        if (!$labels) return [];
        return collect(explode(',', $labels))->map(fn($l) => trim($l))->filter()->unique()->values()->all();
    }

    public function openBatchModal()
    {
        $this->batchModalOpen = true;
        $this->batchForm = ['name' => '', 'note' => ''];
    }

    public function closeBatchModal()
    {
        $this->batchModalOpen = false;
    }

    public function submitBatch()
    {
        $this->validate(['batchForm.name' => 'required|string|max:255']);
        $tenant = $this->getTenant();
        SlipBatch::create([
            'merchant_id' => $tenant->id,
            'name' => trim($this->batchForm['name']),
            'created_by' => auth()->id(),
            'note' => $this->batchForm['note'] ?? null,
            'scanned_at' => now(),
            'status' => 'open',
        ]);
        $this->batchModalOpen = false;
        $this->dispatch('notify', 'Collection created successfully.');
    }

    public function openSlipDetail($uid)
    {
        $slip = Slip::with(['template.merchant', 'batch'])->where('uid', $uid)->firstOrFail();
        $this->activeSlip = $this->decorateSlipForDisplay($slip);
        $this->detailOpen = true;
        $this->dispatch('lucide:refresh');
    }

    public function setBulkAction($action)
    {
        $this->bulkAction = $action;
        $this->applyBulkAction();
    }

    public function applyBulkAction()
    {
        if (empty($this->selectedIds)) return;
        $slips = Slip::whereIn('id', $this->selectedIds)->get();
        foreach ($slips as $slip) {
            switch ($this->bulkAction) {
                case 'mark_reviewed': $slip->update(['workflow_status' => Slip::WORKFLOW_REVIEWED, 'reviewed_at' => now()]); break;
                case 'mark_approved': $slip->update(['workflow_status' => Slip::WORKFLOW_APPROVED, 'approved_at' => now()]); break;
                case 'archive': $slip->update(['workflow_status' => Slip::WORKFLOW_ARCHIVED, 'archived_at' => now()]); break;
                case 'restore': $slip->update(['workflow_status' => Slip::WORKFLOW_REVIEWED, 'archived_at' => null]); break;
            }
        }

        if ($this->bulkAction === 'export') {
            $ids = implode(',', $this->selectedIds);
            $this->selectedIds = [];
            $exportUrl = \App\Support\WorkspaceUrl::current(request(), "slips/export?ids={$ids}");
            return redirect()->to($exportUrl);
        }

        $this->selectedIds = [];
        $this->dispatch('notify', 'Bulk action applied successfully.');
    }

    private function decorateSlipForDisplay(Slip $slip)
    {
        $data = $slip->extracted_data ?: [];
        $slip->display_shop = $data['shop_name'] ?? $data['store_name'] ?? $slip->template?->merchant?->name ?? 'Unknown';
        $slip->display_date = $data['date'] ?? $data['transaction_date'] ?? optional($slip->processed_at)->format('d/m/Y');
        $slip->display_amount = $data['total_amount'] ?? $data['total'] ?? $data['final_total'] ?? 0;
        $fields = [];
        foreach ($data as $key => $value) {
            if ($key === '__metadata') continue;
            
            $label = str_replace('_', ' ', \Illuminate\Support\Str::headline($key));
            $formattedValue = $value;

            if (is_array($value)) {
                // If it's the 'items' array, format it as a clean list
                if ($key === 'items') {
                    $formattedValue = collect($value)->map(function($item) {
                        if (is_array($item)) {
                            return implode(' | ', array_filter($item));
                        }
                        return (string)$item;
                    })->implode("\n");
                } else {
                    $formattedValue = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
            }

            $fields[] = [
                'label' => $label,
                'value' => (string)$formattedValue
            ];
        }
        $slip->display_fields = $fields;
        return $slip;
    }

    public function render()
    {
        $tenant = $this->getTenant();
        $query = Slip::query()->with(['template.merchant', 'batch'])
            ->whereHas('template', fn($q) => $q->where('merchant_id', $tenant->id));
        if ($this->search) {
            $query->where(function($q) {
                $q->where('uid', 'like', '%'.$this->search.'%')
                  ->orWhere('image_path', 'like', '%'.$this->search.'%')
                  ->orWhereHas('template', fn($qt) => $qt->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhereHas('batch', fn($qb) => $qb->where('name', 'like', '%'.$this->search.'%'));
            });
        }
        if ($this->batch_id) $query->where('slip_batch_id', $this->batch_id);
        if ($this->workflow_status) $query->where('workflow_status', $this->workflow_status);
        if ($this->template_id) $query->where('slip_template_id', $this->template_id);
        if ($this->archive_scope === 'archived') $query->whereNotNull('archived_at');
        else $query->whereNull('archived_at');
        
        $query->orderBy($this->sortField, $this->sortDirection)->orderBy('id', 'desc');
        
        $slips = $query->paginate(50);
        $slips->getCollection()->transform(fn($slip) => $this->decorateSlipForDisplay($slip));
        return view('livewire.slip-registry', [
            'slips' => $slips,
            'batches' => SlipBatch::where('merchant_id', $tenant->id)->latest('scanned_at')->get(),
            'templates' => SlipTemplate::where('merchant_id', $tenant->id)->orderBy('name')->get(),
            'workflowOptions' => Slip::workflowOptions(),
        ]);
    }
}
