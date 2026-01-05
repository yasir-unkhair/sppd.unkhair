<?php

namespace App\Livewire\Sppd;

use App\Models\KodeSurat;
use App\Models\RiwayatNomorSurat;
use App\Models\SuratPerjalananDinas;
use App\Models\SuratTugasDinas;
use Livewire\Component;

class Edit extends Component
{
    public $judul = "Edit SPPD";

    public $sppd_id;

    public $nomor_surat, $kode_surat, $nomor_spd, $nomor_spd_old;
    public $pegawai_id, $departemen_id, $kegiatan_spd, $angkutan, $berangakat, $tujuan;
    public $lama_pd = 1, $tanggal_berangakat, $tanggal_kembali, $keterangan, $pejabat_ppk, $status_spd;
    public $kode_mak, $detail_alokasi_anggaran;

    public $sumber_dana, $instansi;

    public $tanggal_spd;

    public $readonly = "readonly";

    public $nama_pegawai;
    public $departemen;
    public $riwayat_nomor_surat = [];


    public function mount($params)
    {
        $this->sppd_id = data_params($params, 'sppd_id');
        $get = SuratPerjalananDinas::with(['pegawai', 'departemen'])->where('id', $this->sppd_id)->first();
        $this->nomor_spd = $get->nomor_spd;
        $this->nomor_spd_old = $get->nomor_spd;

        $this->pecah_nomor_spd($this->nomor_spd);

        $this->tanggal_spd = $get->tanggal_spd;
        $this->pegawai_id = $get->pegawai_id;
        $this->departemen_id = $get->departemen_id;
        $this->kegiatan_spd = $get->kegiatan_spd;
        $this->angkutan = $get->angkutan;
        $this->berangakat = $get->berangakat;
        $this->tujuan = $get->tujuan;
        $this->lama_pd = $get->lama_pd;
        $this->tanggal_berangakat = $get->tanggal_berangakat;
        $this->tanggal_kembali = $get->tanggal_kembali;
        $this->keterangan = $get->keterangan;
        $this->kode_mak = $get->kode_mak;
        $this->detail_alokasi_anggaran = $get->detail_alokasi_anggaran;

        $this->sumber_dana = $get->sumber_dana;
        $this->instansi = $get->instansi;

        $this->nama_pegawai = $get->pegawai->nama_pegawai;
        $this->departemen = $get->departemen->departemen;
    }

    public function render()
    {
        if ($this->nomor_surat) {
            $this->update_nomor_sppd($this->nomor_surat);
        }

        return view('livewire.sppd.edit');
    }

    public function pecah_nomor_spd($nomor_spd)
    {
        $pecah = explode("/", $nomor_spd);
        $this->nomor_surat = trim($pecah[0]);
        $this->kode_surat = trim($pecah[1]) . "/" . trim($pecah[2]) . "/" . trim($pecah[3]);

        $get = KodeSurat::where('kode', trim($pecah[2]))->first();
        $kode = "UN44" . "/" . trim($pecah[2]);
        $tahun = trim($pecah[3]);
        $jenis_surat = 'spd';
        $keterangan = auth()->user()->name . ' membuat SPPD ' . $get->keterangan;
        $this->riwayat_nomor_surat = [
            'nomor' => $this->nomor_surat,
            'kode' => $kode,
            'tahun' => $tahun,
            'jenis_surat' => $jenis_surat,
            'keterangan' => $keterangan
        ];
    }

    public function update_nomor_sppd($nomor)
    {
        $this->riwayat_nomor_surat['nomor'] = $nomor;
        $this->nomor_spd = $nomor . "/" . $this->kode_surat;
    }

    public function pass_tanggal_kembali($value, $form = NULL)
    {
        if ($form == 'lama_pd') {
            $this->lama_pd = $value;
            if ($this->tanggal_berangakat) {
                $this->tanggal_kembali = add_tanggal($this->tanggal_berangakat, $this->lama_pd);
            }
        }

        if ($form == 'tanggal_berangakat') {
            $this->tanggal_berangakat = $value;
            if ($this->tanggal_berangakat) {
                $this->tanggal_kembali = add_tanggal($this->tanggal_berangakat, $this->lama_pd);
            }
        }
    }

    public function save()
    {
        // abort(403);

        $this->validate([
            'nomor_surat' => 'required|numeric|regex:/^[0-9]+$/',
            'kode_surat' => 'required',
            'nomor_spd' => 'required|unique:app_surat_perjalanan_dinas,nomor_spd,' . $this->sppd_id,
            'pegawai_id' => 'required',
            'departemen_id' => 'required',
            'kegiatan_spd' => 'required',
            'angkutan' => 'required',
            'berangakat' => 'required',
            'tujuan' => 'required',
            'lama_pd' => 'required|min_digits:1',
            'tanggal_berangakat' => 'required',
            'tanggal_kembali' => 'required',
            'kode_mak' => 'required',
            'tanggal_spd' => 'required',
        ]);

        // edit sppd jika std sudah terbit maka akan terupdate otomatis
        $std = SuratTugasDinas::where('spd_id', $this->sppd_id)->first();
        if ($std) {
            // remove daftar pegawai
            $std->pegawai()->sync([]);

            $nomor_std = $std->nomor_std;

            // jika ada perubahan nomor_spd maka nomor_std juga berubah
            if ($this->nomor_spd != $this->nomor_spd_old) {
                // pecah normor_spd untuk ambil nomor urut surat
                $pecah_spd = explode("/", $this->nomor_spd);

                // pecah nomor_std
                $pecah_std = explode("/", $nomor_std);
                // simpan nomor_std dengan format nomor/UN44/kode_surat/tahun
                $nomor_std = $pecah_spd[0] . "/" . trim($pecah_std[1]) . "/" . trim($pecah_std[2]) . "/" . trim($pecah_std[3]);
            }

            $std->update([
                'nomor_std' => $nomor_std,
                'tanggal_std' => $this->tanggal_spd,
                'departemen_id' => $this->departemen_id,
                'kegiatan_std' => $this->kegiatan_spd,
                'tanggal_mulai_tugas' => $this->tanggal_berangakat,
                'tanggal_selesai_tugas' => $this->tanggal_kembali,
            ]);

            // simpan daftar pegawai
            $std->pegawai()->sync($this->pegawai_id);
        }
        // dd($std);

        SuratPerjalananDinas::where('id', $this->sppd_id)->update([
            'tanggal_spd' => $this->tanggal_spd,
            'nomor_spd' => $this->nomor_spd,
            'pegawai_id' => $this->pegawai_id,
            'departemen_id' => $this->departemen_id,
            'kegiatan_spd' => $this->kegiatan_spd,
            'angkutan' => $this->angkutan,
            'berangakat' => $this->berangakat,
            'tujuan' => $this->tujuan,
            'lama_pd' => $this->lama_pd,
            'tanggal_berangakat' => $this->tanggal_berangakat,
            'tanggal_kembali' => $this->tanggal_kembali,
            'keterangan' => $this->keterangan,
            'kode_mak' => $this->kode_mak,
            'detail_alokasi_anggaran' => $this->detail_alokasi_anggaran,
            'sumber_dana' => $this->sumber_dana,
            'instansi' => $this->instansi,
        ]);

        $this->simpan_riwayat_nomor_surat($this->sppd_id);

        $this->dispatch('alert', type: 'success', title: 'Successfuly', message: 'SPPD Berhasil Diedit.');
    }

    public function simpan_riwayat_nomor_surat($sppd_id)
    {
        if ($this->nomor_spd != $this->nomor_spd_old) {
            $value = array_merge($this->riwayat_nomor_surat, ['surat_id' => $sppd_id]);
            RiwayatNomorSurat::create($value);
        }
    }
}
