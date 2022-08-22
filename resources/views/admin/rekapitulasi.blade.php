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
                        <h5>Daftar Hasil Rekapitulasi Presensi Karyawan</h5>
                        <span>Daftar hasil rekapitulasi presensi karyawan terhitung dari tanggal 01 Juli 2022</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="dataTable" id="table-admin">
                                <thead>
                                    <th width="5%">No.</th>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                    <th>Total Keterlambatan</th>
                                    <th>Total Izin</th>
                                    <th>Detail</th>
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
            ajax: "{{ route('admin.listrekapkaryawan') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: 'text-center',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'nopeg',
                    name: 'nopeg',
                    class: 'text-center',
                },
                {
                    data: 'name',
                    name: 'name'
                },

                {
                    data: 'duration',
                    name: 'duration'
                },
                {
                    data: 'izin',
                    name: 'izin'
                },
                {
                    data: 'detail',
                    name: 'detail'
                },
                

            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'print'
            ]
        });
    </script>
@endsection
