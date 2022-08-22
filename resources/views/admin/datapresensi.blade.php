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
                        <h5>Monitoring Kehadiran </h5>
                        <span>Daftar hasil monitoring kehadiran karyawan terhitung dari tanggal 01 July 2021</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="dataTable" id="table-admin">
                                <thead>
                                    <th>No.</th>
                                    <th>NIP</th>
                                    <th>Hari</th>
                                    <th>Awal Tugas</th>
                                    <th>Akhir Tugas</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Siang</th>
                                    <th>Jam Keluar</th>
                                    <th>Durasi</th>
                                    <th>Telat Masuk</th>
                                    <th>Telat Siang</th>
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
                {   data: 'nip', name: 'nip' },
                {   data: 'hari', name: 'hari' },
                {   data: 'awal_tugas', name: 'awal_tugas' },
                {   data: 'akhir_tugas', name: 'akhir_tugas' },
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

        
    </script>
@endsection
