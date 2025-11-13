<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Satuan;

class SatuanManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $satuanId;
    public $nama;
    public $kode;
    public $parent_id = null;
    public $aktif = true;
    public $isEditing = false;

    protected $rules = [
        'nama' => 'required|string|max:150',
        'kode' => 'required|string|max:50|unique:satuans,kode',
        'parent_id' => 'nullable|exists:satuans,id',
        'aktif' => 'boolean',
    ];

    public function create()
    {
        $this->authorize('satuan.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('satuan.edit');
        $satuan = Satuan::findOrFail($id);
        $this->satuanId = $satuan->id;
        $this->nama = $satuan->nama;
        $this->kode = $satuan->kode;
        $this->parent_id = $satuan->parent_id;
        $this->aktif = (bool) $satuan->aktif;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->isEditing) {
            $this->authorize('satuan.edit');
            $this->rules['kode'] = 'required|string|max:50|unique:satuans,kode,' . $this->satuanId;
        } else {
            $this->authorize('satuan.create');
        }

        $this->validate();

        // Prevent setting self as parent
        if ($this->isEditing && $this->parent_id == $this->satuanId) {
            $this->parent_id = null;
        }

        $payload = [
            'nama' => $this->nama,
            'kode' => $this->kode,
            'parent_id' => $this->parent_id,
            'aktif' => (bool) $this->aktif,
        ];

        if ($this->isEditing) {
            Satuan::findOrFail($this->satuanId)->update($payload);
        } else {
            Satuan::create($payload);
        }

        $this->resetForm();
        $this->showModal = false;
        session()->flash('message', 'Satuan saved successfully!');
    }

    public function delete($id)
    {
        $this->authorize('satuan.delete');
        Satuan::findOrFail($id)->delete();
        session()->flash('message', 'Satuan deleted successfully!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->satuanId = null;
        $this->nama = '';
        $this->kode = '';
        $this->parent_id = null;
        $this->aktif = true;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        $this->authorize('satuan.view');

        return view('livewire.satuan-management', [
            'items' => Satuan::with('parent')->orderByDesc('created_at')->paginate(10),
            'parentOptions' => Satuan::orderBy('nama')->get(),
        ]);
    }
}
