<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AnnualBudget;
use App\Models\AnnualBudgetItem;
use App\Models\Satuan;
use App\Models\Proposal;

class AnnualBudgetManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $showViewModal = false;

    public $budgetId;
    public $tahun;
    public $satuan_id;
    public $nomor_dokumen;
    public $status = 'draft';
    public $total_rencana = 0;

    public $items = [];

    public $detail = [];
    public $detailItems = [];

    protected $rules = [
        'tahun' => 'required|integer|min:2000|max:2100',
        'satuan_id' => 'required|exists:satuans,id',
        'nomor_dokumen' => 'nullable|string|max:255',
        'status' => 'required|in:draft,final',
        'total_rencana' => 'nullable|numeric|min:0',
    ];

    private function isPlanner(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Perencana', 'perencana']);
    }

    public function create(): void
    {
        $this->authorize('annualbudget.create');
        $this->resetForm();
        $this->tahun = (int) date('Y');
        if ($this->isPlanner()) {
            $this->satuan_id = auth()->user()->satuan_id;
        }
        $this->status = 'draft';
        $this->items = [[
            'id' => null,
            'uraian' => '',
            'qty' => 1,
            'harga_satuan' => 0,
            'subtotal' => 0,
            'sumber_proposal_id' => null,
        ]];
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('annualbudget.edit');
        $b = AnnualBudget::with('items')->findOrFail($id);
        if ($this->isPlanner() && $b->satuan_id !== auth()->user()->satuan_id) abort(403);

        $this->budgetId = $b->id;
        $this->tahun = $b->tahun;
        $this->satuan_id = $b->satuan_id;
        $this->nomor_dokumen = $b->nomor_dokumen;
        $this->status = $b->status;
        $this->total_rencana = (float) $b->total_rencana;
        $this->items = $b->items()->orderBy('id')->get()->map(fn($it) => [
            'id' => $it->id,
            'uraian' => $it->uraian,
            'qty' => (float) $it->qty,
            'harga_satuan' => (float) $it->harga_satuan,
            'subtotal' => (float) $it->subtotal,
            'sumber_proposal_id' => $it->sumber_proposal_id,
        ])->toArray();
        if ($this->isPlanner()) {
            $this->satuan_id = auth()->user()->satuan_id;
        }
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->budgetId) {
            $this->authorize('annualbudget.edit');
        } else {
            $this->authorize('annualbudget.create');
        }

        $this->validate();

        if ($this->isPlanner()) {
            $this->satuan_id = auth()->user()->satuan_id;
        }

        $payload = [
            'tahun' => (int) $this->tahun,
            'satuan_id' => $this->satuan_id,
            'nomor_dokumen' => $this->nomor_dokumen,
            'status' => $this->status,
            'total_rencana' => (float) ($this->total_rencana ?? 0),
        ];

        $budget = $this->budgetId
            ? tap(AnnualBudget::findOrFail($this->budgetId))->update($payload)
            : AnnualBudget::create($payload);

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
                'harga_satuan' => $harga,
                'subtotal' => $subtotal,
                'sumber_proposal_id' => $row['sumber_proposal_id'] ?? null,
            ];

            if (!empty($row['id'])) {
                $item = AnnualBudgetItem::where('annual_budget_id', $budget->id)->find($row['id']);
                if ($item) { $item->update($data); $keepIds[] = $item->id; }
            } else {
                $item = $budget->items()->create($data); $keepIds[] = $item->id;
            }
        }

        if ($this->budgetId) {
            AnnualBudgetItem::where('annual_budget_id', $budget->id)
                ->when(count($keepIds) > 0, fn($q) => $q->whereNotIn('id', $keepIds))
                ->delete();
        }

        $budget->recalcTotal();

        $this->resetForm();
        $this->showModal = false;
        session()->flash('message', 'Annual budget saved successfully!');
    }

    public function delete(int $id): void
    {
        $this->authorize('annualbudget.delete');
        $b = AnnualBudget::findOrFail($id);
        if ($this->isPlanner() && $b->satuan_id !== auth()->user()->satuan_id) abort(403);
        $b->delete();
        session()->flash('message', 'Annual budget deleted successfully!');
    }

    public function addItemRow(): void
    {
        $this->items[] = ['id'=>null,'uraian'=>'','qty'=>1,'harga_satuan'=>0,'subtotal'=>0,'sumber_proposal_id'=>null];
    }

    public function removeItemRow(int $index): void
    {
        if(isset($this->items[$index])){ unset($this->items[$index]); $this->items=array_values($this->items); $this->recalcTotalValue(); }
    }

    public function updated($name, $value): void
    {
        if (str_starts_with($name, 'items.')) {
            if (preg_match('/items\.(\d+)\.(qty|harga_satuan)/', $name, $m)) $this->recalcRow((int)$m[1]);
            $this->recalcTotalValue();
        }
    }

    private function recalcRow(int $index): void
    { if(isset($this->items[$index])){ $qty=(float)($this->items[$index]['qty']??0); $harga=(float)($this->items[$index]['harga_satuan']??0); $this->items[$index]['subtotal']=round($qty*$harga,2);} }

    private function recalcTotalValue(): void
    { $sum=0; foreach($this->items as $row){ $sum+=(float)($row['subtotal']??0);} $this->total_rencana=round($sum,2); }

    public function viewBudget(int $id): void
    {
        $this->authorize('annualbudget.view');
        $b = AnnualBudget::with(['satuan','items'])->findOrFail($id);
        if ($this->isPlanner() && $b->satuan_id !== auth()->user()->satuan_id) abort(403);
        $this->detail = [
            'tahun' => $b->tahun,
            'satuan' => $b->satuan->nama ?? '-',
            'nomor_dokumen' => $b->nomor_dokumen,
            'status' => $b->status,
            'total_rencana' => (float) $b->total_rencana,
        ];
        $this->detailItems = $b->items->map(fn($it)=>[
            'uraian'=>$it->uraian,
            'qty'=>(float)$it->qty,
            'harga_satuan'=>(float)$it->harga_satuan,
            'subtotal'=>(float)$it->subtotal,
        ])->toArray();
        $this->showViewModal = true;
    }

    public function closeModal(): void
    { $this->showModal = false; $this->resetForm(); }

    public function closeViewModal(): void
    { $this->showViewModal = false; $this->detail = []; $this->detailItems = []; }

    public function resetForm(): void
    {
        $this->budgetId = null; $this->tahun = null; $this->satuan_id = null; $this->nomor_dokumen = null;
        $this->status = 'draft'; $this->total_rencana = 0; $this->items = [];
        $this->resetValidation();
    }

    public function render()
    {
        $this->authorize('annualbudget.view');

        $query = AnnualBudget::with(['satuan'])->orderByDesc('created_at');
        if ($this->isPlanner()) $query->where('satuan_id', auth()->user()->satuan_id);

        return view('livewire.annual-budget-management', [
            'budgets' => $query->paginate(10),
            'satuanOptions' => $this->isPlanner() ? Satuan::where('id', auth()->user()->satuan_id)->get() : Satuan::orderBy('nama')->get(),
            'proposalOptions' => Proposal::orderBy('kode_usulan')->get(['id','kode_usulan','judul']),
            'statusOptions' => ['draft','final'],
        ]);
    }
}
