@extends('layouts.backend')

@section('content')
    <div>
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $judul }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">{{ $judul }}</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content pl-2 pr-2">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $judul }}</h3>

                        <div class="card-tools"></div>
                    </div>
                    <div class="card-body">
                        @if (auth()->user()->hasRole('admin-st-dk') && in_array(session('role'), ['admin-st-dk']))
                            <button class="btn btn-sm btn-primary"
                                onclick="location.href='{{ route('admin.std.create') }}'">
                                <i class="fa fa-plus"></i> Buat STD
                            </button>
                        @endif

                        @if (auth()->user()->hasRole(['admin-st']) && in_array(session('role'), ['admin-st']))
                            <button class="btn btn-sm btn-danger"
                                onclick="location.href='{{ route('admin.std.fromSppd') }}'">
                                <i class="fa fa-list"></i> STD Dari SPPD
                            </button>
                        @endif

                        <div class="table-responsive p-0 mb-2">
                            <table class="table table-condensed table-sm table-bordered" style="width: 100%"
                                id="id-datatable">
                                <thead class="warna-warning">
                                    <tr>
                                        <th style="vertical-align: middle">#</th>
                                        <th class="text-left" style="vertical-align: middle">
                                            Kegiatan STD
                                        </th>
                                        <th class="text-left">Tanggal STD</th>
                                        <th class="text-left" style="vertical-align: middle">Pegawai</th>
                                        <th class="text-left" style="vertical-align: middle">
                                            Departemen/Unit
                                        </th>
                                        <th class="text-left">Status</th>
                                        <th style="vertical-align: middle; width: 9%;">
                                            <center>Aksi</center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <livewire:std.detail-std />

        <!-- /.content -->
        @push('script')
            <script>
                function detail(params) {
                    Livewire.dispatch('detail-std', {
                        params: params
                    });
                }

                function edit(params) {
                    return location.href = "{{ route('admin.std.edit', '') }}/" + params;
                }
            </script>
        @endpush
    </div>
@endsection
