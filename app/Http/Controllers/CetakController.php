<?php

namespace App\Http\Controllers;

use App\Models\SuratPerjalananDinas;
use App\Models\SuratTugasDinas;
use Illuminate\Support\Facades\File;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CetakController extends Controller
{
    public function sppd($params)
    {
        $params = decode_arr($params);
        if (!$params) {
            abort(403);
        }

        $sppd = SuratPerjalananDinas::with(['pegawai', 'departemen'])->where('id', $params['sppd_id'])->first();
        if ($sppd->status_spd != '200') {
            abort(404);
        }

        $qrcode_name = $params['sppd_id'] . '.png';
        $qrcode_path = 'images/qrcode/';

        $lokasi_file = public_path($qrcode_path);
        if (!File::isDirectory($lokasi_file)) {
            File::makeDirectory($lokasi_file, 0755, true, true);
        }

        if (!file_exists($lokasi_file . $qrcode_name)) {
            // generate qrcode
            $file_path = $qrcode_path . $qrcode_name;
            $dt = route('frontend.verifikasi-sppd', encode_arr(['sppd_id' => $params['sppd_id']]));
            QrCode::size(512)
                ->format('png')
                ->merge(public_path('images/logo.png'), 0.2, true)
                ->errorCorrection('M')
                ->generate($dt, $file_path);
        }

        $data = ['sppd' => $sppd];
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ])->loadView('pdf.cetak-sppd', $data)->setPaper('a4', 'portrait');


        $judul = date('Ymd') . ' - ' . 'Surat Perjalanan Dinas' . '.pdf';

        //menampilkan output beupa halaman PDF
        return $pdf->stream($judul);
    }

    public function std($params)
    {
        $params = decode_arr($params);
        if (!$params) {
            abort(403);
        }

        $std = SuratTugasDinas::with(['pegawai', 'departemen'])->where('id', $params['stugas_id'])->first();

        if ($std->status_std != '200') {
            abort(404);
        }

        $qrcode_name = $params['stugas_id'] . '.png';
        $qrcode_path = 'images/qrcode/';

        $lokasi_file = public_path($qrcode_path);
        if (!File::isDirectory($lokasi_file)) {
            File::makeDirectory($lokasi_file, 0755, true, true);
        }

        // generate qrcode jika file belum ada dan sudah diverifikasi
        if (!file_exists($lokasi_file . $qrcode_name) && $std->reviewer_id) {
            // generate qrcode
            $file_path = $qrcode_path . $qrcode_name;
            $dt = route('frontend.verifikasi-std', encode_arr(['stugas_id' => $params['stugas_id']]));
            QrCode::size(512)
                ->format('png')
                ->merge(public_path('images/logo.png'), 0.2, true)
                ->errorCorrection('M')
                ->generate($dt, $file_path);
        }

        $data = ['std' => $std];
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ])->loadView('pdf.cetak-std', $data)->setPaper('a4', 'portrait');


        $judul = date('Ymd') . ' - ' . 'Surat Tugas Dinas' . '.pdf';

        //menampilkan output beupa halaman PDF
        return $pdf->stream($judul);
    }
}
