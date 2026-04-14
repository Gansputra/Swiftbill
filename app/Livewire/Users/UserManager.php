<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManager extends Component
{
    public $users = [];
    
    // Modal State
    public $isModalOpen = false;
    public $isEditMode = false;
    public $userIdToEdit = null;

    // Form Fields
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'cashier';

    public function mount()
    {
        // Security check, ensure only admin can access this Livewire component
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->users = User::latest()->get();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->userIdToEdit = $id;

        $user = User::findOrFail($id);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        // Password left intentionally blank for edit
        
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'cashier';
        $this->isEditMode = false;
        $this->userIdToEdit = null;
        $this->resetErrorBag();
    }

    public function saveUser()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->userIdToEdit)
            ],
            'role' => 'required|in:admin,cashier'
        ];

        // Only require password on creation or if it's filled in during an edit
        if (!$this->isEditMode || !empty($this->password)) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEditMode) {
            User::findOrFail($this->userIdToEdit)->update($data);
        } else {
            User::create($data);
        }

        $this->closeModal();
        $this->loadUsers();
    }

    public function deleteUser($id)
    {
        // Prevent deleting oneself
        if (auth()->id() === $id) {
            $this->addError('delete', 'You cannot delete your own admin account.');
            return;
        }

        User::findOrFail($id)->delete();
        $this->loadUsers();
    }

    public function render()
    {
        return view('livewire.users.user-manager')->layout('layouts.app');
    }
}
