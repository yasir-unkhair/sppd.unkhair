<?php

namespace App\Livewire\Std;

use App\Models\SuratTugasDinas;
use Livewire\Attributes\On;
use Livewire\Component;

class InputNilaiPencairan extends Component
{
    public $modal = "ModalInputNilaiPencairanSTD";
    public $judul = "Input Nilai Pencairan STD";
    public $params;
    public $get;

    public $nilai_pencairan = 0;

    public function render()
    {
        if ($this->nilai_pencairan) {
            $this->nilai_pencairan = rupiah($this->nilai_pencairan);
        }
        return view('livewire.std.input-nilai-pencairan');
    }

    #[On('form-nilai-pencairan-std')]
    public function modal_form($params)
    {
        $this->params = decode_arr($params);
        $this->get = SuratTugasDinas::with(['pegawai', 'departemen', 'user'])->where('id', $this->params['std_id'])->first();
        $this->nilai_pencairan = rupiah($this->get->nilai_pencairan);
        // dd($this);
        $this->dispatch('open-modal', modal: $this->modal);
    }

    public function save()
    {
        $this->validate([
            'nilai_pencairan' => 'required'
        ]);

        SuratTugasDinas::where('id', $this->params['std_id'])->update([
            'nilai_pencairan' => rupiah($this->nilai_pencairan, false)
        ]);

        $this->dispatch('alert', type: 'success', title: 'Succesfully', message: 'Berhasil Input Nilai Pencairan STD');
        $this->_reset();
        $this->dispatch('load-datatable');
    }

    public function _reset()
    {
        $this->resetErrorBag();

        $this->get = NULL;
        $this->params = NULL;
        $this->nilai_pencairan = 0;

        $this->dispatch('close-modal', modal: $this->modal);
    }
}
