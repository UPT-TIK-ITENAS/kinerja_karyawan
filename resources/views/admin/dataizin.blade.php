@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Pengajuan Izin</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Data Pengajuan Izin</li>
                        <li class="breadcrumb-item active">Data Izin</li>
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
                    <div class="card-body">
                        <a href="{{ route('admin.createizin') }}" class="btn btn-primary"><i class="icofont icofont-plus-square"></i> Tambah</a>
                        <div class="table-responsive"> 
                            <table class="dataTable" id="table-izin">
                                <thead>
                                    <th>No.</th>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                    <th>Unit</th>
                                    <th>Jenis Izin</th>
                                    <th>Tanggal Awal Izin</th>
                                    <th>Tanggal Akhir Izin</th>
                                    <th>Total Hari Izin</th>
                                    <th>Aksi</th>
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
        let table = $('#table-izin').DataTable({
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
            ajax: "{{ route('admin.listizin') }}",
            columns: [
                {    data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            className: 'text-center',
                            orderable: false,
                            searchable: false,
                },
                { data: 'nopeg', name: 'nopeg'},
                { data: 'name',  name: 'name'},
                { data: 'unit', name: 'unit'},
                { data: 'jenis_izin', name: 'jenis_izin'},
                { data: 'tgl_awal_izin', name: 'tgl_awal_izin'},
                { data: 'tgl_akhir_izin', name: 'tgl_akhir_izin'},
                { data: 'total_izin', name: 'total_izin'},
                { data: 'print', name: 'print'},
                { data: 'status', name: 'status'},
            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'print'
            ]
        });



    </script>
@endsection

