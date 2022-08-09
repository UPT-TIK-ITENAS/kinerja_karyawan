@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-lg-6">
                <h3>Rekap Data Izin-Cuti</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Rekap Data Izin-Cuti</li>
                    <li class="breadcrumb-item active">Rekap Cuti</li>
                </ol>
            </div>
        </div>
    </div>
</div>
    <div class="card-body d-flex">
        <a href="{{ route('admin.createcuti') }}" class="btn btn-primary"><i class="icofont icofont-plus-square"></i> Add</a>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Zero Configuration  Starts-->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Daftar Cuti </h5>
                        <span>Daftar Cuti Karyawan</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive"> 
                            <table class="dataTable" id="table-cuti">
                                <thead>
                                    <th>No.</th>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                    <th>Unit</th>
                                    <th>Jenis Cuti</th>
                                    <th>Tanggal Awal Cuti</th>
                                    <th>Tanggal Akhir Cuti</th>
                                    <th>Total Hari Cuti</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
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
        let table = $('#table-cuti').DataTable({
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
            ajax: "{{ route('admin.listcuti') }}",
            columns: [
                {    data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            className: 'text-center',
                            orderable: false,
                            searchable: false,
                },
                { data: 'name',  name: 'name'},
                { data: 'nopeg', name: 'nopeg'},
                { data: 'unit', name: 'unit'},
                { data: 'jenis_cuti', name: 'jenis_cuti'},
                { data: 'tgl_awal_cuti', name: 'tgl_awal_cuti'},
                { data: 'tgl_akhir_cuti', name: 'tgl_akhir_cuti'},
                { data: 'total_cuti', name: 'total_cuti'},
                { data: 'alasan_tolak', name: 'alasan_tolak'},
                { data: 'action', name: 'action'},
            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'print'
            ]
        });



    </script>
@endsection

