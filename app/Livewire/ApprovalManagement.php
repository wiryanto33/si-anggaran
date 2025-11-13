<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Approval;
use App\Models\Proposal;
use App\Models\User;

class ApprovalManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $approvalId;
    public $proposal_id;
    public $actor_id;
    public $aksi = 'diajukan';
    public $catatan;
    public $acted_at;
    public $isEditing = false;

    protected $rules = [
        'proposal_id' => 'required|exists:proposals,id',
        'actor_id' => 'nullable|exists:users,id',
        'aksi' => 'required|in:diajukan,diverifikasi,disetujui,ditolak,revisi',
        'catatan' => 'nullable|string',
        'acted_at' => 'nullable|date',
    ];

    private function isPlanner(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Perencana', 'perencana']);
    }

    public function create(): void
    {
        $this->authorize('approval.create');
        $this->resetForm();
        $this->actor_id = auth()->id();
        $this->aksi = 'diajukan';
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('approval.edit');
        $a = Approval::findOrFail($id);
        if ($this->isPlanner() && optional($a->proposal)->perencana_id !== auth()->id()) abort(403);
        $this->approvalId = $a->id;
        $this->proposal_id = $a->proposal_id;
        $this->actor_id = $a->actor_id;
        $this->aksi = $a->aksi;
        $this->catatan = $a->catatan;
        $this->acted_at = optional($a->acted_at)->format('Y-m-d H:i:s');
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->isEditing) {
            $this->authorize('approval.edit');
        } else {
            $this->authorize('approval.create');
        }

        if ($this->isPlanner()) {
            // Planner can only create approvals for own proposals
            if ($this->proposal_id) {
                $prop = Proposal::find($this->proposal_id);
                if (!$prop || $prop->perencana_id !== auth()->id()) abort(403);
            }
            $this->actor_id = auth()->id();
        }

        $this->validate();

        $payload = [
            'proposal_id' => $this->proposal_id,
            'actor_id' => $this->actor_id ?: auth()->id(),
            'aksi' => $this->aksi,
            'catatan' => $this->catatan,
            'acted_at' => $this->acted_at,
        ];

        if ($this->isEditing) {
            $a = Approval::findOrFail($this->approvalId);
            $a->update($payload);
        } else {
            Approval::create($payload);
        }

        $this->resetForm();
        $this->showModal = false;
        session()->flash('message', 'Approval saved successfully!');
    }

    public function delete(int $id): void
    {
        $this->authorize('approval.delete');
        $a = Approval::findOrFail($id);
        if ($this->isPlanner() && optional($a->proposal)->perencana_id !== auth()->id()) abort(403);
        $a->delete();
        session()->flash('message', 'Approval deleted successfully!');
    }

    public function closeModal(): void
    { $this->showModal = false; $this->resetForm(); }

    public function resetForm(): void
    {
        $this->approvalId = null; $this->proposal_id = null; $this->actor_id = null; $this->aksi = 'diajukan';
        $this->catatan = null; $this->acted_at = null; $this->isEditing = false; $this->resetValidation();
    }

    public function render()
    {
        $this->authorize('approval.view');

        $query = Approval::with(['proposal','actor'])->orderByDesc('acted_at')->orderByDesc('id');
        if ($this->isPlanner()) {
            $query->whereHas('proposal', fn($q)=>$q->where('perencana_id', auth()->id()));
        }

        return view('livewire.approval-management', [
            'approvals' => $query->paginate(12),
            'proposalOptions' => $this->isPlanner()
                ? Proposal::where('perencana_id', auth()->id())->orderByDesc('created_at')->get(['id','kode_usulan','judul'])
                : Proposal::orderByDesc('created_at')->get(['id','kode_usulan','judul']),
            'userOptions' => User::orderBy('name')->get(['id','name']),
            'aksiOptions' => ['diajukan','diverifikasi','disetujui','ditolak','revisi'],
        ]);
    }
}
