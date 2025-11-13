<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PengumumanManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $showModal = false;
    public $pengumumanId;
    public $judul = '';
    public $deskripsi = '';
    public $file; // Temporary upload
    public $isEditing = false;
    public $aktif = true;
    public $publish_at = null; // datetime-local string

    protected $rules = [
        'judul' => 'required|string|max:255',
        'deskripsi' => 'required|string',
        'file' => 'nullable|file|max:10240', // 10MB, any file type
        'aktif' => 'boolean',
        'publish_at' => 'nullable|date',
    ];

    public function create()
    {
        $this->authorize('pengumuman.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('pengumuman.edit');
        $item = Pengumuman::findOrFail($id);
        $this->pengumumanId = $item->id;
        $this->judul = $item->judul;
        $this->deskripsi = $item->deskripsi;
        $this->aktif = (bool) $item->aktif;
        $this->publish_at = $item->publish_at ? $item->publish_at->format('Y-m-d\TH:i') : null;
        // Convert publish_at to UI timezone for input display
        if ($this->publish_at) {
            $uiTz = config('app.ui_timezone', 'Asia/Jakarta');
            $this->publish_at = \Carbon\Carbon::parse($item->publish_at)
                ->setTimezone($uiTz)
                ->format('Y-m-d\\TH:i');
        }
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->isEditing) {
            $this->authorize('pengumuman.edit');
        } else {
            $this->authorize('pengumuman.create');
        }

        $this->validate();

        $data = [
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'aktif' => (bool) $this->aktif,
        ];

        if ($this->publish_at) {
            $data['publish_at'] = $this->publish_at; // initial value from input
        } else {
            $data['publish_at'] = null;
        }

        // Normalize publish_at to UTC using UI timezone
        if (!empty($data['publish_at'])) {
            $uiTz = config('app.ui_timezone', 'Asia/Jakarta');
            $data['publish_at'] = Carbon::createFromFormat('Y-m-d\\TH:i', $this->publish_at, $uiTz)
                ->setTimezone('UTC');
        }

        $existing = null;
        if ($this->isEditing) {
            $existing = Pengumuman::findOrFail($this->pengumumanId);
        }

        if ($this->file) {
            $path = $this->file->store('pengumuman', 'public');
            $data['file'] = $path;
            if ($existing && !empty($existing->file)) {
                Storage::disk('public')->delete($existing->file);
            }
        }

        if ($this->isEditing) {
            $existing->update($data);
            $item = $existing;
        } else {
            $item = Pengumuman::create($data);
        }

        $this->resetForm();
        $this->showModal = false;
        session()->flash('message', 'Pengumuman saved successfully!');
    }

    public function delete($id)
    {
        $this->authorize('pengumuman.delete');
        $item = Pengumuman::findOrFail($id);
        if (!empty($item->file)) {
            Storage::disk('public')->delete($item->file);
        }
        $item->delete();
        session()->flash('message', 'Pengumuman deleted successfully!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->pengumumanId = null;
        $this->judul = '';
        $this->deskripsi = '';
        $this->file = null;
        $this->aktif = true;
        $this->publish_at = null;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        $this->authorize('pengumuman.view');

        return view('livewire.pengumuman-management', [
            'items' => Pengumuman::latest()->paginate(10),
        ]);
    }
}
