@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Presensi</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Data Kehadiran</li>
                        <li class="breadcrumb-item active">Data Presensi</li>
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
                        <div class="row mb-2">
                            <h5>Monitoring Kehadiran </h5>
                            <span>Daftar hasil monitoring kehadiran karyawan terhitung dari tanggal 01 Juli 2022</span>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <label class="form-label" for="validationDefault01">Pilih Bulan</label>
                                <input class="datepicker-here form-control digits" type="text" data-language="en"
                                    data-min-view="months" data-view="months" id="bulan_hadir" data-date-format="MM yyyy">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="dataTable" id="table-kehadiran">
                                <thead>
                                    <th>No.</th>
                                    <th>Hari</th>
                                    <th>Awal Tugas</th>
                                    <th>Akhir Tugas</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Siang</th>
                                    <th>Jam Keluar</th>
                                    <th>Durasi</th>
                                    <th>Telat Masuk</th>
                                    <th>Telat Siang</th>
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
        $(function() {
            $("#bulan_hadir").datepicker();
            let table = $('#table-kehadiran').DataTable({
                fixedHeader: true,
                pageLength: 20,
                responsive: true,
                processing: true,
                autoWidth: false,
                serverSide: true,
                columnDefs: [{
                    targets: 1,
                    width: "200px !important",
                }, ],
                ajax: "{{ route('karyawan.listdatapresensi') }}",
                // ajax: {
                //     data: function(d) {
                //         d.bulan = $('#bulan_hadir').datepicker('getDate')
                //         // d.tahun = ('#bulan_hadir').val()
                //     },
                // },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'hari',
                        name: 'hari'
                    },
                    {
                        data: 'awal_tugas',
                        name: 'awal_tugas'
                    },
                    {
                        data: 'akhir_tugas',
                        name: 'akhir_tugas'
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
                ],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'print'
                ]
            });
        });
    </script>
@endsection
