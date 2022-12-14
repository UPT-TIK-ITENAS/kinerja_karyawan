@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Dashboard</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Sistem Laporan Presensi Karyawan</li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card o-hidden border-0">
                    <div class="bg-primary b-r-4 card-body">
                        <div class="media static-top-widget">
                            <div class="align-self-center text-center">
                                <i data-feather="check-circle"></i>
                            </div>
                            <div class="media-body">
                                <span class="m-0">Total Pengajuan Cuti Yang Sudah di Approve</span>
                                <h4 class="mb-0 counter">{{ $data['cuti'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card o-hidden border-0">
                    <div class="bg-warning b-r-4 card-body">
                        <div class="media static-top-widget">
                            <div class="align-self-center text-center">
                                <i data-feather="info"></i>
                            </div>
                            <div class="media-body">
                                <span class="m-0">Total Pengajuan Cuti Yang Belum di Approve</span>
                                <h4 class="mb-0 counter">{{ $data['pengajuan_cuti'] }}</h4>
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
                                <span class="m-0">Total Pengajuan Izin Yang Sudah di Approve</span>
                                <h4 class="mb-0 counter">{{ $data['izin'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card o-hidden border-0">
                    <div class="bg-warning b-r-4 card-body">
                        <div class="media static-top-widget">
                            <div class="align-self-center text-center">
                                <i data-feather="info"></i>
                            </div>
                            <div class="media-body">
                                <span class="m-0">Total Pengajuan Izin Yang Belum di Approve</span>
                                <h4 class="mb-0 counter">{{ $data['pengajuan_izin'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Zero Configuration  Starts-->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header pb-0">
                                <h5>Total Pegawai (Tendik & Nondik)</h5>
                        </div>
                        <div class="card-body chart-block">
                                <canvas id="GrafikKaryawan" height="50%"></canvas>
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
        $(function(){
            var barData = {
                labels: {!! $data['user_info']->pluck('singkatan_unit') !!},
                datasets: [{
                    label: "My First dataset",
                    fillColor: "rgba(36, 105, 92, 0.4)",
                    strokeColor: vihoAdminConfig.primary,
                    highlightFill: "rgba(36, 105, 92, 0.6)",
                    highlightStroke: vihoAdminConfig.primary,
                    data: {!! $data['user_info']->pluck('total') !!}
                }]
            };
            var barOptions = {
                scaleBeginAtZero: true,
                scaleShowGridLines: true,
                scaleGridLineColor: "rgba(0,0,0,0.1)",
                scaleGridLineWidth: 1,
                scaleShowHorizontalLines: true,
                scaleShowVerticalLines: true,
                barShowStroke: true,
                barStrokeWidth: 2,
                barValueSpacing: 5,
                barDatasetSpacing: 1,
            };
        var barCtx = document.getElementById("GrafikKaryawan").getContext("2d");
        var myBarChart = new Chart(barCtx).Bar(barData, barOptions);
        
        })
            
    </script>


@endsection

