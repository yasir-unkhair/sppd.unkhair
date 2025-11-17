<?php

namespace App\Livewire\Std;

use App\Models\KodeSurat;
use App\Models\Pimpinan;
use App\Models\RiwayatNomorSurat;
use App\Models\SuratTugasDinas;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public $judul;

    public $std_dk, $nomor_surat, $kode_surat, $nomor_std, $nomor_std_old;

    public $stugas_id, $departemen_id, $departemen, $kegiatan_std, $tanggal_mulai_tugas, $tanggal_selesai_tugas;
    public $keterangan, $pimpinan_ttd, $pimpinan_id, $status_std;
    public $kode_mak, $detail_alokasi_anggaran;

    public $kelengkapan_laporan_std = [], $tembusan_std = [];

    public $pegawai_id = [];

    public $tanggal_std;

    public $readonly = "readonly";

    public $fromSppd = false;

    public $pegawai_selected = [];

    public $riwayat_nomor_surat = [];

    public function mount($params, $judul)
    {
        $this->stugas_id = data_params($params, 'stugas_id');
        $this->judul = $judul;

        $get = SuratTugasDinas::with(['departemen', 'pegawai'])->where('id', $this->stugas_id)->first();
        $this->std_dk = $get->std_dk;
        $this->nomor_std = $get->nomor_std;
        $this->nomor_std_old = $get->nomor_std;

        $this->pecah_nomor_std($this->nomor_std);

        $this->tanggal_std = $get->tanggal_std;
        $this->departemen_id = $get->departemen_id;
        $this->departemen = $get->departemen->departemen;

        $this->kegiatan_std = $get->kegiatan_std;
        $this->tanggal_mulai_tugas = $get->tanggal_mulai_tugas;
        $this->tanggal_selesai_tugas = $get->tanggal_selesai_tugas;
        $this->keterangan = $get->keterangan;

        $this->kode_mak = $get->kode_mak;
        $this->detail_alokasi_anggaran = $get->detail_alokasi_anggaran;

        $get_kelengkapan_laporan_std = json_decode($get->kelengkapan_laporan_std, true);
        $get_tembusan_std = json_decode($get->tembusan_std, true);

        if ($get_kelengkapan_laporan_std) {
            foreach ($get_kelengkapan_laporan_std as $row) {
                $this->kelengkapan_laporan_std[] = $row['key'];
            }
        }

        if ($get_tembusan_std) {
            foreach ($get_tembusan_std as $row) {
                $this->tembusan_std[] = $row['key'];
            }
        }

        $this->status_std = $get->status_std;
        $this->pimpinan_ttd = get_datajson($get->pimpinan_ttd, 'id');

        $this->pimpinan_id = $get->pimpinan_id;

        foreach ($get->pegawai as $row) {
            $this->pegawai_id[] = $row->id;
            $this->pegawai_selected[] = [
                'id' => $row->id,
                'nama' => $row->nama_pegawai
            ];
        }

        if ($get->spd_id) {
            $this->fromSppd = true;
        }
    }

    public function render()
    {
        if ($this->nomor_surat) {
            $this->update_nomor_std($this->nomor_surat);
        }

        $pimpinan = Pimpinan::where('ppk', 0)->orderBy('nama_pimpinan', 'ASC')->get();
        if ($this->fromSppd) {
            return view('livewire.std.edit-from-sppd', ['pimpinan' => $pimpinan]);
        } else {
            return view('livewire.std.edit', ['pimpinan' => $pimpinan]);
        }
    }

    public function pecah_nomor_std($nomor_std)
    {
        $pecah = explode("/", $nomor_std);
        $this->nomor_surat = trim($pecah[0]);
        $this->kode_surat = trim($pecah[1]) . "/" . trim($pecah[2]) . "/" . trim($pecah[3]);

        $get = KodeSurat::where('kode', trim($pecah[2]))->first();
        $kode = "UN44" . "/" . trim($pecah[2]);
        $tahun = trim($pecah[3]);

        $jenis_surat = 'st';
        if ($this->std_dk) {
            $jenis_surat = 'std-dk';
        }

        $keterangan = auth()->user()->name . ' membuat STD ' . $get->keterangan;
        $this->riwayat_nomor_surat = [
            'nomor' => $this->nomor_surat,
            'kode' => $kode,
            'tahun' => $tahun,
            'jenis_surat' => $jenis_surat,
            'keterangan' => $keterangan
        ];
    }

    public function update_nomor_std($nomor)
    {
        $this->riwayat_nomor_surat['nomor'] = $nomor;
        $this->nomor_std = $nomor . "/" . $this->kode_surat;
    }

    public function save()
    {
        // abort(403);
        $rules = [
            'nomor_surat' => 'required|numeric|regex:/^[0-9]+$/',
            'kode_surat' => 'required',
            'nomor_std' => [
                'required',
                Rule::unique('app_surat_tugas_dinas')->where('std_dk', $this->std_dk)->ignore($this->stugas_id)
            ],
            'pegawai_id' => 'required|array|min:1',
            'departemen_id' => 'required',
            'kegiatan_std' => 'required',
            'tanggal_mulai_tugas' => 'required',
            'tanggal_selesai_tugas' => 'required',
            'pimpinan_ttd' => 'required',
            'tanggal_std' => 'required',
        ];

        $this->validate($rules);

        $kelengkapan_laporan_std = [];
        $tembusan_std = [];

        if ($this->kelengkapan_laporan_std) {
            foreach ($this->kelengkapan_laporan_std as $val) {
                $kelengkapan_laporan_std[] = [
                    'key' => $val,
                    'value' => kelengkapan_laporan_std($val)
                ];
            }
        }

        // dd($this->kelengkapan_laporan_std, $kelengkapan_laporan_std);

        if ($this->tembusan_std) {
            foreach ($this->tembusan_std as $val) {
                $tembusan_std[] = [
                    'key' => $val,
                    'value' => tembusan_std($val)
                ];
            }
        }

        $pimpinan_ttd = Pimpinan::where('id', $this->pimpinan_ttd)->select(['id', 'nama_pimpinan', 'nip', 'jabatan', 'detail_jabatan'])->first()->toArray();

        $this->pimpinan_id = $pimpinan_ttd['id'];

        $std = SuratTugasDinas::where('id', $this->stugas_id)->first();

        $values = [
            'nomor_std' => $this->nomor_std,
            'tanggal_std' => $this->tanggal_std,
            'departemen_id' => $this->departemen_id,
            'kegiatan_std' => $this->kegiatan_std,
            'tanggal_mulai_tugas' => $this->tanggal_mulai_tugas,
            'tanggal_selesai_tugas' => $this->tanggal_selesai_tugas,
            'pimpinan_ttd' => json_encode($pimpinan_ttd),
            'pimpinan_id' => $this->pimpinan_id,
            'keterangan' => $this->keterangan,

            'kode_mak' => $this->kode_mak,
            'detail_alokasi_anggaran' => $this->detail_alokasi_anggaran,

            'kelengkapan_laporan_std' => $kelengkapan_laporan_std ? json_encode($kelengkapan_laporan_std) : NULL,
            'tembusan_std' => $tembusan_std ? json_encode($tembusan_std) : NULL,
            'status_std' => $this->status_std,
        ];

        // dd($values);

        // remove daftar pegawai
        $std->pegawai()->sync([]);
        $std->update($values);

        // simpan daftar pegawai
        $std->pegawai()->sync($this->pegawai_id);

        $this->simpan_riwayat_nomor_surat($this->stugas_id);

        $this->_clear_form();
        $this->dispatch('alert', type: 'success', title: 'Successfuly', message: 'Surat Tugas Berhasil Diedit.');
    }

    public function simpan_riwayat_nomor_surat($std_id)
    {
        if ($this->nomor_std != $this->nomor_std_old) {
            $value = array_merge($this->riwayat_nomor_surat, ['surat_id' => $std_id]);
            RiwayatNomorSurat::create($value);
        }
    }

    public function _clear_form()
    {
        $this->resetErrorBag();
    }
}
