<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratTugasDinas;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Str;

class ReviewStdController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:review-st']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tahun = tahun();
            $pimpinan = auth()->user()->pj_pimpinan()->first();
            $pimpinan_id = '-';
            if ($pimpinan) {
                $pimpinan_id = $pimpinan->id;
            }

            $listdata = SuratTugasDinas::with(['departemen', 'pegawai'])->tahun($tahun)->status_std(['102', '200'])->pimpinan_id($pimpinan_id)
                ->select([
                    'app_surat_tugas_dinas.id',
                    'app_surat_tugas_dinas.nomor_std',
                    'app_surat_tugas_dinas.kegiatan_std',
                    'app_surat_tugas_dinas.tanggal_std',
                    'app_surat_tugas_dinas.tanggal_mulai_tugas',
                    'app_surat_tugas_dinas.tanggal_selesai_tugas',
                    'app_surat_tugas_dinas.departemen_id',
                    'app_surat_tugas_dinas.status_std',
                ])
                ->orderByRaw("FIELD(status_std , '102', '200') ASC")
                ->orderBy('app_surat_tugas_dinas.tanggal_std', 'DESC');
            return DataTables::eloquent($listdata)
                ->addIndexColumn()
                ->editColumn('action', function ($row) {
                    $detail = "detail('" . encode_arr(['stugas_id' => $row->id]) . "')";

                    $btnReview = '<button type="button" class="btn btn-sm btn-primary disabled"><i class="fa fa-search"></i> Review</button>';
                    if ($row->status_std == '102') {
                        $review = "review('" . encode_arr(['stugas_id' => $row->id]) . "')";
                        $btnReview = '<button type="button" onclick="' . $review . '" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Review</button>';
                    }

                    $actionBtn = '
                    <center>
                        <button type="button" onclick="' . $detail . '" class="btn btn-sm btn-info"><i class="fa fa-info-circle"></i></button>
                        ' . $btnReview . '
                    </center>';
                    return $actionBtn;
                })
                ->editColumn('nomor_std', function ($row) {
                    $detail = "detail('" . encode_arr(['stugas_id' => $row->id]) . "')";
                    $str = '<ul class="list-group list-group-flush">';
                    $str .= '<li class="list-group-item p-0">' . (Str::limit($row->kegiatan_std, 60, ' ...')) . '</li>';
                    $str .= '<li class="list-group-item p-0"><a href="#" onclick="' . $detail . '" class="">' . $row->nomor_std . '</a></li>';
                    $str .= '</ul>';
                    return $str;
                })
                ->editColumn('tanggal_std', function ($row) {
                    $str = tgl_indo($row->tanggal_std, false);
                    return $str;
                })
                ->editColumn('pegawai', function ($row) {
                    $str = '<ul class="list-group list-group-flush">';
                    foreach ($row->pegawai as $index => $r) {
                        if (count($row->pegawai) == 1) {
                            $str .= '<li class="list-group-item p-0">' . $r->nama_pegawai . '</li>';
                            break;
                        }

                        $nomor = $index + 1;
                        if ($nomor <= 3) {
                            $str .= '<li class="list-group-item p-0">' . $nomor . '. ' . $r->nama_pegawai . '</li>';
                        } else {
                            $str .= '<li class="list-group-item p-0 text-muted">&nbsp;... </li>';
                            break;
                        }
                    }
                    $str .= '</ul>';
                    return  $str;
                })
                ->editColumn('departemen', function ($row) {
                    return $row->departemen->departemen ?? '-';
                })
                ->editColumn('status', function ($row) {
                    return str_status_std($row->status_std);
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
                ->rawColumns(['nomor_std', 'pegawai', 'tanggal_std', 'departemen', 'status', 'action'])
                ->make(true);
        }

        $data = [
            'judul' => 'Daftar Pengajuan STD',
            'datatable' => [
                'url' => route('admin.std.review'),
                'id_table' => 'id-datatable',
                'columns' => [
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'nomor_std', 'name' => 'nomor_std', 'orderable' => 'false', 'searchable' => 'true'],
                    ['data' => 'tanggal_std', 'name' => 'tanggal_std', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'pegawai', 'name' => 'pegawai', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'departemen', 'name' => 'departemen', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'status', 'name' => 'status', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'action', 'name' => 'action', 'orderable' => 'false', 'searchable' => 'false']
                ]
            ]
        ];

        return view('backend.admin.std.review', $data);
    }
}
