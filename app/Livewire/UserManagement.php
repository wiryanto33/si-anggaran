<?php
// app/Livewire/UserManagement.php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Satuan;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class UserManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $showModal = false;
    public $userId;
    public $name;
    public $email;
    public $avatar;
    public $password;
    public $selectedRoles = [];
    public $satuan_id = null;
    public $active = true;
    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'password' => 'required|min:8',
        'selectedRoles' => 'array',
        'satuan_id' => 'nullable|exists:satuans,id',
        'active' => 'boolean',
    ];

    public function create()
    {
        $this->authorize('user.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('user.edit');
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->satuan_id = $user->satuan_id;
        $this->active = (bool) $user->active;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->isEditing) {
            $this->authorize('user.edit');
            $this->rules['email'] = 'required|email|unique:users,email,' . $this->userId;
            $this->rules['password'] = 'nullable|min:8';
        } else {
            $this->authorize('user.create');
        }

        $this->validate();

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'satuan_id' => $this->satuan_id,
            'active' => (bool) $this->active,
        ];

        $existingUser = null;
        if ($this->isEditing) {
            $existingUser = User::findOrFail($this->userId);
        }

        if ($this->avatar) {
            $avatarPath = $this->avatar->store('avatars', 'public');
            $userData['avatar'] = $avatarPath;

            if ($existingUser && !empty($existingUser->avatar)) {
                Storage::disk('public')->delete($existingUser->avatar);
            }
        }

        if (!$this->isEditing || $this->password) {
            $userData['password'] = Hash::make($this->password);
        }

        if ($this->isEditing) {
            $existingUser->update($userData);
            $user = $existingUser;
        } else {
            $user = User::create($userData);
        }

        $user->syncRoles($this->selectedRoles);

        $this->resetForm();
        $this->showModal = false;
        session()->flash('message', 'User berhasil disimpan!');
    }

    public function delete($id)
    {
        $this->authorize('user.delete');

        if ($id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
            return;
        }

        try {
            $user = User::findOrFail($id);

            // Bersihkan data terkait (Proposal & Approval) secara cascade
            // Dilakukan tanpa pengecekan role untuk memastikan semua data referensi dihapus
            \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
                // Ambil semua proposal miliknya (termasuk yang soft-deleted)
                $proposals = \App\Models\Proposal::where('perencana_id', $user->id)->withTrashed()->get();
                foreach ($proposals as $proposal) {
                    // Hapus file lampiran jika ada
                    foreach ($proposal->attachments as $attachment) {
                        if (Storage::disk('public')->exists($attachment->path)) {
                            Storage::disk('public')->delete($attachment->path);
                        }
                        $attachment->delete();
                    }
                    // Force delete proposal untuk menghapus permanen dari DB 
                    $proposal->forceDelete();
                }

                // Hapus approval yang dilakukan oleh user ini sebagai aktor (jika ada)
                \App\Models\Approval::where('actor_id', $user->id)->delete();
            });

            // Delete avatar if exists
            if (!empty($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();
            session()->flash('message', 'User berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") { // Integrity constraint violation
                session()->flash('error', 'User tidak dapat dihapus karena memiliki data terkait di tabel lain (seperti Realisasi Anggaran). Silakan hapus data tersebut terlebih dahulu.');
            } else {
                session()->flash('error', 'Terjadi kesalahan saat menghapus user.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->avatar = null;
        $this->password = '';
        $this->selectedRoles = [];
        $this->satuan_id = null;
        $this->active = true;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        $this->authorize('user.view');

        return view('livewire.user-management', [
            'users' => User::with(['roles', 'satuan'])->paginate(10),
            'roles' => Role::all(),
            'satuans' => Satuan::orderBy('nama')->get(),
        ]);
    }
}
