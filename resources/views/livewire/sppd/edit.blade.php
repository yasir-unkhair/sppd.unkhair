<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $judul }}</h3>

        <div class="card-tools"></div>
    </div>
    <form wire:submit="save">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3">
                                <label for="judul_konten">
                                    Nomor SPPD<sup class="text-danger">*</sup> :
                                </label>
                                <div class="row">
                                    <div class="col-sm-4 mr-0 pr-1">
                                        <input type="text" class="form-control" wire:model="nomor_surat"
                                            wire:model.live.debounce.400ms="nomor_surat">
                                    </div>
                                    <div class="col-sm-8 ml-0 pl-0">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text pl-1 pr-1">/</span>
                                            </div>
                                            <input type="text" class="form-control" wire:model="kode_surat"
                                                {{ $readonly }}>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" wire:model="nomor_spd">
                                <input type="hidden" wire:model="nomor_spd_old">
                                {{-- @dump($nomor_spd, $nomor_spd_old) --}}
                                @if ($errors->has('nomor_spd'))
                                    @error('nomor_spd')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @else
                                    @error('nomor_surat')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    @error('kode_surat')
                                        <br><small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-sm-6"></div>
                            <div class="col-sm-3">
                                <label for="judul_konten">
                                    Tanggal SPPD<sup class="text-danger">*</sup> :
                                </label>
                                <div class="input-group">
                                    <input type="date" class="form-control" wire:model="tanggal_spd">
                                    <div class="input-group-append">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                @error('tanggal_spd')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="judul_konten">
                            Maksud Kegiatan SPPD<sup class="text-danger">*</sup> :
                        </label>
                        <div class="input-group">
                            <textarea class="form-control" wire:model="kegiatan_spd" rows="3" placeholder="Isi maksud perjalanan dinas"></textarea>
                        </div>
                        @error('kegiatan_spd')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="judul_konten">
                            Transportasi<sup class="text-danger">*</sup> :
                        </label>
                        <div class="input-group w-25">
                            <select class="form-control" id="angkutan" wire:model="angkutan" style="width: 100%;">
                                <option value="">-- Pilih --</option>
                                @foreach (transportasi() as $key => $value)
                                    <option value="{{ $key }}" {{ $angkutan == $key ? 'selected' : '' }}>
                                        {{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('angkutan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="judul_konten">
                                    Tempat Berangkat<sup class="text-danger">*</sup> :
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model="berangakat"
                                        placeholder="Tempat berangkat">
                                </div>
                                @error('berangakat')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="judul_konten">
                                    Tempat Tujuan<sup class="text-danger">*</sup> :
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model="tujuan"
                                        placeholder="Tempat tujuan">
                                </div>
                                @error('tujuan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="judul_konten">
                                    Lama Perjalanan Dinas<sup class="text-danger">*</sup> :
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" wire:model="lama_pd"
                                        wire:change="pass_tanggal_kembali($event.target.value, 'lama_pd')">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-default">hari</span>
                                    </div>
                                </div>
                                @error('lama_pd')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-9"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="judul_konten">
                                    Tanggal Berangkat<sup class="text-danger">*</sup> :
                                </label>
                                <div class="input-group">
                                    <input type="date" class="form-control" wire:model="tanggal_berangakat"
                                        wire:change="pass_tanggal_kembali($event.target.value, 'tanggal_berangakat')" />
                                    <div class="input-group-append">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                @error('tanggal_berangakat')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="judul_konten">
                                    Tanggal Kembali<sup class="text-danger">*</sup> :
                                </label>
                                <div class="input-group">
                                    <input type="date" class="form-control" wire:model="tanggal_kembali">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-default">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                                @error('tanggal_kembali')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="judul_konten">
                            Pegawai<sup class="text-danger">*</sup> :
                        </label>
                        <div class="input-group" wire:ignore>
                            <select class="form-control" id="pegawai_id" wire:model="pegawai_id"
                                style="width: 100%;">
                                <option value="{{ $pegawai_id }}" selected="selected">{{ $nama_pegawai }}</option>
                            </select>
                        </div>

                        @error('pegawai_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="judul_konten">
                            Departemen/Unit<sup class="text-danger">*</sup> :
                        </label>
                        <div class="input-group" wire:ignore>
                            <select class="form-control" id="departemen_id" wire:model="departemen_id"
                                style="width: 100%;">
                                <option value="{{ $departemen_id }}" selected="selected">{{ $departemen }}
                                </option>
                            </select>
                        </div>

                        @error('departemen_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="judul_konten">
                                    Kode MAK<sup class="text-danger">*</sup> :
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model="kode_mak"
                                        placeholder="Kode MAK">
                                </div>
                                @error('kode_mak')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-9">
                                <label for="judul_konten">
                                    Detail Alokasi Anggaran :
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model="detail_alokasi_anggaran"
                                        placeholder="Detail Alokasi Anggaran">
                                </div>
                                @error('detail_alokasi_anggaran')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="judul_konten">
                            Instansi :
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" wire:model="instansi" placeholder="Instansi">
                        </div>
                        @error('instansi')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="judul_konten">
                            Sumber Dana :
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" wire:model="sumber_dana"
                                placeholder="Sumber Dana">
                        </div>
                        @error('sumber_dana')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="judul_konten">
                            Keterangan Lainnya :
                        </label>
                        <div class="input-group">
                            <textarea class="form-control" wire:model="keterangan" rows="3" placeholder="Isi keterangan tambahan"></textarea>
                        </div>
                        @error('keterangan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire.target="save"><i class="fa fa-save"></i> Simpan</span>
                <span wire:loading wire.target="save"><span class="spinner-border spinner-border-sm" role="status"
                        aria-hidden="true"></span> Please wait...</span>
            </button>

            <button type="button" class="btn btn-secondary float-right"
                onclick="location.href='{{ route('admin.sppd.index') }}'">
                <i class="fa fa-list"></i> Daftar SPPD
            </button>
        </div>
    </form>

    @push('style')
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('adminlte3') }}/plugins/select2/css/select2.min.css">
        <link rel="stylesheet"
            href="{{ asset('adminlte3') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    @endpush

    @push('script')
        <!-- Select2 -->
        <script src="{{ asset('adminlte3') }}/plugins/select2/js/select2.full.min.js"></script>

        <script>
            $(function() {
                //Initialize Select2 Elements
                $('#pegawai_id').select2({
                    theme: 'bootstrap4',
                    //minimumInputLength: 2,
                    minimumResultsForSearch: 10,
                    ajax: {
                        url: "{{ route('admin.pegawai.search-pegawai') }}",
                        dataType: 'json',
                        data: function(params) {
                            var query = {
                                search: params.term,
                                type: 'search-pegawai'
                            }

                            // Query parameters will be ?search=[term]&type=user_search
                            return query;
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        }
                    },
                    cache: true
                }).on('change', function(e) {
                    const selectedValues = $(this).val();
                    @this.set('pegawai_id', selectedValues);
                    console.log('change : ' + selectedValues);
                });

                $('#departemen_id').select2({
                    theme: 'bootstrap4',
                    //minimumInputLength: 2,
                    minimumResultsForSearch: 10,
                    ajax: {
                        url: "{{ route('admin.departemen.search-departemen') }}",
                        dataType: 'json',
                        data: function(params) {
                            var query = {
                                search: params.term,
                                type: 'search-departemen'
                            }

                            // Query parameters will be ?search=[term]&type=user_search
                            return query;
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        }
                    },
                    cache: true
                }).on('change', function(e) {
                    const selectedValues = $(this).val();
                    @this.set('departemen_id', selectedValues);
                });

            });
        </script>
    @endpush
</div>
