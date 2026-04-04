<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TokenBalance extends Component
{
    public $tokens;

    protected $listeners = [
        'token-updated' => 'refreshBalance',
        'refresh-tokens' => 'refreshBalance'
    ];

    public function mount()
    {
        $this->tokens = Auth::user()->tokens;
    }

    public function refreshBalance()
    {
        Auth::user()->refresh();
        $this->tokens = Auth::user()->tokens;
    }

    public function render()
    {
        return <<<'BLADE'
            <div class="flex items-center gap-2 px-3 py-1.5 bg-[#f2f3f5] dark:bg-[#2b2d31] rounded-full border border-[#e3e5e8] dark:border-[#313338] shadow-sm">
                <i data-lucide="coins" class="w-4 h-4 text-amber-500"></i>
                <span class="text-[11px] font-black tracking-widest text-[#1e1f22] dark:text-[#f2f3f5]">{{ number_format($tokens) }}</span>
            </div>
        BLADE;
    }
}
