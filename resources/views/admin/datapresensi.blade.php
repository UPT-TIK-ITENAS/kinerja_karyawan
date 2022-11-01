@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-lg-6">
                <h3>Master Data Presensi</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Sistem Laporan Presensi Karyawan</li>
                    <li class="breadcrumb-item active">Master Data Presensi</li>
                </ol>
            </div>
        </div>
    </div>
</div>
    <div class="container-fluid">
        <div class="row">
            <!-- Zero Configuration  Starts-->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <p><b>Sinkronisasi Mesin Sidik Jari</b> </p>
                        <form action="{{ route('admin.SyncAndInsertBiometric') }}" method="POST">
                            @csrf
                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label">Tanggal</label>
                                <div class="col-xl-2">
                                    <div class="input-group">
                                        <input class="datepicker-here form-control digits" id="tanggal" name="tanggal"
                                            type="text" data-language="en" required>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6">
                                    <button class="btn btn-outline-success-2x" type="button"  id="clear"><i class="fa fa-refresh"></i> Sinkron</button>
                                </div>
                            </div>
                        <form>
                        <hr>
                        <form>
                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label">Filter</label>
                                <div class="col-xl-3">
                                    <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible" name="filter1" id="filter1" required="">
                                        <option selected="" disabled="" value=""> Pilih Nama </option>
                                        @foreach ($user as $u)
                                            <option value="{{ $u->nopeg }}">{{ $u->nopeg }} - {{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-2">
                                    <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible" name="filter2" id="filter2" required="">
                                        <option selected="" disabled="" value="">Pilih Tanggal </option>
                                        @foreach ($attendance as $a)
                                            <option value="{{ $a->tanggal }}">{{ $a->tanggal }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6">
                                    <button class="btn btn-outline-danger txt-red" type="button"  id="clear"><i class="icofont icofont-ui-delete"></i> Hapus</button>
                                    {{-- <button class="btn btn-danger" type="submit" id="clear">Hapus</button> --}}
                                </div>
                            </div>
                        <form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive"> 
                            <table class="dataTable" id="table-admin">
                                <thead>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Hari</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Siang</th>
                                    <th>Jam Keluar</th>
                                    <th>Durasi</th>
                                    <th>Telat Masuk</th>
                                    <th>Telat Siang</th>
                                    <th>Pulang cepat</th>
                                    <th>Aksi</th>
                                    <th>File</th>
                                    <th>Status</th>
                                </thead>
                                <tbody>
                 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    <script>
        $().ready(function() {
            let table = $('#table-admin').DataTable({
                fixedHeader: true,
                pageLength: 10,
                responsive: true,
                processing: true,
                autoWidth: false,
                serverSide: true,
                searching: false,
                columnDefs: [{
                    targets: 1,
                    width: "200px !important",
                }, ],
                ajax: {
                    url: "{{ route('admin.listkaryawan') }}",
                    data: function(d) {
                        d.filter1 = $('#filter1').val() ? $('#filter1').val() : '<>';
                        d.filter2 = $('#filter2').val() ? $('#filter2').val() : '<>';
                        // d.search = $('input[type="search"]').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'hari',
                        name: 'hari'
                    },
                    {
                        data: 'jam_masuk',
                        name: 'jam_masuk'
                    },
                    {
                        data: 'jam_siang',
                        name: 'jam_siang'
                    },
                    {
                        data: 'jam_pulang',
                        name: 'jam_pulang'
                    },
                    {
                        data: 'duration',
                        name: 'duration'
                    },
                    {
                        data: 'latemasuk',
                        name: 'latemasuk'
                    },
                    {
                        data: 'latesiang',
                        name: 'latesiang'
                    },
                    {
                        data: 'latesore',
                        name: 'latesore'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },

                    {
                        data: 'file',
                        name: 'file'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                ],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'print'
                ]
            });
            $("#clear").on('click', function(e) {
                e.preventDefault();
                // location.reload();
                $("#filter1").val('').trigger('change');
                $("#filter2").val('').trigger('change');
            });
            $("#filter1").on('change', function() {
                table.draw();
            });
            $("#filter2").on('change', function() {
                table.draw();
            });

            $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
                console.log(message);
            };
        });
    </script>
@endsection
