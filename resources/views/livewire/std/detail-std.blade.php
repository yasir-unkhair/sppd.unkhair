<div class="modal fade" wire:ignore.self id="{{ $modal }}" tabindex="-1" aria-labelledby="ModalUpdateRoleLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">{{ $judul }}</h5>
                <button type="button" class="close" wire:click="_reset">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-2">
                @if ($get)
                    <div class="text-right mb-4">
                        <b style="font-size:12px;">Dibuat Oleh : &nbsp;</b>
                        <span class="badge bg-cyan">
                            <i class="fa fa-user"></i>
                            {{ $get->user?->name }}
                        </span>
                        <span class="badge bg-cyan">
                            <i class="fa fa-clock"></i>
                            {{ tgl_indo($get->created_at) }}
                        </span>
                    </div>
                    <div class="table-responsive mb-0 border">
                        <table class="table table-sm mb-0">
                            <tr>
                                <th class="text-right warna-warning" width="15%">Nomor STD :</td>
                                <td width="40%">
                                    {{ $get->nomor_std }}
                                </td>
                            </tr>
                            <tr>
                                <th class="text-right warna-warning">Perihal Kegiatan :</td>
                                <td>{{ $get->kegiatan_std }}</td>
                            </tr>
                            <tr>
                                <th class="text-right warna-warning">Nama Pegawai :</td>
                                <td>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($get->pegawai as $r)
                                            @if (count($get->pegawai) == 1)
                                                <li class="list-group-item p-0">
                                                    {{ $r->nama_pegawai }}
                                                </li>
                                                @break
                                            @endif
                                            <li class="list-group-item p-0">
                                                {{ $loop->index + 1 }}. {{ $r->nama_pegawai }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-right warna-warning">Departemen/Unit :</td>
                                <td>{{ $get->departemen->departemen }}</td>
                            </tr>
                            <tr>
                                <th class="text-right warna-warning">Tanggal Dinas :</td>
                                <td>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item p-0">
                                            <b>Dimulai: &nbsp;</b> {{ tgl_indo($get->tanggal_mulai_tugas, false) }}
                                        </li>
                                        <li class="list-group-item p-0">
                                            <b>Selesai: &nbsp;</b>{{ tgl_indo($get->tanggal_selesai_tugas, false) }}
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-right warna-warning">Ttd. Pimpinan :</td>
                                <td>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item p-0">
                                            {{ get_datajson($get->pimpinan_ttd, 'nama_pimpinan') }}
                                        </li>
                                        <li class="list-group-item p-0">
                                            <b>Jabatan:
                                                &nbsp;</b>{{ get_datajson($get->pimpinan_ttd, 'detail_jabatan') }}
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-right warna-warning">Kode MAK :</td>
                                <td>{{ $get->kode_mak ?? '' }}</td>
                            </tr>
                            <tr>
                                <th class="text-right warna-warning">Detail Alokasi Anggaran :</td>
                                <td>{{ $get->detail_alokasi_anggaran ?? '' }}</td>
                            </tr>
                            <tr>
                                <th class="text-right warna-info">Diverifikasi Oleh :</td>
                                <td>
                                    {{ $get->reviwer?->name ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th class="text-right warna-info">Tanggal Verifikasi :</td>
                                <td>
                                    {{ tgl_indo($get->tanggal_review ?? '') }}
                                </td>
                            </tr>
                        </table>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" wire:click="_reset"><i class="fa fa-times"></i>
                    Close</button>
            </div>
        </div>
    </div>
</div>
