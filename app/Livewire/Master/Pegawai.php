<?php

namespace App\Livewire\Master;

use App\Models\Departemen;
use App\Models\Pegawai as ModelsPegawai;
use Livewire\Attributes\On;
use Livewire\Component;

class Pegawai extends Component
{
    public $judul = "Tambah Pegawai";
    public $modal = "ModalForm";

    public $mode = 'add';

    public $id;

    public $nik;
    public $nama_pegawai;
    public $jk;
    public $tempat_lahir;
    public $tanggal_lahir;
    public $agama;

    public $nip;
    public $pangkat;
    public $golongan;
    public $jabatan;
    public $jabatan_tugas_tambahan;
    public $unit_kerja_id;
    public $departemen_id;
    public $nama_departemen;

    public $hp;
    public $email;
    public $alamat;
    public $kategori_pegawai;

    public $tabActive = "identitas";

    public function render()
    {
        return view('livewire.master.pegawai');
    }

    public function tab($tab)
    {
        $this->tabActive = $tab;
    }

    #[On('add-data')]
    public function add($kategori_pegawai)
    {
        $this->kategori_pegawai = data_params($kategori_pegawai, 'kategori');
        // dd($this);
        $this->dispatch('open-modal', modal: $this->modal);
    }

    public function save()
    {
        $rules = [
            'nama_pegawai' => 'required',
            // 'jk' => 'required',
            // 'tempat_lahir' => 'required',
            // 'tanggal_lahir' => 'required',
            // 'agama' => 'required',
            'jabatan' => 'required',
            'departemen_id' => 'required',
        ];

        if ($this->nik && strlen($this->nik) > 3) {
            $rule_nik = [
                'nik' => 'required|unique:app_pegawai,nik',
            ];
            $rules += $rule_nik;
        }

        if ($this->nip && strlen($this->nip) > 3) {
            $rule_nip =  [
                'nip' => 'required|unique:app_pegawai,nip'
            ];
            $rules += $rule_nip;
        }

        if ($this->email && strlen($this->email) > 4) {
            $rule_email = [
                'email' => 'email|unique:app_pegawai,email',
            ];
            $rules += $rule_email;
        }

        // dd($rules, $this, $this->getErrorBag());

        $this->validate($rules);

        // dd($this);

        $values = [
            'nik' => $this->nik,
            'nama_pegawai' => $this->nama_pegawai,
            'nip' => $this->nip,
            'jk' => $this->jk,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'agama' => $this->agama,
            'pangkat' => $this->pangkat,
            'golongan' => $this->golongan,
            'jabatan' => $this->jabatan,
            'jabatan_tugas_tambahan' => $this->jabatan_tugas_tambahan,
            'unit_kerja_id' => $this->unit_kerja_id,
            'departemen_id' => $this->departemen_id,
            'departemen_string' => $this->nama_departemen,
            'hp' => $this->hp,
            'email' => $this->email,
            'alamat' => $this->alamat,
            'kategori_pegawai' => $this->kategori_pegawai,
        ];

        if ($this->mode == 'add') {
            ModelsPegawai::create($values);
            $this->dispatch('alert', type: 'success', title: 'Successfully', message: 'Data Pegawai Berhasil Ditambahkan');
        }

        $this->dispatch('load-datatable2');
        $this->_reset();
    }

    public function _reset()
    {
        $this->resetErrorBag();

        $this->id = "";
        $this->mode = "add";
        $this->tabActive = "identitas";

        $this->nik = "";
        $this->nama_pegawai = "";
        $this->jk = "";
        $this->tempat_lahir = "";
        $this->tanggal_lahir = "";
        $this->agama = "";

        $this->nip = "";
        $this->pangkat = "";
        $this->golongan = "";
        $this->jabatan = "";
        $this->jabatan_tugas_tambahan = "";
        $this->unit_kerja_id = "";
        $this->departemen_id = "";
        $this->nama_departemen = "";

        $this->hp = "";
        $this->email = "";
        $this->alamat = "";
        $this->kategori_pegawai = "";

        $this->dispatch('close-modal', modal: $this->modal);
    }
}
