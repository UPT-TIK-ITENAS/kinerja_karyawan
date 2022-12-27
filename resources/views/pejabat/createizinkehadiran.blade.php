@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Dashboard</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Sistem Laporan Presensi Karyawan</li>
                        <li class="breadcrumb-item active">Data Izin</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header pb-0">
            <h5>Data Izin</h5>
        </div>
        <form autocomplete="off" action="{{ route('karyawan.storeizinkehadiran', $data->id) }}" method="POST" name="tmbh"
            enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">

                    <input type="text" id="id_attendance" name="id_attendance" hidden value="{{ $data->id }}" />
                    <div class="col-xl-1 col-md-6 col-12">
                        <div class="mb-1">
                            <label class="form-label" for="helpInputTop">No. Pegawai</label>
                            {{-- <small class="text-muted">eg.<i>someone@example.com</i></small> --}}
                            <input type="text" class="form-control" id="nopeg" name="nopeg" readonly
                                value="{{ $data->nip }}" />
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="mb-1">
                            <label class="form-label" for="basicInput">Nama Karyawan</label>
                            <input type="text" class="form-control" id="name" name="name" readonly
                                value="{{ $data->name }}" />
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="mb-1">
                            <label class="form-label" for="disabledInput">Unit Kerja</label>
                            <input type="text" class="form-control" id="unit" name="unit" readonly
                                value="{{ $data->nama_unit }}" />
                            <input type="hidden" id="idunit" name="idunit" value="{{ $data->unit }}" />
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-6 col-12">
                        <div class="mb-1">
                            <label class="form-label" for="disabledInput">Tanggal</label>
                            <input type="text" class="form-control" id="tgl" name="tgl" readonly
                                value="{{ $data->tanggal }}" />
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-6 col-12">
                        <div class="mb-1">
                            <label class="form-label" for="disabledInput">Jam Awal</label>
                            <div class="input-group clockpicker">
                                <input class="form-control" type="text" id="jam_awal" name="jam_awal"><span
                                    class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-6 col-12">
                        <div class="mb-1">
                            <label class="form-label" for="disabledInput">Jam Akhir</label>
                            <div class="input-group clockpicker">
                                <input class="form-control" type="text" id="jam_akhir" name="jam_akhir"><span
                                    class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-12 mb-1 mb-md-0">
                        <div class="mb-1">
                            <label class="form-label" for="exampleFormControlTextarea1">Alasan</label>
                            <textarea class="form-control" id="alasan" name="alasan" rows="3" placeholder="Alasan" required></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-1">
                            <div class="form-check form-check-success">
                                <input type="checkbox" class="form-check-input" id="validasi" name="validasi"
                                    value="1">
                                <label class="form-check-label" for="colorCheck3" required>Dengan ini saya menyatakan
                                    dengan
                                    benar bahwa saya izin</label>
                            </div>
                        </div>
                    </div>

                </div>

                <button class="btn btn-primary" type="submit" id="btnSubmit" name="btnSubmit">Simpan</button>
            </div>
            <form autocomplete="off">
    </div>
@endsection

@section('scripts')
    @parent
@endsection
