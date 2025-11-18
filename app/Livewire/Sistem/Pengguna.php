<?php

namespace App\Livewire\Sistem;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Pengguna extends Component
{
    use WithPagination;

    public $judul = "Data Pengguna";
    public $modal = "ModalUpdatePengguna";

    public $id;
    public $name;
    public $email;
    public $username;
    public $password;
    public $is_active = 1;
    public $roles_pengguna = [];
    public $mode = "new";

    public $pencarian = '';
    public int $perPage = 10;

    public function render()
    {
        if ($this->pencarian) {
            $this->resetPage();
        }
        $listuser = User::role(['admin-spd', 'admin-st', 'ppk', 'keuangan', 'review-st', 'admin-st-dk', 'kepegawaian'])->pencarian($this->pencarian)->orderBy('created_at', 'ASC')->paginate($this->perPage);
        return view('livewire.sistem.pengguna-index', ['listdata' => $listuser])
            ->extends('layouts.backend')
            ->section('content');
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|min:5|max:15|unique:users,username',
            'password' => 'required|min:5|max:15',
            'roles_pengguna' => 'required|array|min:1',
        ];

        if ($this->mode == 'edit') {
            if (!trim($this->password)) {
                unset($rules['password']);
            }

            $rules['email'] = 'required|email|unique:users,email,' . $this->id;
            $rules['username'] = 'required|string|min:5|max:15|unique:users,username,' . $this->id;
        }

        // dd($rules);
        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'is_active' => $this->is_active ? true : false
        ];

        if ($this->mode == 'edit') {
            unset($data['password']);
            if (trim($this->password)) {
                $data['password'] = Hash::make($this->password);
            }
        }

        // dd($data);

        if ($this->mode == 'new') {
            $user = User::create($data);
            if ($user->roles()->count()) {
                foreach ($user->roles()->get() as $row) {
                    $user->removeRole($row->name);
                }
            }

            foreach ($this->roles_pengguna as $role) {
                $user->assignRole($role);
            }

            $message = "Berhasil tambah pengguna baru";
        } else {
            // dd($data, $this->id);
            $user = User::where('id', $this->id);
            $user->update($data);

            $user = $user->first();
            // dd($user->roles()->count());
            if ($user->roles()->count()) {
                foreach ($user->roles()->get() as $row) {
                    $user->removeRole($row->name);
                }
            }

            foreach ($this->roles_pengguna as $role) {
                $user->assignRole($role);
            }

            $message = "Berhasil perbaruhi data pengguna";
        }

        // alert()->success('Success', $message);
        $this->dispatch('alert', type: 'success', title: 'Succesfuly', messasge: $message);
        // return $this->redirect(route('admin.pengguna'));
        $this->_reset();
    }

    public function add_pengguna()
    {
        $this->mode = "new";
        $this->dispatch('open-modal', modal: $this->modal);
    }

    public function edit($id)
    {
        $get = User::with('roles')->where('id', $id)->first();
        $this->id = $get->id;
        $this->name = $get->name;
        $this->email = $get->email;
        $this->username = $get->username;
        $this->is_active = $get->is_active;
        $this->roles_pengguna = $get->roles()->get()->pluck('name')->toArray();
        $this->mode = "edit";

        $this->dispatch('open-modal', modal: $this->modal);
    }

    public function _reset()
    {
        $this->resetErrorBag();
        $this->reset(['id', 'name', 'email', 'username', 'password']);
        $this->dispatch('close-modal', modal: $this->modal);
    }
}
