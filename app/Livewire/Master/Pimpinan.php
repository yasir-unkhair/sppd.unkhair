<?php

namespace App\Livewire\Master;

use App\Models\Pimpinan as ModelsPimpinan;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class Pimpinan extends Component
{
    public $judul = "Tambah Pimpinan";
    public $modal = "ModalForm";

    public $mode = 'add';
    public $id;
    public $nama_pimpinan;
    public $nip;
    public $golongan;
    public $jabatan;
    public $singkat_jabatan;
    public $ppk = 0;
    public $user_id;

    public function render()
    {
        $users = User::role('review-st')->orderBy('created_at', 'ASC')->get();
        return view('livewire.master.pimpinan', ['users' => $users]);
    }

    #[On('add-data')]
    public function add($title)
    {
        $this->judul = $title;
        $this->dispatch('open-modal', modal: $this->modal);
    }

    #[On('edit-data')]
    public function edit($pimpinan_id)
    {
        $this->judul = 'Edit Pimpinan';

        $get = ModelsPimpinan::where('id', $pimpinan_id)->first();
        $this->nama_pimpinan = $get->nama_pimpinan;
        $this->nip = $get->nip;
        $this->golongan = $get->golongan;
        $this->singkat_jabatan = $get->jabatan;
        $this->jabatan = $get->detail_jabatan;
        $this->ppk = $get->ppk;
        $this->user_id = $get->user_id;
        $this->id = $pimpinan_id;
        $this->mode = 'edit';

        $this->dispatch('open-modal', modal: $this->modal);
    }

    public function save()
    {
        $this->validate([
            'nama_pimpinan' => 'required',
            'nip' => 'required',
            'jabatan' => 'required',
            'singkat_jabatan' => 'required',
        ]);

        if ($this->mode == 'add') {
            ModelsPimpinan::create([
                'nama_pimpinan' => $this->nama_pimpinan,
                'nip' => $this->nip,
                'golongan' => $this->golongan,
                'jabatan' => $this->singkat_jabatan,
                'detail_jabatan' => $this->jabatan,
                'ppk' => $this->ppk,
                'user_id' => $this->user_id,
            ]);

            $this->dispatch('alert', type: 'success', title: 'Successfully', message: 'Pimpinan Berhasil Ditambahkan');
        } else {
            ModelsPimpinan::where('id', $this->id)->update([
                'nama_pimpinan' => $this->nama_pimpinan,
                'nip' => $this->nip,
                'golongan' => $this->golongan,
                'jabatan' => $this->singkat_jabatan,
                'detail_jabatan' => $this->jabatan,
                'ppk' => $this->ppk,
                'user_id' => $this->user_id,
            ]);

            $this->dispatch('alert', type: 'success', title: 'Successfully', message: 'Pimpinan Berhasil Diperbarui');
        }

        $this->dispatch('load-datatable');
        $this->_reset();
    }

    public function _reset()
    {
        $this->resetErrorBag();

        $this->nama_pimpinan = '';
        $this->nip = '';
        $this->golongan = '';
        $this->jabatan = '';
        $this->singkat_jabatan = '';
        $this->ppk = 0;
        $this->id = '';
        $this->user_id = '';
        $this->mode = 'add';
        $this->dispatch('close-modal', modal: $this->modal);
    }
}
