@extends('layouts.backend')

@section('content')
    @php
        $pengaturan = pengaturan();
    @endphp
    <div>
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content pl-2 pr-2">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $jml_pegawai }}</h3>

                                <p>PEGAWAI</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-ios-people"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $jml_departemen }}</h3>

                                <p>Departemen/Unit</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-home"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $jml_sppd }}</h3>

                                <p>SPPD {{ $tahun }}</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-ios-paper-outline"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $jml_stugas }}</h3>

                                <p>SURAT TUGAS {{ $tahun }}</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-ios-paper-outline"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->

                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center g-5">
                            <div class="col-lg-3">
                                <img src="{{ asset('images/dashboard.png') }}" class="img-fluid opacity-85" alt="images"
                                    loading="lazy">
                            </div>
                            <div class="col-lg-9 px-xl-5">
                                <h4 class="mb-2">
                                    Selamat datang <b>{{ Auth::user()->name }}</b> di {{ $pengaturan['nama-sub-aplikasi'] }}
                                    {{ $pengaturan['nama-departemen'] }}
                                </h4>
                                <p class="lead-dashboard mb-4">
                                    {{ $pengaturan['nama-sub-aplikasi'] }}
                                    merupakan sistem informasi yang dirancang khusus untuk mengelola data
                                    <span title="Surat Perintah Perjalanan Dinas">SPPD</span>
                                    dan <span title="Surat Tugas Dinas">STD</span>.
                                    Sehingga Universitas Khairun dapat menyediakan layanan yang lebih efektif dan efisien.
                                </p>
                                <div class="d-grid gap-3 d-md-flex justify-content-md-start">
                                    <livewire:auth.logout tampilan="logout2" />
                                    <livewire:auth.profile />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- statistik pengajuan sppd / std -->
                <div class="row">
                    <div class="col-md-5">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Statistik Usulan Departemen/Unit {{ $tahun }}</h3>

                                <div class="card-tools"></div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive p-0 mb-2">
                                    <table class="table table-condensed table-bordered"
                                        id="{{ $datatable_departemen['id_table'] }}">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Departemen</th>
                                                <th>SPPD</th>
                                                <th>STD</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card card-outline card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Statistik Usulan Pegawai {{ $tahun }}</h3>

                                <div class="card-tools"></div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive p-0 mb-2">
                                    <table class="table table-condensed table-bordered"
                                        id="{{ $datatable_pegawai['id_table'] }}">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Nama Pegawai</th>
                                                <th>Jabatan</th>
                                                <th>SPPD</th>
                                                <th>STD</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    @push('style')
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    @endpush

    @push('script')
        <script type="text/javascript">
            var table_departemen;
            var table_pegawai;
            $(function() {
                table_departemen = $("#{{ $datatable_departemen['id_table'] }}").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ $datatable_departemen['url'] }}",
                    },
                    columns: [
                        @foreach ($datatable_departemen['columns'] as $row)
                            {
                                data: "{{ $row['data'] }}",
                                name: "{{ $row['name'] }}",
                                orderable: {{ $row['orderable'] }},
                                searchable: {{ $row['searchable'] }}
                            },
                        @endforeach
                    ]
                });

                table_pegawai = $("#{{ $datatable_pegawai['id_table'] }}").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ $datatable_pegawai['url'] }}",
                    },
                    columns: [
                        @foreach ($datatable_pegawai['columns'] as $row)
                            {
                                data: "{{ $row['data'] }}",
                                name: "{{ $row['name'] }}",
                                orderable: {{ $row['orderable'] }},
                                searchable: {{ $row['searchable'] }}
                            },
                        @endforeach
                    ]
                });
            });
        </script>
    @endpush
@endsection
