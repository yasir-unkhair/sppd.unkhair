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
                        <fieldset class="border p-2 mb-3 shadow-sm">
                            <legend class="float-none w-auto p-2">Filter Data</legend>
                            <form class="form-horizontal ml-2">
                                <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">
                                        Tanggal STD
                                    </label>
                                    <div class="col-sm-2">
                                        <input type="date" class="form-control" name="tanggal_awal" id="tanggal_awal" />
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control" name="tanggal_akhir"
                                            id="tanggal_akhir" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">
                                        Departemen/Unit
                                    </label>
                                    <div class="col-sm-4">
                                        <select class="form-control" id="departemen_id" name="departemen_id"
                                            style="width: 100%;">
                                            <option value="">-- All --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-3">
                                        <button type="button" id="btn-tampilkan" class="btn btn-block btn-primary">
                                            <i class="fa fa-search"></i> Tampilkan
                                        </button>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default">Export</button>
                                            <button type="button" class="btn btn-default dropdown-toggle dropdown-icon"
                                                data-toggle="dropdown">
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu" role="menu">
                                                <a class="dropdown-item disabled" href="#" id="export-excel"
                                                    target="_blank">Export Excel</a>
                                                <a class="dropdown-item disabled" href="#" id="export-pdf"
                                                    target="_blank">Export PDF</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </fieldset>

                        <div class="table-responsive p-0 mb-2">
                            <table class="table table-condensed table-bordered table-sm" id="{{ $datatable2['id_table'] }}">
                                <thead class="warna-warning">
                                    <tr>
                                        <th class="text-left" style="width:3%;">#</th>
                                        <th class="text-left">Kegiatan STD</th>
                                        <th class="text-left">Tanggal STD</th>
                                        <th class="text-left">Pegawai</th>
                                        <th class="text-left">Departemen/Unit</th>
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

        @push('style')
            <!-- Select2 -->
            <link rel="stylesheet" href="{{ asset('adminlte3') }}/plugins/select2/css/select2.min.css">
            <link rel="stylesheet" href="{{ asset('adminlte3') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
        @endpush

        @push('script')
            <!-- Select2 -->
            <script src="{{ asset('adminlte3') }}/plugins/select2/js/select2.full.min.js"></script>

            <script>
                var table;
                $(function() {
                    //Initialize Select2 Elements
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
                                return query;
                            },
                            processResults: function(data) {
                                return {
                                    results: data
                                };
                            }
                        },
                        cache: true
                    });

                    table = $("#{{ $datatable2['id_table'] }}").DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ $datatable2['url'] }}",
                            data: function(d) {
                                d.tanggal_awal = $('#tanggal_awal').val(),
                                    d.tanggal_akhir = $('#tanggal_akhir').val(),
                                    d.departemen_id = $('#departemen_id').val()
                            }
                        },
                        columns: [
                            @foreach ($datatable2['columns'] as $row)
                                {
                                    data: "{{ $row['data'] }}",
                                    name: "{{ $row['name'] }}",
                                    orderable: {{ $row['orderable'] }},
                                    searchable: {{ $row['searchable'] }}
                                },
                            @endforeach
                        ]
                    });

                    $('#btn-tampilkan').on('click', function() {
                        //alert('moco');
                        table.draw();

                        var tanggal_awal = $('#tanggal_awal').val();
                        var tanggal_akhir = $('#tanggal_akhir').val();
                        var departemen_id = $('#departemen_id').val();

                        var params = '';
                        if (tanggal_awal && tanggal_akhir) {
                            params += 'date=' + tanggal_awal + ' to ' + tanggal_akhir;
                        }

                        if (departemen_id) {
                            if (params) {
                                params += '&';
                            }
                            params += 'departemen_id=' + departemen_id;
                        }

                        var export_excel = "{{ route('kepegawaian.std.export.excel') }}?" + params;
                        //alert(export_excel);
                        $('#export-excel').attr("href", export_excel).removeClass("disabled");
                    });

                });

                function detail(params) {
                    Livewire.dispatch('detail-std', {
                        params: params
                    });
                }
            </script>
        @endpush

    </div>
@endsection
