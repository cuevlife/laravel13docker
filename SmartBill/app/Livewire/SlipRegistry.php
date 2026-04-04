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
    public $workflow_status = '';
    public $date_from = '';
    public $date_to = '';
    public $template_id = '';
    public $label = '';
    public $sort = 'latest';
    public $sortField = 'processed_at';
    public $sortDirection = 'desc';

    // State
    public $scanModalOpen = false;
    public $detailOpen = false;
    
    // Rename to be more specific and initialize as empty array
    public $fileQueue = []; 
    public $uploadTemp = []; // For initial catch
    
    public $scanForm = [
        'template_id' => 'auto',
        'labels' => '',
    ];

    public $activeSlip = null;
    public $isScanning = false;
    public $scanStatus = 'Idle';
    public $currentScanCount = 0;
    public $totalScanCount = 0;
    public $duplicateCount = 0;

    // Bulk Actions
    public $selectedIds = [];
    public $bulkAction = 'mark_reviewed';

    protected $listeners = [
        'gentle-open-scan' => 'openScanModal',
        'refresh-registry' => '$refresh'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'workflow_status' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'template_id' => ['except' => ''],
        'sort' => ['except' => 'latest'],
        'sortField' => ['except' => 'processed_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
    }

    private function getTenant()
    {
        $projectId = session('active_project_id');
        if (!$projectId) {
            abort(403, 'No active project selected.');
        }
        return Merchant::findOrFail($projectId);
    }

    public function updatedUploadTemp()
    {
        $this->validate([
            'uploadTemp.*' => 'image|max:10240',
        ]);

        if (!Storage::disk('public')->exists('livewire-tmp')) {
            Storage::disk('public')->makeDirectory('livewire-tmp');
        }

        foreach ($this->uploadTemp as $file) {
            $filename = time() . '-' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('livewire-tmp', $filename, 'public');
            $this->fileQueue[] = $filename;
        }

        $this->uploadTemp = [];
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingWorkflowStatus() { $this->resetPage(); }
    public function updatingDateFrom() { $this->resetPage(); }
    public function updatingDateTo() { $this->resetPage(); }

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
        $this->clearFileQueue();
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
                'main_instruction' => $dynamicInstruction ?? '',
            ]);

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

    public function closeScanModal()
    {
        if ($this->isScanning) return;
        $this->scanModalOpen = false;
        $this->clearFileQueue();
    }

    public function removeFromQueue($index)
    {
        if (isset($this->fileQueue[$index])) {
            $filename = $this->fileQueue[$index];
            if (Storage::disk('public')->exists('livewire-tmp/' . $filename)) {
                Storage::disk('public')->delete('livewire-tmp/' . $filename);
            }
            unset($this->fileQueue[$index]);
            $this->fileQueue = array_values($this->fileQueue);
        }
    }

    public function clearFileQueue()
    {
        foreach ($this->fileQueue as $filename) {
            if (Storage::disk('public')->exists('livewire-tmp/' . $filename)) {
                Storage::disk('public')->delete('livewire-tmp/' . $filename);
            }
        }
        $this->fileQueue = [];
    }

    public function submitScan()
    {
        $this->validate([
            'fileQueue' => 'required|array|min:1',
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
            
            if (!isset($this->fileQueue[$index])) {
                $this->finishScanning();
                return;
            }

            $filename = $this->fileQueue[$index];
            $this->currentScanCount = $index + 1;
            $this->scanStatus = "Scanning {$this->currentScanCount} of {$this->totalScanCount}...";

            $relPath = 'livewire-tmp/' . $filename;
            if (!Storage::disk('public')->exists($relPath)) {
                $this->finishScanning();
                return;
            }

            $tempFullPath = Storage::disk('public')->path($relPath);
            $imageHash = hash_file('sha256', $tempFullPath);

            $isDuplicate = Slip::where('image_hash', $imageHash)
                ->whereHas('template', fn($q) => $q->where('merchant_id', $tenant->id))
                ->exists();

            if (!$isDuplicate) {
                $permanentPath = 'slips/' . $filename;
                Storage::disk('public')->move($relPath, $permanentPath);
                $fullPath = Storage::disk('public')->path($permanentPath);

                $batch = $this->resolveBatch($tenant, $user);
                
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
                    'main_instruction' => $dynamicInstruction ?? '',
                ]);

                $uid = 'SB-' . now()->format('ym') . '-' . strtoupper(\Illuminate\Support\Str::random(5));

                Slip::create([
                    'uid' => $uid,
                    'user_id' => $user->id,
                    'slip_template_id' => $template->id,
                    'slip_batch_id' => $batch->id,
                    'image_path' => $permanentPath,
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
                // If duplicate, still cleanup the temp file
                Storage::disk('public')->delete($relPath);
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
        $this->clearFileQueue();
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
        return SlipBatch::firstOrCreate(
            ['merchant_id' => $tenant->id, 'name' => 'Main Inbox'],
            ['created_by' => $user->id, 'scanned_at' => now(), 'status' => 'open']
        );
    }

    private function normalizeLabels($labels)
    {
        if (!$labels) return [];
        return collect(explode(',', $labels))->map(fn($l) => trim($l))->filter()->unique()->values()->all();
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
        
        if ($this->bulkAction === 'export') {
            foreach ($slips as $slip) {
                $slip->update(['workflow_status' => Slip::WORKFLOW_EXPORTED, 'exported_at' => now()]);
            }
            
            $ids = implode(',', $this->selectedIds);
            $exportUrl = \App\Support\WorkspaceUrl::current(request(), "slips/export?ids={$ids}");
            
            $this->selectedIds = []; // Clear selection
            $this->dispatch('trigger-download', url: $exportUrl);
            $this->dispatch('notify', 'กำลังเตรียมไฟล์ Excel และปรับสถานะเป็นส่งออกแล้ว');
            return;
        }

        foreach ($slips as $slip) {
            switch ($this->bulkAction) {
                case 'mark_reviewed': $slip->update(['workflow_status' => Slip::WORKFLOW_REVIEWED, 'reviewed_at' => now()]); break;
                case 'mark_approved': $slip->update(['workflow_status' => Slip::WORKFLOW_APPROVED, 'approved_at' => now()]); break;
            }
        }

        $this->selectedIds = [];
        $this->dispatch('notify', 'ดำเนินการสำเร็จเรียบร้อยแล้ว');
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
            ->whereHas('template', fn($q) => $q->where('merchant_id', $tenant->id))
            ->whereNull('archived_at');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('uid', 'like', '%'.$this->search.'%')
                  ->orWhere('image_path', 'like', '%'.$this->search.'%')
                  ->orWhereHas('template', fn($qt) => $qt->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhereHas('batch', fn($qb) => $qb->where('name', 'like', '%'.$this->search.'%'));
            });
        }

        if ($this->workflow_status) $query->where('workflow_status', $this->workflow_status);
        if ($this->template_id) $query->where('slip_template_id', $this->template_id);
        
        if ($this->date_from) $query->whereDate('processed_at', '>=', $this->date_from);
        if ($this->date_to) $query->whereDate('processed_at', '<=', $this->date_to);
        
        $query->orderBy($this->sortField, $this->sortDirection)->orderBy('id', 'desc');
        
        $slips = $query->paginate(50);
        $slips->getCollection()->transform(fn($slip) => $this->decorateSlipForDisplay($slip));
        return view('livewire.slip-registry', [
            'slips' => $slips,
            'templates' => SlipTemplate::where('merchant_id', $tenant->id)->orderBy('name')->get(),
            'workflowOptions' => Slip::workflowOptions(),
        ]);
    }
}
