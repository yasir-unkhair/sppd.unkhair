<!DOCTYPE html>
<html>

    <head>
        <style>
            body {
                font-family: Times;
            }

            .kolom {
                border: 1px solid black;
                text-align: left;
                vertical-align: top;
                padding: 4px;
            }

            .kolom2 {
                border: 1px solid black;
                text-align: left;
                vertical-align: top;
            }

            .wrapper-page {
                page-break-after: always;
            }

            .wrapper-page:last-child {
                page-break-after: avoid;
            }
        </style>
    </head>

    {{-- <body style="border:1px solid #000"> --}}

    <body>
        <div class="wrapper-page">
            <page_header>
                <table width="90%" align="center">
                    <tr>
                        <td width="20%">
                            <center>
                                <img src="{{ public_path('images/logo.jpg') }}" alt=""
                                    style="width:95px; height:85px;">
                            </center>
                        </td>
                        <td width="80%" style="text-align:center">
                            <span style="font-size:19px; font-weight:bold;">
                                KEMENTERIAN PENDIDIKAN, SAIN DAN TEKNOLOGI
                            </span>
                            <br>
                            <span style="font-size:19px; font-weight:bold;">UNIVERSITAS KHAIRUN</span> <br>
                            <span style="font-size:14px;">
                                Jalan Jusuf Abdurrahman Kampus Gambesi Kode Pos 97719 Ternate Selatan
                            </span> <br>
                            <span style="font-size:14px;">
                                Laman: <a href="https://www.unkhair.ac.id">www.unkhair.ac.id</a> / Email:
                                <u>admin@unkhair.ac.id</a>
                            </span>
                        </td>
                    </tr>
                </table>
                <hr>
            </page_header>

            <br>
            <center>
                <span style="font-size:14px;">
                    SURAT TUGAS DINAS (STD) <br>
                    Nomor &nbsp;:&nbsp;{{ $std->nomor_std }}
                </span>
            </center>
            <br>

            <p style="font-size:12px; text-align: justify">
                {{ get_datajson($std->pimpinan_ttd, 'detail_jabatan') }} Universitas Khairun memberikan tugas kepada:
            </p>

            <table width="100%" style="font-size:12px; border-collapse: collapse;">
                <tr>
                    <th width="5%" class="kolom">No</th>
                    <th width="30%" class="kolom">Nama / NIP</th>
                    <th width="25%" class="kolom">Pangkat / Golongan</th>
                    <th width="40%" class="kolom">Jabatan</th>
                </tr>
                @foreach ($std->pegawai as $row)
                    <tr>
                        <td class="kolom">{{ $loop->index + 1 }}</td>
                        <td class="kolom">
                            {{ $row->nama_pegawai }} <br>
                            NIP: {{ $row->nip ?? '-' }}
                        </td>
                        <td class="kolom">
                            -
                        </td>
                        <td class="kolom">
                            {{ $row->jabatan ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </table>

            <p style="font-size:12px; text-align: justify">
                Untuk melakukan {{ $std->kegiatan_std }}
                pada tanggal {{ str_tanggal_dinas($std->tanggal_mulai_tugas, $std->tanggal_selesai_tugas) }}.
                Setelah melaksanakan tugas harap saudara menyampaikan laporan hasil kegiatan kepada Pimpinan Universitas
            </p>


            <table width="100%" style="font-size:12px;">
                <tr>
                    <td width="60%" style="vertical-align: top">
                        {!! str_repeat('<br>', 10) !!}
                        Tembusan: <br>
                        1. Rektor Universitas Khairun <br>
                        2. Kepala KPPN Ternate <br>
                        3. Bendahara Universitas Khairun
                    </td>
                    <td width="40%" style="vertical-align: top">
                        <br>
                        Ternate, {{ tgl_indo(now(), false) }} <br>
                        a.n Rektor <br>
                        {{ get_datajson($std->pimpinan_ttd, 'jabatan') }}
                        {!! str_repeat('<br>', 5) !!}
                        {{ get_datajson($std->pimpinan_ttd, 'nama_pimpinan') }} <br>
                        NIP: {{ get_datajson($std->pimpinan_ttd, 'nip') }}
                    </td>
                </tr>
            </table>
        </div>
    </body>

</html>