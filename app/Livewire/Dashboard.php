<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Proposal;
use App\Models\Pengumuman;

class Dashboard extends Component
{
    public $showPengumumanModal = false;
    public $selectedPengumuman = null; // array with fields

    public function render()
    {
        $stats = [];

        if (auth()->user()->can('user.view')) {
            $stats['users'] = User::count();
        }

        if (auth()->user()->can('proposal.view')) {
            $stats['proposals_total'] = Proposal::count();
        }

        $pengumuman = Pengumuman::query()
            ->where('aktif', true)
            ->where(function($q){
                $q->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            })
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.dashboard', [
            'stats' => $stats,
            'pengumuman' => $pengumuman,
        ]);
    }

    public function openPengumuman($id): void
    {
        $p = Pengumuman::query()
            ->where('aktif', true)
            ->where(function($q){
                $q->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            })
            ->findOrFail($id);

        $this->selectedPengumuman = [
            'id' => $p->id,
            'judul' => $p->judul,
            'deskripsi' => $p->deskripsi,
            'file' => $p->file,
            'publish_at' => optional($p->publish_at)->toDateTimeString(),
            'created_at' => $p->created_at->toDateTimeString(),
        ];
        $this->showPengumumanModal = true;
    }

    public function closePengumuman(): void
    {
        $this->showPengumumanModal = false;
        $this->selectedPengumuman = null;
    }
}
