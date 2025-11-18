<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

if (!function_exists('roles')) {
    function roles()
    {
        $expire = Carbon::now()->addMinutes(300); // 5 menit
        $select = Cache::remember('roles', $expire, function () {
            return Role::select(['id', 'name'])->orderBy('created_at', 'ASC')->get();
        });
        return $select;
    }
}

if (!function_exists('my_roles')) {
    function my_roles()
    {
        return auth()->user()->roles()->get();
    }
}

if (!function_exists('tahun')) {
    function tahun()
    {
        return session('tahun') ?? date('Y');
    }
}

if (!function_exists('strip_tags_content')) {
    function strip_tags_content($string)
    {
        return strip_tags(html_entity_decode($string));
    }
}

if (!function_exists('tampil_aset')) {
    function tampil_aset($aset)
    {
        if (!trim($aset)) {
            return '';
        }

        $array = json_decode($aset);
        $str = '';
        if ($array) {
            foreach ($array as $r) {
                $baca = get_referensi($r);
                $str .= $baca . ", ";
            }
        }
        return $str ? rtrim($str, ", ") : '';
    }
}

if (!function_exists('kategori_pegawai')) {
    function kategori_pegawai($key = NULL)
    {
        $kategori = [
            'dosen-pns' => 'Dosen PNS',
            'dosen-kontrak' => 'Dosen Kontrak',
            'tendik-pns' => 'Tendik PNS',
            'tendik-kontrak' => 'Tendik Kontrak',
            'tamu-undangan' => 'Tamu Undangan',
            'mahasiswa' => 'Mahasiswa',
        ];

        if ($key && array_key_exists($key, $kategori)) {
            return $kategori[$key];
        }

        return $kategori;
    }
}

if (!function_exists('agama')) {
    function agama($key = NULL)
    {
        $agama = [
            'islam' => 'Islam',
            'kristen-protestan' => 'Kristen Protestan',
            'kristen-katolik' => 'Kristen Katolik',
            'hindu' => 'Hindu',
            'buddha' => 'Buddha',
            'konghucu' => 'Konghucu',
        ];

        if ($key && array_key_exists($key, $agama)) {
            return $agama[$key];
        }

        return $agama;
    }
}

if (!function_exists('str_status_sppd')) {
    function str_status_sppd($key = NULL)
    {
        $status = [
            '102' => '<span class="text-muted">Sedang Pengajuan</span>',
            '406' => '<span class="text-danger">Pengajuan Ditolak!</span>',
            '200' => '<span class="text-success">Pengajuan Disetujui</span>',
            '204' => '<span class="text-danger">Pengajuan Dihapus</span>',
            '409' => '<span class="text-danger">SPPD Dibatalkan!</span>',
        ];

        if ($status && array_key_exists($key, $status)) {
            return $status[$key];
        }

        return '';
    }
}

if (!function_exists('str_status_std')) {
    function str_status_std($key = NULL)
    {
        $status = [
            '102' => '<span class="text-warning">Belum Diverifikasi</span>',
            '406' => '<span class="text-danger">STD Ditolak!</span>',
            '200' => '<span class="text-success">Terverifikasi</span>',
            '204' => '<span class="text-danger">STD Dihapus</span>',
            '206' => '<span class="text-warning">STD Belum Lengkap</span>',
            '409' => '<span class="text-danger">STD Dibatalkan!</span>',
        ];

        if ($status && array_key_exists($key, $status)) {
            return $status[$key];
        }

        return '';
    }
}

if (!function_exists('get_datajson')) {
    function get_datajson($json, $key = NULL)
    {
        $json = json_decode($json, true);
        if ($json && array_key_exists($key, $json)) {
            return $json[$key];
        }
        return $json;
    }
}

if (!function_exists('str_role')) {
    function str_role($key = NULL)
    {
        $roles = [
            'developper' => 'Developper',
            'super-admin' => 'Super Admin',
            'admin-spd' => 'Admin SPD',
            'admin-st' => 'Admin STD',
            'admin-st-dk' => 'Admin STD Dalam Kota',
            'ppk' => 'PPK',
            'review-st' => 'Review STD',
            'keuangan' => 'Keuangan',
            'kepegawaian' => 'Kepegawaian',
        ];

        if ($roles && array_key_exists($key, $roles)) {
            return $roles[$key];
        }

        return '';
    }
}

if (!function_exists('get_image')) {
    function get_image($path_image = NULL)
    {
        if (env('APP_ENV') == 'local') {
            return $path_image;
        }

        // $type = pathinfo($path_image, PATHINFO_EXTENSION);
        // $data = file_get_contents($path_image);
        // return 'data:image/' . $type . ';base64,' . base64_encode($data);

        //$avatarUrl = 'https://assets-cdn.github.com/images/modules/logos_page/GitHub-Mark.png';

        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        $type = pathinfo($path_image, PATHINFO_EXTENSION);
        $avatarData = file_get_contents($path_image, false, stream_context_create($arrContextOptions));
        $avatarBase64Data = base64_encode($avatarData);
        return 'data:image/' . $type . ';base64,' . $avatarBase64Data;
    }
}

if (!function_exists('kelengkapan_laporan_std')) {
    function kelengkapan_laporan_std($key = NULL)
    {
        $arr = [
            [
                'key' => 'k0',
                'value' => 'Tiket PP di sertai boarding pass'
            ],
            [
                'key' => 'k1',
                'value' => 'Bukti pembayaran hotel selama kegiatan'
            ],
            [
                'key' => 'k2',
                'value' => 'Bukti pembayaran taxi PP'
            ]
        ];

        if ($key) {
            $return =  "";
            foreach ($arr as $row) {
                if ($row['key'] === $key) {
                    $return = $row['value'];
                    break;
                }
            }
            if ($return) {
                return $return;
            }
            return '-';
        }

        return $arr;
    }
}

if (!function_exists('tembusan_std')) {
    function tembusan_std($key = NULL)
    {
        $arr = [
            [
                'key' => 't0',
                'value' => 'Rektor Universitas Khairun'
            ],
            [
                'key' => 't1',
                'value' => 'Kepala KPPN Ternate'
            ],
            [
                'key' => 't2',
                'value' => 'Bendahara Universitas Khairun'
            ]
        ];

        if ($key) {
            $return =  "";
            foreach ($arr as $row) {
                if ($row['key'] === $key) {
                    $return = $row['value'];
                    break;
                }
            }
            if ($return) {
                return $return;
            }
            return '-';
        }

        return $arr;
    }
}

if (!function_exists('searcharray')) {
    function searcharray($val, $kolom, $array)
    {
        $key = array_search($val, array_column($array, $kolom));
        return ($key === FALSE) ? FALSE : TRUE;
    }
}

if (!function_exists('transportasi')) {
    function transportasi($key = NULL)
    {
        $transportasi = [
            'Pesawat' => 'Pesawat',
            'Kereta Api' => 'Kereta Api',
            'Kapal Feri' => 'Kapal Feri',
            'Speedboad' => 'Speedboad',
            'Bus' => 'Bus',
            'Mobil Pribadi/Dinas' => 'Mobil Pribadi/Dinas',
            'Ojek/Taksi Online' => 'Ojek/Taksi Online',
            'Sepeda Motor' => 'Sepeda Motor',
            'Transportasi Umum' => 'Transportasi Umum',
        ];

        if ($transportasi && array_key_exists($key, $transportasi)) {
            return $transportasi[$key];
        }

        return $transportasi;
    }
}
