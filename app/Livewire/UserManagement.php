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
        session()->flash('message', 'User saved successfully!');
    }

    public function delete($id)
    {
        $this->authorize('user.delete');
        User::findOrFail($id)->delete();
        session()->flash('message', 'User deleted successfully!');
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
