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
        <h6>Rekap untuk bulan {{ \Carbon\Carbon::parse(date('Y-m-d'))->isoFormat('MMMM Y') }}</h6>
        <div class="row">
            <!-- Zero Configuration  Starts-->
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card o-hidden border-0">
                    <div class="bg-secondary b-r-4 card-body">
                        <div class="media static-top-widget">
                            <div class="align-self-center text-center">
                                <i data-feather="clock"></i>
                            </div>
                            <div class="media-body">
                                <span class="m-0">Total Keterlambatan</span>
                                <h4 class="mb-0 counter">{{ $data->kurang_jam }}</h4>
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
                                <span class="m-0">Total Hari Kerja</span>
                                <h4 class="mb-0 counter">{{ $data->total_hari_kerja_per_bulan }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card o-hidden border-0">
                    <div class="bg-secondary b-r-4 card-body">
                        <div class="media static-top-widget">
                            <div class="align-self-center text-center">
                                <i data-feather="trending-up"></i>
                            </div>
                            <div class="media-body">
                                <span class="m-0">Total Izin</span>
                                <h4 class="mb-0 counter">{{ $data->izin }}</h4>
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
                                <h4 class="mb-0 counter">{{ $data->cuti }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
