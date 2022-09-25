@extends('layouts.app')
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/date-picker.css') }}">
    <!-- Plugins css Ends-->

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
                        <h5>Monitoring Kehadiran </h5>
                        <span>Daftar hasil monitoring kehadiran karyawan terhitung dari tanggal 01 July 2021</span>
                        <hr>

                        <form action="{{ route('admin.SyncAndInsertBiometric') }}" method="POST">
                            @csrf
                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label">Tanggal</label>
                                <div class="col-xl-2">
                                <div class="input-group">
                                    <input class="datepicker-here form-control digits" id="tanggal" name="tanggal" type="text" data-language="en" required>
                                </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6">
                                    <button class="btn btn-secondary" type="submit">Sinkron</button>
                                </div>
                            </div>
                        <form>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="dataTable" id="table-admin">
                                <thead class="text-center">
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Hari</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Siang</th>
                                    <th>Jam Keluar</th>
                                    <th>Durasi</th>
                                    <th>Telat Masuk</th>
                                    <th>Telat Siang</th>
                                    <th>Telat Sore</th>
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
                columnDefs: [{
                    targets: 1,
                    width: "200px !important",
                }, ],
                ajax: "{{ route('admin.listkaryawan') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {   data: 'name', name: 'name' },
                    {   data: 'hari', name: 'hari' },
                    {   data: 'jam_masuk', name: 'jam_masuk' },
                    {   data: 'jam_siang', name: 'jam_siang' },
                    {   data: 'jam_pulang', name: 'jam_pulang' },
                    {   data: 'duration', name: 'duration' },
                    {   data: 'latemasuk', name: 'latemasuk'},
                    {   data: 'latesiang', name: 'latesiang'},
                    {   data: 'action', name: 'action'},
                    
                    { data: 'file', name: 'file'},
                    { data: 'status', name: 'status'},
                ],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'print'
                ]
            });
            $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
                    console.log(message);
            };
        });
        
    </script>
@endsection
