<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Proposal;
use App\Models\Satuan;
use App\Models\User;
use App\Models\ProposalItem;

class ProposalManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    // Listing state
    public $search = '';
    public $perPage = 10;
    public $selected = [];
    public $proposalId;
    public $satuan_id;
    public $perencana_id;
    public $kode_usulan;
    public $judul;
    public $deskripsi;
    public $tahun;
    public $tanggal_pengajuan;
    public $status = 'draft';
    public $catatan_verifikator;
    public $catatan_pimpinan;
    public $total_rencana;
    public $isEditing = false;

    // Inline items
    public $items = [];

    // View modal
    public $showViewModal = false;
    public $detail = [];
    public $detailItems = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
        'perPage' => ['except' => 10],
    ];

    protected $rules = [
        'satuan_id' => 'required|exists:satuans,id',
        'perencana_id' => 'required|exists:users,id',
        'kode_usulan' => 'required|string|max:255|unique:proposals,kode_usulan',
        'judul' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'tahun' => 'required|integer|min:2000|max:2100',
        'tanggal_pengajuan' => 'nullable|date',
        'status' => 'required|in:draft,diajukan,diverifikasi,disetujui,ditolak',
        'catatan_verifikator' => 'nullable|string',
        'catatan_pimpinan' => 'nullable|string',
        'total_rencana' => 'nullable|numeric|min:0',
    ];

    private function isPlanner(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Perencana', 'perencana']);
    }

    private function isVerifikator(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Verifikator', 'verifikator']);
    }

    private function isPimpinan(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Pimpinan', 'pimpinan']);
    }

    private function isSuperAdmin(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Super Admin']);
    }

    public function create()
    {
        $this->authorize('proposal.create');
        $this->resetForm();
        $this->perencana_id = auth()->id();
        if ($this->isPlanner()) {
            $this->satuan_id = auth()->user()->satuan_id;
        }
        $this->tahun = (int) date('Y');
        $this->tanggal_pengajuan = null;
        $this->status = 'draft';
        $this->total_rencana = 0;
        $this->items = [[
            'id' => null,
            'uraian' => '',
            'qty' => 1,
            'satuan' => '',
            'harga_satuan' => 0,
            'subtotal' => 0,
        ]];
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('proposal.edit');
        $p = Proposal::findOrFail($id);
        if ($this->isPlanner() && $p->perencana_id !== auth()->id()) {
            abort(403, 'Tidak diizinkan mengakses proposal ini.');
        }
        $this->proposalId = $p->id;
        $this->satuan_id = $p->satuan_id;
        $this->perencana_id = $p->perencana_id;
        $this->kode_usulan = $p->kode_usulan;
        $this->judul = $p->judul;
        $this->deskripsi = $p->deskripsi;
        $this->tahun = $p->tahun;
        $this->tanggal_pengajuan = optional($p->tanggal_pengajuan)->format('Y-m-d');
        $this->status = $p->status;
        $this->catatan_verifikator = $p->catatan_verifikator;
        $this->catatan_pimpinan = $p->catatan_pimpinan;
        $this->total_rencana = $p->total_rencana;
        $this->items = $p->items()->orderBy('id')->get()->map(fn($it) => [
            'id' => $it->id,
            'uraian' => $it->uraian,
            'qty' => (float) $it->qty,
            'satuan' => $it->satuan,
            'harga_satuan' => (float) $it->harga_satuan,
            'subtotal' => (float) $it->subtotal,
        ])->toArray();
        if ($this->isPlanner()) {
            $this->satuan_id = auth()->user()->satuan_id;
        }
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->isEditing) {
            $this->authorize('proposal.edit');
            $this->rules['kode_usulan'] = 'required|string|max:255|unique:proposals,kode_usulan,' . $this->proposalId;
        } else {
            $this->authorize('proposal.create');
        }

        $this->validate();

        if ($this->isPlanner()) {
            $this->perencana_id = auth()->id();
            $this->satuan_id = auth()->user()->satuan_id;
            $allowed = ['draft', 'diajukan'];
            if (!in_array($this->status, $allowed, true)) {
                $this->status = 'draft';
            }
        } elseif ($this->isVerifikator() && !$this->isSuperAdmin()) {
            $allowed = ['diajukan', 'diverifikasi'];
            if (!in_array($this->status, $allowed, true)) {
                $this->status = 'diajukan';
            }
        } elseif ($this->isPimpinan() && !$this->isSuperAdmin()) {
            $allowed = ['diverifikasi', 'disetujui', 'ditolak'];
            if (!in_array($this->status, $allowed, true)) {
                $this->status = 'diverifikasi';
            }
        }

        $payload = [
            'satuan_id' => $this->satuan_id,
            'perencana_id' => $this->perencana_id,
            'kode_usulan' => $this->kode_usulan,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'tahun' => (int) $this->tahun,
            'tanggal_pengajuan' => $this->tanggal_pengajuan ?: null,
            'status' => $this->status,
            'total_rencana' => $this->total_rencana ?? 0,
        ];

        // Only Verifikator or Super Admin may set this note
        if ($this->isVerifikator() || $this->isSuperAdmin()) {
            $payload['catatan_verifikator'] = $this->catatan_verifikator;
        }

        // Only Pimpinan or Super Admin may set this note
        if ($this->isPimpinan() || $this->isSuperAdmin()) {
            $payload['catatan_pimpinan'] = $this->catatan_pimpinan;
        }

        $proposal = $this->isEditing
            ? tap(Proposal::findOrFail($this->proposalId))->update($payload)
            : Proposal::create($payload);

        $keepIds = [];
        foreach ($this->items as $row) {
            $uraian = trim((string)($row['uraian'] ?? ''));
            if ($uraian === '') continue;
            $qty = (float) ($row['qty'] ?? 0);
            $harga = (float) ($row['harga_satuan'] ?? 0);
            $subtotal = round($qty * $harga, 2);
            $data = [
                'uraian' => $uraian,
                'qty' => $qty,
                'satuan' => (string)($row['satuan'] ?? ''),
                'harga_satuan' => $harga,
                'subtotal' => $subtotal,
            ];
            if (!empty($row['id'])) {
                $item = ProposalItem::where('proposal_id', $proposal->id)->find($row['id']);
                if ($item) {
                    $item->update($data);
                    $keepIds[] = $item->id;
                }
            } else {
                $item = $proposal->items()->create($data);
                $keepIds[] = $item->id;
            }
        }

        if ($this->isEditing) {
            ProposalItem::where('proposal_id', $proposal->id)
                ->when(count($keepIds) > 0, fn($q) => $q->whereNotIn('id', $keepIds))
                ->delete();
        }

        $proposal->recalcTotal();

        $this->resetForm();
        $this->showModal = false;
        session()->flash('message', 'Proposal saved successfully!');
    }

    public function delete($id)
    {
        $this->authorize('proposal.delete');
        $p = Proposal::findOrFail($id);
        if ($this->isPlanner() && $p->perencana_id !== auth()->id()) abort(403);
        $p->delete();
        session()->flash('message', 'Proposal deleted successfully!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->proposalId = null;
        $this->satuan_id = null;
        $this->perencana_id = null;
        $this->kode_usulan = '';
        $this->judul = '';
        $this->deskripsi = '';
        $this->tahun = null;
        $this->tanggal_pengajuan = null;
        $this->status = 'draft';
        $this->catatan_verifikator = '';
        $this->catatan_pimpinan = '';
        $this->total_rencana = 0;
        $this->isEditing = false;
        $this->items = [];
        $this->resetValidation();
    }

    public function addItemRow(): void
    {
        $this->items[] = ['id' => null, 'uraian' => '', 'qty' => 1, 'satuan' => '', 'harga_satuan' => 0, 'subtotal' => 0];
    }

    public function removeItemRow(int $index): void
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->recalcTotalValue();
        }
    }

    public function updated($name, $value): void
    {
        if (str_starts_with($name, 'items.')) {
            if (preg_match('/items\.(\d+)\.(qty|harga_satuan)/', $name, $m)) $this->recalcRow((int)$m[1]);
            $this->recalcTotalValue();
        }
    }

    private function recalcRow(int $index): void
    {
        if (isset($this->items[$index])) {
            $qty = (float)($this->items[$index]['qty'] ?? 0);
            $harga = (float)($this->items[$index]['harga_satuan'] ?? 0);
            $this->items[$index]['subtotal'] = round($qty * $harga, 2);
        }
    }

    private function recalcTotalValue(): void
    {
        $sum = 0;
        foreach ($this->items as $row) {
            $sum += (float)($row['subtotal'] ?? 0);
        }
        $this->total_rencana = round($sum, 2);
    }

    // Reset pagination when filters change
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function onItemChange(int $index): void
    {
        $this->recalcRow($index);
        $this->recalcTotalValue();
    }

    public function viewProposal(int $id): void
    {
        $this->authorize('proposal.view');
        $p = Proposal::with(['satuan', 'perencana', 'items'])->findOrFail($id);
        if ($this->isPlanner() && $p->perencana_id !== auth()->id()) abort(403);
        $this->detail = [
            'id' => $p->id,
            'kode_usulan' => $p->kode_usulan,
            'judul' => $p->judul,
            'satuan' => $p->satuan->nama ?? '-',
            'perencana' => $p->perencana->name ?? '-',
            'tahun' => $p->tahun,
            'tanggal_pengajuan' => optional($p->tanggal_pengajuan)->format('Y-m-d'),
            'status' => $p->status,
            'deskripsi' => $p->deskripsi,
            'catatan_verifikator' => $p->catatan_verifikator,
            'catatan_pimpinan' => $p->catatan_pimpinan,
            'total_rencana' => (float)$p->total_rencana,
        ];
        $this->detailItems = $p->items->map(fn($it) => [
            'uraian' => $it->uraian,
            'qty' => (float)$it->qty,
            'satuan' => $it->satuan,
            'harga_satuan' => (float)$it->harga_satuan,
            'subtotal' => (float)$it->subtotal,
        ])->toArray();
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->detail = [];
        $this->detailItems = [];
    }

    public function downloadSelected()
    {
        $this->authorize('proposal.view');
        if (!($this->isVerifikator() || $this->isSuperAdmin())) {
            abort(403, 'Hanya Verifikator atau Super Admin.');
        }
        $ids = collect($this->selected)
            ->filter(fn($v) => is_numeric($v))
            ->map(fn($v) => (int) $v)
            ->unique()
            ->values()
            ->all();
        if (empty($ids)) {
            session()->flash('message', 'Pilih minimal satu proposal untuk diunduh.');
            return;
        }
        return redirect()->route('proposals.pdf.batch', [
            'ids' => implode(',', $ids),
        ]);
    }

    public function render()
    {
        $this->authorize('proposal.view');
        $query = Proposal::with(['satuan', 'perencana'])->orderByDesc('created_at');
        if ($this->isPlanner()) {
            $query->where('perencana_id', auth()->id());
        } elseif ($this->isPimpinan() && !$this->isSuperAdmin()) {
            // Pimpinan melihat proposal dengan status diverifikasi/disetujui/ditolak
            $query->whereIn('status', ['diverifikasi', 'disetujui', 'ditolak']);
        }

        if (trim($this->search) !== '') {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('kode_usulan', 'like', $s)
                  ->orWhere('judul', 'like', $s)
                  ->orWhere('tahun', 'like', $s)
                  ->orWhereHas('satuan', fn($qq) => $qq->where('nama', 'like', $s))
                  ->orWhereHas('perencana', fn($qq) => $qq->where('name', 'like', $s));
            });
        }

        $statusOptions = ['draft', 'diajukan', 'diverifikasi', 'disetujui', 'ditolak'];
        if ($this->isPlanner()) {
            $statusOptions = ['draft', 'diajukan'];
        } elseif ($this->isVerifikator()) {
            $statusOptions = ['diajukan', 'diverifikasi'];
        } elseif ($this->isPimpinan()) {
            $statusOptions = ['diverifikasi', 'disetujui', 'ditolak'];
        }

        return view('livewire.proposal-management', [
            'proposals' => $query->paginate((int) $this->perPage),
            'satuanOptions' => $this->isPlanner() ? Satuan::where('id', auth()->user()->satuan_id)->get() : Satuan::orderBy('nama')->get(),
            'userOptions' => $this->isPlanner() ? User::where('id', auth()->id())->get() : User::orderBy('name')->get(),
            'statusOptions' => $statusOptions,
            'canBulkDownload' => ($this->isSuperAdmin() || $this->isVerifikator()),
        ]);
    }
}
