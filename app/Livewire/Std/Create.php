<?php

namespace App\Livewire\Std;

use App\Models\KodeSurat;
use App\Models\Pimpinan;
use App\Models\RiwayatNomorSurat;
use App\Models\SuratPerjalananDinas;
use App\Models\SuratTugasDinas;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class Create extends Component
{
    public $judul = "Buat Surat Tugas";

    public $nomor_surat, $kode_surat, $nomor_std;
    public $id, $spd_id, $user_id, $pegawai_id = [], $departemen_id, $departemen, $kegiatan_std, $tanggal_mulai_tugas, $tanggal_selesai_tugas;
    public $keterangan, $pimpinan_ttd, $pimpinan_id, $status_std = '102';

    public $kode_mak = "", $detail_alokasi_anggaran = "";

    public $kelengkapan_laporan_std = [];
    public $tembusan_std = [];

    public $tanggal_std;

    public $readonly = "readonly";

    public $nama_pegawai;

    public $show_daftar_surat = false;

    public $riwayat_nomor_surat = [];


    public function mount($params = NULL)
    {
        if ($params) {
            $this->spd_id = data_params($params, 'spd_id');

            $get = SuratPerjalananDinas::with(['departemen', 'pegawai'])->where('id', $this->spd_id)
                ->select(['id', 'departemen_id', 'pegawai_id', 'kegiatan_spd', 'tanggal_berangakat', 'tanggal_kembali'])->first();
            $this->departemen_id = $get->departemen_id;
            $this->departemen = $get->departemen->departemen;
            $this->kegiatan_std = $get->kegiatan_spd;
            $this->tanggal_mulai_tugas = $get->tanggal_berangakat;
            $this->tanggal_selesai_tugas = $get->tanggal_kembali;
            $this->pegawai_id[] = $get->pegawai_id;
            $this->nama_pegawai = $get->pegawai->nama_pegawai;
        }
    }

    public function render()
    {
        $pimpinan = Pimpinan::where('ppk', 0)->orderBy('nama_pimpinan', 'ASC')->get();
        if ($this->spd_id) {
            return view('livewire.std.create-from-sppd', ['pimpinan' => $pimpinan]);
        } else {
            return view('livewire.std.create', ['pimpinan' => $pimpinan]);
        }
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

    #[On('generate-nomor-std')]
    public function generate_nomor_std($kodesurat_id)
    {
        // dd($kodesurat_id);
        $get = KodeSurat::where('id', $kodesurat_id)->first();

        $this->nomor_surat = '01';
        $kode = "UN44" . "/" . $get->kode;
        $tahun = date('Y');
        $jenis_surat = 'std-dk';
        $keterangan = auth()->user()->name . ' membuat surat ' . $get->keterangan;

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
        $this->nomor_std = $this->nomor_surat . "/" . $kode . "/" . $tahun;

        $this->readonly = "";
        $this->close_modal_daftar_surat();
    }

    public function save()
    {
        // abort(403);

        $this->validate([
            'nomor_surat' => 'required|numeric|regex:/^[0-9]+$/',
            'kode_surat' => 'required',
            'nomor_std' => [
                'required',
                Rule::unique('app_surat_tugas_dinas')->where('std_dk', 1)
            ],
            'pegawai_id' => 'required|array|min:1',
            'departemen_id' => 'required',
            'kegiatan_std' => 'required',
            'tanggal_mulai_tugas' => 'required',
            'tanggal_selesai_tugas' => 'required',
            'pimpinan_ttd' => 'required',
            'tanggal_std' => 'required',
        ]);

        $kelengkapan_laporan_std = [];
        $tembusan_std = [];

        if ($this->kelengkapan_laporan_std) {
            foreach ($this->kelengkapan_laporan_std as $index => $val) {
                $kelengkapan_laporan_std[] = [
                    'key' => $val,
                    'value' => kelengkapan_laporan_std($val)
                ];
            }
        }

        if ($this->tembusan_std) {
            foreach ($this->tembusan_std as $index => $val) {
                $tembusan_std[] = [
                    'key' => $val,
                    'value' => tembusan_std($val)
                ];
            }
        }

        $pimpinan_ttd = Pimpinan::where('id', $this->pimpinan_ttd)->select(['id', 'nama_pimpinan', 'nip', 'jabatan', 'detail_jabatan'])->first()->toArray();
        $this->pimpinan_id = $pimpinan_ttd['id'];

        $std = SuratTugasDinas::create([
            'user_id' => auth()->user()->id,
            'spd_id' => $this->spd_id,
            'std_dk' => 1,
            'nomor_std' => $this->nomor_std,
            'tanggal_std' => $this->tanggal_std,
            'departemen_id' => $this->departemen_id,
            'kegiatan_std' => $this->kegiatan_std,
            'tanggal_mulai_tugas' => $this->tanggal_mulai_tugas,
            'tanggal_selesai_tugas' => $this->tanggal_selesai_tugas,
            'pimpinan_ttd' => json_encode($pimpinan_ttd),
            'pimpinan_id' => $this->pimpinan_id,

            'kode_mak' => $this->kode_mak,
            'detail_alokasi_anggaran' => $this->detail_alokasi_anggaran,

            'keterangan' => $this->keterangan,
            'kelengkapan_laporan_std' => json_encode($kelengkapan_laporan_std),
            'tembusan_std' => json_encode($tembusan_std),
            'status_std' => $this->status_std,
        ]);

        $this->id = $std->id->toString();

        // simpan daftar pegawai
        $std->pegawai()->sync($this->pegawai_id);

        // simpan riwayat nomor surat
        $this->simpan_riwayat_nomor_surat($this->id);

        $this->_clear_form();

        $this->dispatch('alert', type: 'success', title: 'Successfuly', message: 'Surat Tugas Berhasil Dibuat.');
    }

    public function simpan_riwayat_nomor_surat($std_id)
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
        $values = array_merge($value, ['surat_id' => $std_id]);
        RiwayatNomorSurat::create($values);
    }

    public function _clear_form()
    {
        $this->resetErrorBag();

        $this->readonly = "readonly";
        $this->show_daftar_surat = false;
        $this->riwayat_nomor_surat = [];
        $this->kelengkapan_laporan_std = [];
        $this->tembusan_std = [];
    }
}
