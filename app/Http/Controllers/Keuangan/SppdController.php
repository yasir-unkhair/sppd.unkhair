<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\SuratPerjalananDinas;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class SppdController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:keuangan']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $listdata = SuratPerjalananDinas::join('app_pegawai', 'app_surat_perjalanan_dinas.pegawai_id', '=', 'app_pegawai.id')->status_spd(['200'])->tahun(tahun())
                ->select([
                    'app_surat_perjalanan_dinas.id',
                    'app_surat_perjalanan_dinas.nomor_spd',
                    'app_surat_perjalanan_dinas.kegiatan_spd',
                    'app_surat_perjalanan_dinas.tujuan',
                    'app_surat_perjalanan_dinas.tanggal_berangakat',
                    'app_surat_perjalanan_dinas.tanggal_kembali',
                    'app_surat_perjalanan_dinas.nilai_pencairan',
                    'app_pegawai.nama_pegawai'
                ])
                ->orderBy('app_surat_perjalanan_dinas.created_at', 'ASC')
                ->orderBy('app_surat_perjalanan_dinas.nomor_spd', 'ASC');
            return DataTables::of($listdata)
                ->addIndexColumn()
                ->editColumn('action', function ($row) {
                    $onclick = "input_np('" . encode_arr(['sppd_id' => $row->id]) . "')";
                    $warna_btn = $row->nilai_pencairan ? 'btn-warning' : 'btn-primary';
                    $actionBtn = '
                    <center>
                        <button type="button" onclick="' . $onclick . '" class="btn btn-sm ' . $warna_btn . '"><i class="fa fa-pencil"></i> Nilai Pencairan</button>
                    </center>';
                    return $actionBtn;
                })
                ->editColumn('nomor_spd', function ($row) {
                    $str = '<ul class="list-group list-group-flush">';
                    $str .= '<li class="list-group-item p-0">' . (Str::limit($row->kegiatan_spd, 50, '...')) . '</li>';
                    $str .= '<li class="list-group-item p-0"><span class="text-primary">' . $row->nomor_spd . '</span></li>';
                    $str .= '</ul>';
                    return $str;
                })
                ->editColumn('tanggal_berangakat', function ($row) {
                    return str_tanggal_dinas($row->tanggal_berangakat, $row->tanggal_kembali);
                })
                ->editColumn('pegawai', function ($row) {
                    $str = $row->nama_pegawai;
                    return  $str;
                })
                ->editColumn('tujuan', function ($row) {
                    return $row->tujuan;
                })
                ->editColumn('nilai_pencairan', function ($row) {
                    return 'Rp.' . (empty($row->nilai_pencairan) ? '0' : rupiah($row->nilai_pencairan));
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->input('search.value'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->input('search.value');
                            $w->orWhere('app_surat_perjalanan_dinas.nomor_spd', 'LIKE', "%$search%")
                                // ->orWhere('app_pegawai.nama_pegawai', 'LIKE', "%$search%")
                                ->orWhere('app_surat_perjalanan_dinas.kegiatan_spd', 'LIKE', "%$search%")
                                ->orWhere('app_surat_perjalanan_dinas.tujuan', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['nomor_spd', 'tanggal_berangakat', 'pegawai', 'tujuan', 'nilai_pencairan', 'action'])
                ->make(true);
        }

        $data = [
            'judul' => 'Daftar SPPD',
            'datatable' => [
                'url' => route('keuangan.sppd.index'),
                'id_table' => 'id-datatable',
                'columns' => [
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'nomor_spd', 'name' => 'nomor_spd', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'tanggal_berangakat', 'name' => 'tanggal_berangakat', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'tujuan', 'name' => 'tujuan', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'pegawai', 'name' => 'pegawai', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'nilai_pencairan', 'name' => 'nilai_pencairan', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'action', 'name' => 'action', 'orderable' => 'false', 'searchable' => 'false']
                ]
            ]
        ];

        return view('backend.keuangan.sppd-index', $data);
    }
}
