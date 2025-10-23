<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratTugasDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Str;

class StdController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin-st|admin-st-dk|admin-spd']);
    }

    public function index(Request $request)
    {
        $tahun = tahun();

        $std_dk = 0;
        if (auth()->user()->hasRole('admin-st-dk') && in_array(session('role'), ['admin-st-dk'])) {
            $std_dk = 1;
        }

        $listdata = SuratTugasDinas::with(['departemen', 'pegawai'])->dalam_kota($std_dk)->status_std(['200', '102', '409'])->tahun($tahun)
            ->select([
                'app_surat_tugas_dinas.id',
                DB::raw("SUBSTRING_INDEX(app_surat_tugas_dinas.nomor_std, '/', 1) AS nomor"),
                'app_surat_tugas_dinas.nomor_std',
                'app_surat_tugas_dinas.kegiatan_std',
                'app_surat_tugas_dinas.tanggal_std',
                'app_surat_tugas_dinas.departemen_id',
                'app_surat_tugas_dinas.status_std',
            ])
            // ->orderByRaw("FIELD(status_std , '102', '200') ASC")
            ->orderBy('nomor', 'DESC')
            ->orderBy('app_surat_tugas_dinas.tanggal_std', 'DESC')
            ->get();

        $data = [
            'judul' => 'Data STD',
            'results' => $listdata
        ];

        return view('backend.admin.std.index', $data);
    }

    public function create()
    {
        if (!auth()->user()->hasRole('admin-st-dk')) {
            abort(403);
        }

        $data = [
            'judul' => 'Buat STD',
        ];

        return view('backend.admin.std.create', $data);
    }

    public function stdfromsppd(Request $request)
    {
        if (!auth()->user()->hasRole('admin-st')) {
            abort(403);
        }

        $tahun = tahun();
        $listdata = SuratTugasDinas::with(['pegawai'])->status_std(['206'])->tahun($tahun)
            ->select([
                'app_surat_tugas_dinas.id',
                DB::raw("SUBSTRING_INDEX(app_surat_tugas_dinas.nomor_std, '/', 1) AS nomor"),
                'app_surat_tugas_dinas.nomor_std',
                'app_surat_tugas_dinas.kegiatan_std',
                'app_surat_tugas_dinas.tanggal_std',
                'app_surat_tugas_dinas.tanggal_mulai_tugas',
                'app_surat_tugas_dinas.tanggal_selesai_tugas',
                'app_surat_tugas_dinas.created_at',
            ])
            ->orderBy('nomor', 'DESC')
            ->orderBy('app_surat_tugas_dinas.tanggal_std', 'DESC')
            ->get();

        $data = [
            'judul' => 'Daftar STD Dari SPPD',
            'results' => $listdata
        ];

        return view('backend.admin.std.from-sppd', $data);
    }

    public function edit($params)
    {
        $data = [
            'judul' => 'Edit STD',
            'params' => $params
        ];

        return view('backend.admin.std.edit', $data);
    }

    public function Lengkapi($params)
    {
        if (!auth()->user()->hasRole(['admin-spd', 'admin-st'])) {
            abort(403);
        }

        $data = [
            'judul' => 'Lengkapi STD',
            'params' => $params
        ];

        return view('backend.admin.std.lengkapi', $data);
    }

    public function delete($params)
    {
        $std_id = data_params($params, 'std_id');
        SuratTugasDinas::where('id', $std_id)->update(['status_std' => '204']);
        alert()->success('Success', 'Data STD Berhasil Dihapus');
        return redirect(route('admin.std.index'));
    }
}
