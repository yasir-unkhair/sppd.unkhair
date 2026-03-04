<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuratPerjalananDinas;
use Illuminate\Http\Request;

class SpdController extends Controller
{
    //
    public function index(Request $request)
    {
        $nomor_spd = $request->post('nomor_spd');
        $get = SuratPerjalananDinas::join('app_pegawai', 'app_surat_perjalanan_dinas.pegawai_id', '=', 'app_pegawai.id')->status_spd(['200'])->tahun(tahun())
            ->select([
                'app_surat_perjalanan_dinas.id',
                'app_surat_perjalanan_dinas.nomor_spd',
                'app_surat_perjalanan_dinas.kegiatan_spd',
                'app_surat_perjalanan_dinas.tujuan',
                'app_surat_perjalanan_dinas.tanggal_berangakat',
                'app_surat_perjalanan_dinas.tanggal_kembali',
                'app_pegawai.nama_pegawai',
                'app_pegawai.nip'
            ])->first();

        if (!$get) {
            $data = [
                'status' => false,
                'message' => 'Data Tidak Ditemukan!'
            ];
        } else {
            $data = [
                'status' => true,
                'message' => 'Data Ditemukan',
                'data' => $get
            ];
        }

        return response()->json($data);
    }
}
