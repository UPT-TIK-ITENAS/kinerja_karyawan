@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Master Data Presensi</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Sistem Laporan Presensi Karyawan</li>
                        <li class="breadcrumb-item active">Data Rekapitulasi</li>
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
                            <table class="dataTable" id="table-rekapitulasi">
                                <thead>
                                    <th width="5%">No.</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Total Telat Pagi</th>
                                    <th>Total Telat Siang</th>
                                    <th>Total Telat Keseluruhan</th>
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
        let table = $('#table-rekapitulasi').DataTable({
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
            ajax: "{{ route('karyawan.listdatarekapitulasi') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: 'text-center',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'bulan',
                    name: 'bulan',
                },
                {
                    data: 'tahun',
                    name: 'tahun',
                    class: 'text-center',
                },

                {
                    data: 'total_telat_pagi',
                    name: 'total_telat_pagi',
                    class: 'text-center',
                },
                {
                    data: 'total_telat_siang',
                    name: 'total_telat_siang',
                    class: 'text-center',
                },
                {
                    data: 'total_telat',
                    name: 'total_telat',
                    class: 'text-center',
                },

            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'print'
            ]
        });
    </script>
@endsection
