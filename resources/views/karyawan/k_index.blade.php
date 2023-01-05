@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Dashboard</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Penilaian Kinerja</li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <hr>
        <h6>Rekap untuk periode {{ $periode->judul }}</h6>
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card o-hidden border-0">
                    <div class="bg-primary b-r-4 card-body">
                        <div class="media static-top-widget">
                            <div class="align-self-center text-center">
                                <i data-feather="check-circle"></i>
                            </div>
                            <div class="media-body">
                                <span class="m-0">Total Hari Kerja</span>
                                <h4 class="mb-0 counter">{{ $data->sum('total_hari_kerja_per_bulan') }} Hari</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card o-hidden border-0">
                    <div class="bg-primary b-r-4 card-body">
                        <div class="media static-top-widget">
                            <div class="align-self-center text-center">
                                <i data-feather="check-circle"></i>
                            </div>
                            <div class="media-body">
                                <span class="m-0">Total Hari Hadir Kerja</span>
                                <h4 class="mb-0 counter">{{ $data->sum('total_masuk_karyawan') }} Hari</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card o-hidden border-0">
                    <div class="bg-primary b-r-4 card-body">
                        <div class="media static-top-widget">
                            <div class="align-self-center text-center">
                                <i data-feather="check-circle"></i>
                            </div>
                            <div class="media-body">
                                <span class="m-0">Total Hari Mangkir</span>
                                <h4 class="mb-0 counter">{{ $data->sum('total_hari_mangkir') }} Hari</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card o-hidden border-0">
                    <div class="bg-primary b-r-4 card-body">
                        <div class="media static-top-widget">
                            <div class="align-self-center text-center">
                                <i data-feather="clock"></i>
                            </div>
                            <div class="media-body">
                                <span class="m-0">Total Kurang Jam</span>
                                <h4 class="mb-0 counter">
                                    {{ \Carbon\CarbonInterval::seconds(($data->sum('kurang_jam') * 3600) / 60)->cascade()->forHumans() }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card o-hidden border-0">
                    <div class="bg-primary b-r-4 card-body">
                        <div class="media static-top-widget">
                            <div class="align-self-center text-center">
                                <i data-feather="check-circle"></i>
                            </div>
                            <div class="media-body">
                                <span class="m-0">Total Cuti</span>
                                <h4 class="mb-0 counter">{{ $data->sum('cuti') ?? 0 }} Hari</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card o-hidden border-0">
                    <div class="bg-primary b-r-4 card-body">
                        <div class="media static-top-widget">
                            <div class="align-self-center text-center">
                                <i data-feather="trending-up"></i>
                            </div>
                            <div class="media-body">
                                <span class="m-0">Total Izin</span>
                                <h4 class="mb-0 counter">{{ $data->sum('izin_kerja') ?? 0 }} Hari</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Data Rekap Kehadiran</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="dt-ext table-responsive">
                        <table class="table table-bordered" id="table-rekapitulasi">
                            <thead>
                                <th>No.</th>
                                <th>Bulan</th>
                                <th>Total Hari Kerja</th>
                                <th>Total Hari Hadir Kerja</th>
                                <th>Total Hari Mangkir</th>
                                <th>Cuti</th>
                                <th>Izin Non Resmi</th>
                                <th>Izin Resmi</th>
                                <th>Izin Sakit</th>
                                <th>Kurang Jam</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
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
            autoWidth: true,
            serverSide: true,
            ajax: {
                url: "{{ route('karyawan.listdatarekapitulasi') }}",
                data: function(d) {
                    d.periode = '2';
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
                    data: 'nama_bulan',
                    name: 'nama_bulan'
                },
                {
                    data: 'total_hari_kerja_per_bulan',
                    name: 'total_hari_kerja_per_bulan'
                },
                {
                    data: 'total_masuk_karyawan',
                    name: 'total_masuk_karyawan'
                },
                {
                    data: 'total_hari_mangkir',
                    name: 'total_hari_mangkir'
                },
                {
                    data: 'cuti',
                    name: 'cuti'
                },
                {
                    data: 'total_izin',
                    name: 'total_izin'
                },
                {
                    data: 'izin_kerja',
                    name: 'izin_kerja'
                },
                {
                    data: 'izin_sakit',
                    name: 'izin_sakit'
                },
                {
                    data: 'kurang_jam',
                    name: 'kurang_jam'
                },
            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'print'
            ]
        });
    </script>
@endsection
