<?php

namespace App\Livewire\Sppd;

use App\Models\Departemen;
use App\Models\KodeSurat;
use App\Models\RiwayatNomorSurat;
use App\Models\SuratPerjalananDinas;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class Create extends Component
{
    public $judul = "Buat SPPD";

    public $nomor_surat, $kode_surat, $nomor_spd;
    public $id, $user_id, $pegawai_id, $departemen_id, $kegiatan_spd, $angkutan, $berangakat = "Ternate", $tujuan;
    public $lama_pd = 1, $tanggal_berangakat, $tanggal_kembali, $keterangan, $pejabat_ppk, $status_spd;
    public $kode_mak, $detail_alokasi_anggaran;

    public $sumber_dana, $instansi = "Dipa Universitas Khairun";

    public $tanggal_spd;

    public $mode = "add";

    public $readonly = "readonly";

    public $show_daftar_surat = false;

    public $riwayat_nomor_surat = [];

    public $tamu = 0;
    public $ppk_tamu, $nip_ppk;

    public function render()
    {
        if ($this->nomor_surat) {
            $this->update_nomor_sppd($this->nomor_surat);
        }
        return view('livewire.sppd.create');
    }

    public function show_modal_daftar_surat()
    {
        $this->show_daftar_surat = true;
        $this->dispatch('open-modal', modal: 'ModalDaftarSurat');
    }

    public function close_modal_daftar_surat()
    {
        $this->show_daftar_surat = false;
        $this->dispatch('close-modal', modal: 'ModalDaftarSurat');
    }

    #[On('generate-nomor-spd')]
    public function generate_nomor_spd($kodesurat_id)
    {
        // dd($kodesurat_id);
        $get = KodeSurat::where('id', $kodesurat_id)->first();

        $this->nomor_surat = '01';
        $kode = "UN44" . "/" . $get->kode;
        $tahun = date('Y');
        $jenis_surat = 'spd';
        $keterangan = auth()->user()->name . ' membuat SPPD ' . $get->keterangan;

        // $riwayat = RiwayatNomorSurat::kode($kode)->tahun($tahun)->jenis($jenis_surat)->orderBy('nomor', 'DESC')->limit(1)->first();
        $riwayat = RiwayatNomorSurat::tahun($tahun)->jenis($jenis_surat)->orderBy('nomor', 'DESC')->limit(1)->first();
        if ($riwayat) {
            $urut = (int) abs($riwayat->nomor) + 1;
            $this->nomor_surat = ($urut < 10) ? '0' . $urut : $urut;
        }

        $this->riwayat_nomor_surat = [
            'nomor' => $this->nomor_surat,
            'kode' => $kode,
            'tahun' => $tahun,
            'jenis_surat' => $jenis_surat,
            'keterangan' => $keterangan
        ];

        $this->kode_surat = $kode . "/" . $tahun;
        $this->nomor_spd = $this->nomor_surat . "/" . $kode . "/" . $tahun;

        $this->readonly = "";
        $this->close_modal_daftar_surat();
    }

    public function update_nomor_sppd($nomor)
    {
        $this->nomor_spd = $nomor . "/" . $this->kode_surat;
    }

    public function pass_tanggal_kembali($value, $form = NULL)
    {
        if ($form == 'lama_pd') {
            $this->lama_pd = $value;
            if ($this->tanggal_berangakat) {
                if ($this->lama_pd == 1) {
                    $this->tanggal_kembali = $this->tanggal_berangakat;
                } else {
                    $lama_pd = $this->lama_pd - 1;
                    $this->tanggal_kembali = add_tanggal($this->tanggal_berangakat, $lama_pd);
                }
            }
        }

        if ($form == 'tanggal_berangakat') {
            $this->tanggal_berangakat = $value;
            if ($this->tanggal_berangakat) {
                if ($this->lama_pd == 1) {
                    $this->tanggal_kembali = $this->tanggal_berangakat;
                } else {
                    $lama_pd = $this->lama_pd - 1;
                    $this->tanggal_kembali = add_tanggal($this->tanggal_berangakat, $lama_pd);
                }
            }
        }
    }

    public function save()
    {
        // abort(403);
        $rules = [
            'nomor_surat' => 'required|numeric|regex:/^[0-9]+$/',
            'kode_surat' => 'required',
            // 'nomor_spd' => 'required|unique:app_surat_perjalanan_dinas,nomor_spd',

            // cek nomor_spd di table sppd dan std
            'nomor_spd' => 'required|unique:app_surat_perjalanan_dinas,nomor_spd|unique:app_surat_tugas_dinas,nomor_std',

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
        ];

        if ($this->tamu == 1) {
            $rules += ['ppk_tamu' => 'required'];
        }

        $this->validate($rules);

        $sppd = SuratPerjalananDinas::create([
            'user_id' => auth()->user()->id,
            'nomor_spd' => $this->nomor_spd,
            'pegawai_id' => $this->pegawai_id,
            'departemen_id' => $this->departemen_id,
            'kegiatan_spd' => $this->kegiatan_spd,
            'angkutan' => ucwords(strtolower($this->angkutan)),
            'berangakat' => ucwords(strtolower($this->berangakat)),
            'tujuan' => ucwords(strtolower($this->tujuan)),
            'lama_pd' => $this->lama_pd,
            'tanggal_berangakat' => $this->tanggal_berangakat,
            'tanggal_kembali' => $this->tanggal_kembali,
            'keterangan' => $this->keterangan,
            'kode_mak' => $this->kode_mak,
            'tanggal_spd' => $this->tanggal_spd,
            'detail_alokasi_anggaran' => $this->detail_alokasi_anggaran,
            'sumber_dana' => $this->sumber_dana,
            'instansi' => $this->instansi,
            'status_spd' => '102', // 102 status spd baru di diajukan ke ppk,

            'tamu' => $this->tamu,
            'ppk_tamu' => $this->tamu ? json_encode(['ppk_tamu' => $this->ppk_tamu, 'nip_ppk' => $this->nip_ppk]) : NULL,
        ]);

        $sppd_id = $sppd->id->toString();

        // dd($sppd_id);

        // simpan riwayat nomor surat
        $this->simpan_riwayat_nomor_surat($sppd_id);

        $this->_clear_form();

        $this->dispatch('alert', type: 'success', title: 'Successfuly', message: 'SPPD Berhasil Dibuat.');
    }

    public function simpan_riwayat_nomor_surat($sppd_id)
    {
        $value = $this->riwayat_nomor_surat;
        if (trim($this->nomor_surat) != trim($this->riwayat_nomor_surat['nomor'])) {
            $value = [
                'nomor' => $this->nomor_surat,
                'kode' => $this->riwayat_nomor_surat['kode'],
                'tahun' => $this->riwayat_nomor_surat['tahun'],
                'jenis_surat' => $this->riwayat_nomor_surat['jenis_surat'],
                'keterangan' => $this->riwayat_nomor_surat['keterangan'],
            ];
        }
        $values = array_merge($value, ['surat_id' => $sppd_id]);
        RiwayatNomorSurat::create($values);
    }

    public function _clear_form()
    {
        $this->resetErrorBag();

        $this->id = "";
        $this->nomor_spd = "";
        $this->pegawai_id = "";
        $this->departemen_id = "";
        $this->kegiatan_spd = "";
        $this->angkutan = "";
        $this->berangakat = "";
        $this->tujuan = "";
        $this->lama_pd = "";
        $this->tanggal_berangakat = "";
        $this->tanggal_kembali = "";
        $this->keterangan = "";
        $this->status_spd = "";
        $this->tanggal_spd = "";

        $this->nomor_surat = "";
        $this->kode_surat = "";

        $this->sumber_dana = "";
        $this->instansi = "";

        $this->readonly = "readonly";

        $this->show_daftar_surat = false;
        $this->riwayat_nomor_surat = [];

        $this->tamu = 0;
        $this->ppk_tamu = "";
        $this->nip_ppk = "";
    }
}
