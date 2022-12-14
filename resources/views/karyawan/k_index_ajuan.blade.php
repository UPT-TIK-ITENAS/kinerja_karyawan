@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Pengajuan Presensi</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Pengajuan</li>
                        <li class="breadcrumb-item active">Presensi</li>
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
                        <div class="alert alert-warning dark col-md-6" role="alert">
                            <p style="color:black;"> <b> *) Ajuan ini digunakan hanya untuk yang tidak ada sama sekali di
                                    kehadiran presensi/tidak ter record mesin sidik jari/dispensasi 1 hari penuh </b></p>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <a href="#" class="btn btn-primary" data-bs-target="#tambahIzin"
                                    data-bs-toggle="modal" style="float: right">+ Tambah</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="dt-ext table-responsive">
                                <table class="dataTable" id="table-izin">
                                    <thead>
                                        <tr align="center">
                                            <th width="5%">No.</th>
                                            <th>Nopeg</th>
                                            <th>Nama</th>
                                            <th>Unit</th>
                                            <th>Tanggal</th>
                                            <th>Alasan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data['mangkir'] as $no => $r)
                                            <tr>
                                                <td align="center">{{ $no + 1 }}</td>
                                                <td>{{ $r->nopeg }}</td>
                                                <td>{{ $r->nama }}
                                                <td>{{ $r->units->nama_unit }}</td>
                                                <td>{{ $r->tanggal }}</td>
                                                <td>{{ $r->alasan }}</td>
                                                @if ($r->status == 0)
                                                    <td><span class="badge badge-warning">Menunggu Persetujuan</span></td>
                                                @else
                                                    <td> <span class="badge badge-primary">Disetujui Atasan</span> </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <div class="modal fade bd-example-modal-lg" id="tambahIzin" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Form Pengajuan Presensi</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form autocomplete="off" class="needs-validation" novalidate=""
                    action="{{ route('karyawan.store_ajuan') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <span class="form-label" for="nopeg">No Pegawai</span>
                                <input class="form-control" id="nopeg" name="nopeg" type="text"
                                    value="{{ auth()->user()->nopeg }}" required="" readonly>
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="nama">Nama Lengkap</span>
                                <input class="form-control" id="nama" name="nama" type="text" required=""
                                    value="{{ auth()->user()->name }}" readonly>
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <span class="form-label" for="tanggal">Tanggal</span>
                                <input class="form-control" id="tanggal" name="tanggal" type="text" required="">
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="alasan">Alasan</span>
                                <textarea name="alasan" id="alasan" name="alasan" class="form-control" required></textarea>
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
    <script>
        let table = $('#table-izin').DataTable({
            fixedHeader: true,
            pageLength: 10,
            responsive: true,
            processing: true,
        });

        // daterangepicker('#tanggal', drops = "auto", autoUpdate = true, autoApply = true, timePicker = false,
        //     parentEl = '#tambahIzin');

        $("#tanggal").daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            showDropdowns: true,
            autoUpdateInput: true,
            autoApply: true,
            locale: {
                cancelLabel: "Hapus",
                applyLabel: "Terapkan",
                format: "YYYY-MM-DD",
            },
            drops: "auto",
            parentEl: "#tambahIzin",
            minDate: moment().startOf('year').format('YYYY-MM-DD'),
            maxDate: moment().endOf('year').format('YYYY-MM-DD')
        });
    </script>
@endsection
