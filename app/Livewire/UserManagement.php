<?php
// app/Livewire/UserManagement.php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $userId;
    public $name;
    public $email;
    public $password;
    public $selectedRoles = [];
    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'selectedRoles' => 'array',
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
        ];

        if (!$this->isEditing || $this->password) {
            $userData['password'] = Hash::make($this->password);
        }

        if ($this->isEditing) {
            $user = User::findOrFail($this->userId);
            $user->update($userData);
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
        $this->password = '';
        $this->selectedRoles = [];
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        $this->authorize('user.view');
        
        return view('livewire.user-management', [
            'users' => User::with('roles')->paginate(10),
            'roles' => Role::all(),
        ]);
    }
}