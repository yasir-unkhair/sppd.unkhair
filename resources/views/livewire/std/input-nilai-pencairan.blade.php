<div>
    <div class="modal fade" wire:ignore.self id="{{ $modal }}" tabindex="-1" aria-labelledby="ModalUpdateRoleLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form wire:submit.prevent="save" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">{{ $judul }}</h5>
                        <button type="button" class="close" wire:click="_reset">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive mb-0 border">
                            <table class="table table-sm mb-0">
                                @if ($get)
                                    <tr>
                                        <th class="text-right warna-warning" width="15%">Nomor STD :</td>
                                        <td width="40%">
                                            {{ $get->nomor_std }}
                                        </td>

                                        <th class="text-right warna-warning" width="15%">Nama Pegawai :</td>
                                        <td width="30%">
                                            {{ $get->pegawai[0]->nama_pegawai }} <br>
                                            NIP: {{ $get->pegawai[0]->nip ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-right warna-warning">Perihal Kegiatan :</td>
                                        <td>{{ $get->kegiatan_std }}</td>

                                        <th class="text-right warna-warning">Departemen/Unit :</td>
                                        <td>{{ $get->departemen->departemen }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-right warna-warning">Tanggal Berangkat :</td>
                                        <td colspan="3">{{ tgl_indo($get->tanggal_mulai_tugas, false) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-right warna-warning">Tanggal Kembali :</td>
                                        <td colspan="3">{{ tgl_indo($get->tanggal_selesai_tugas, false) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-right warna-warning">Kode MAK :</td>
                                        <td colspan="3">{{ $get->kode_mak ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-right warna-warning">Detail Alokasi Anggaran :</td>
                                        <td colspan="3">{{ $get->detail_alokasi_anggaran ?? '' }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="4">&nbsp;</td>
                                </tr>
                                <tr>
                                    <th class="text-right warna-info">Nilai Pencairan :</td>
                                    <td colspan="3">
                                        <div class="input-group w-25">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-default">Rp.</span>
                                            </div>
                                            <input type="text" class="form-control" wire:model="nilai_pencairan"
                                                wire:model.live.debounce.400ms="nilai_pencairan">
                                        </div>
                                        @error('nilai_pencairan')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="_reset"><i
                                class="fa fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled"
                            wire:target="save">
                            <span wire:loading.remove wire.target="save"><i class="fa fa-save"></i> Save</span>
                            <span wire:loading wire.target="save"><span class="spinner-border spinner-border-sm"
                                    role="status" aria-hidden="true"></span> Please wait...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
