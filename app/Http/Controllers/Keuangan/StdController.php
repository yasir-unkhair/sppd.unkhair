<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratTugasDinas;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class StdController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['role:keuangan']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $std_dk = 0;
            $listdata = SuratTugasDinas::with(['pegawai'])->dalam_kota($std_dk)->status_std(['200'])->tahun(tahun())
                ->select([
                    'app_surat_tugas_dinas.id',
                    DB::raw("SUBSTRING_INDEX(app_surat_tugas_dinas.nomor_std, '/', 1) AS nomor"),
                    'app_surat_tugas_dinas.nomor_std',
                    'app_surat_tugas_dinas.kegiatan_std',
                    'app_surat_tugas_dinas.tanggal_mulai_tugas',
                    'app_surat_tugas_dinas.tanggal_selesai_tugas',
                    'app_surat_tugas_dinas.nilai_pencairan',
                    'app_surat_tugas_dinas.status_std',
                ])
                ->orderBy('nomor', 'DESC')
                ->orderBy('app_surat_tugas_dinas.tanggal_std', 'DESC');
            return DataTables::of($listdata)
                ->addIndexColumn()
                ->editColumn('action', function ($row) {
                    $onclick = "input_np('" . encode_arr(['std_id' => $row->id]) . "')";
                    $warna_btn = $row->nilai_pencairan ? 'btn-warning' : 'btn-primary';
                    $actionBtn = '
                    <center>
                        <button type="button" onclick="' . $onclick . '" class="btn btn-sm ' . $warna_btn . '"><i class="fa fa-pencil"></i> Nilai Pencairan</button>
                    </center>';
                    return $actionBtn;
                })
                ->editColumn('nomor_std', function ($row) {
                    $str = '<ul class="list-group list-group-flush">';
                    $str .= '<li class="list-group-item p-0">' . (Str::limit($row->kegiatan_std, 50, '...')) . '</li>';
                    $str .= '<li class="list-group-item p-0"><span class="text-primary">' . $row->nomor_std . '</span></li>';
                    $str .= '</ul>';
                    return $str;
                })
                ->editColumn('tanggal_berangakat', function ($row) {
                    return str_tanggal_dinas($row->tanggal_mulai_tugas, $row->tanggal_selesai_tugas);
                })
                ->editColumn('pegawai', function ($row) {
                    $str = '-';
                    if ($row->pegawai) {
                        $str = $row->pegawai[0]->nama_pegawai;
                    }
                    return  $str;
                })
                ->editColumn('nilai_pencairan', function ($row) {
                    return 'Rp.' . (empty($row->nilai_pencairan) ? '0' : rupiah($row->nilai_pencairan));
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->input('search.value'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->input('search.value');
                            $w->orWhere('app_surat_tugas_dinas.nomor_std', 'LIKE', "%$search%")
                                ->orWhere('app_surat_tugas_dinas.kegiatan_std', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['nomor_std', 'tanggal_berangakat', 'pegawai', 'nilai_pencairan', 'action'])
                ->make(true);
        }

        $data = [
            'judul' => 'Daftar STD',
            'datatable' => [
                'url' => route('keuangan.std.index'),
                'id_table' => 'id-datatable',
                'columns' => [
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'nomor_std', 'name' => 'nomor_std', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'tanggal_berangakat', 'name' => 'tanggal_berangakat', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'pegawai', 'name' => 'pegawai', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'nilai_pencairan', 'name' => 'nilai_pencairan', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'action', 'name' => 'action', 'orderable' => 'false', 'searchable' => 'false']
                ]
            ]
        ];

        return view('backend.keuangan.std-index', $data);
    }
}
