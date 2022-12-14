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
                        <h5>Silakan pilih periode untuk melihat jumlah kehadiran</h5>
                        <br>
                        <div class="form-group row">
                            <label class="col-lg-1 col-md-12 col-form-label">Periode</label>
                            <div class="col-lg-6 col-md-12">
                                <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                    name="filter1" id="filter1" required="">
                                    @foreach ($periode as $p)
                                        @if ($p->id == 2)
                                            <option value="{{ $p->id }}" selected>{{ $p->judul }}</option>
                                        @else
                                            <option value="{{ $p->id }}">{{ $p->judul }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <h5>{{ $user->name . ' - (' . $user->nopeg . ')' }}</h5>
                        </div>
                        <h6 class="font-primary">Rekapitulasi Kehadiran</h6>
                        <div class="dt-ext table-responsive">
                            <table class="table table-bordered" id="table-rekapitulasi">
                                <thead>
                                    <th>No.</th>
                                    <th>Bulan</th>
                                    <th>Total Hari Hadir Kerja</th>
                                    <th>Total Hari Kerja</th>
                                    <th>Total Hari Mangkir</th>
                                    <th>Cuti</th>
                                    <th>Izin</th>
                                    <th>Izin Kerja</th>
                                    <th>Kurang Jam</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Penilaian</h5>
                        <hr>
                    </div>
                    <div class="card-body pt-0">
                        <div class="mb-3">
                            <h5>{{ $user->name . ' - (' . $user->nopeg . ')' }}</h5>
                        </div>
                        <div class="my-3">
                            <h6 class="font-primary">Detail Penilaian</h6>
                            <div class="dt-ext table-responsive">
                                <table class="table table-bordered" id="table-penilaian">
                                    <thead>
                                        <th>No.</th>
                                        <th>Bulan</th>
                                        <th>Poin Izin</th>
                                        <th>Poin Sakit</th>
                                        <th>Poin Mangkir</th>
                                        <th>Poin Kurang Jam</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        <div class="mb-3">
                            <h5>{{ $user->name . ' - (' . $user->nopeg . ')' }}</h5>
                        </div>
                        <div class="my-3">

                            <h6 class="font-primary">Total Penilaian</h6>
                            <div class="dt-ext table-responsive">
                                <table class="table table-bordered" id="table-total-penilaian">
                                    <thead>
                                        <th>No.</th>
                                        <th>Komponen Penilaian</th>
                                        <th>Sub Komponen</th>
                                        <th>Bobot</th>
                                        <th>Poin</th>
                                    </thead>
                                    <tbody>

                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="4" style="text-align:right !important">Total Poin</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
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
    @parent
    <script>
        $(document).ready(function() {
            let table = $('#table-rekapitulasi').DataTable({
                fixedHeader: true,
                pageLength: 10,
                responsive: true,
                processing: true,
                autoWidth: false,
                serverSide: true,
                searching: false,
                ajax: {
                    url: "{{ route('kepalaunit.listdatarekapitulasi', $nopeg) }}",
                    data: function(d) {
                        d.periode = $('#filter1').val() ? $('#filter1').val() : '2';
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
                        data: 'total_masuk_karyawan',
                        name: 'total_masuk_karyawan'
                    },
                    {
                        data: 'total_hari_kerja_per_bulan',
                        name: 'total_hari_kerja_per_bulan'
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
                        data: 'izin_kerja',
                        name: 'izin_kerja'
                    },
                    {
                        data: 'total_izin',
                        name: 'total_izin'
                    },
                    {
                        data: 'kurang_jam',
                        name: 'kurang_jam'
                    },
                ],
            });
            let tablePenilaian = $('#table-penilaian').DataTable({
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
                ajax: {
                    url: "{{ route('kepalaunit.penilaian_detail', 'detail') }}",
                    data: function(d) {
                        d.periode = $('#filter1').val() ? $('#filter1').val() : '2';
                        d.nopeg = "{{ $nopeg }}";
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
                        data: 'bulan',
                        name: 'bulan'
                    },
                    {
                        data: 'izin',
                        name: 'izin'
                    },
                    {
                        data: 'sakit',
                        name: 'sakit'
                    },
                    {
                        data: 'mangkir',
                        name: 'mangkir'
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

            let tableTotalPenilaian = $('#table-total-penilaian').DataTable({
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
                ajax: {
                    url: "{{ route('kepalaunit.penilaian_detail', 'total') }}",
                    data: function(d) {
                        d.periode = $('#filter1').val() ? $('#filter1').val() : '2';
                        d.nopeg = "{{ $nopeg }}";
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
                        data: 'komponen_penilaian',
                        name: 'komponen_penilaian'
                    },
                    {
                        data: 'sub_komponen',
                        name: 'sub_komponen'
                    },
                    {
                        data: 'bobot',
                        name: 'bobot'
                    },
                    {
                        data: 'point',
                        name: 'point'
                    },
                ],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'print'
                ],
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();

                    // Remove the formatting to get integer data for summation
                    let intVal = function(i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i ===
                            'number' ? i :
                            0;
                    };

                    // Total over all pages
                    let total = api
                        .column(4)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Total over this page
                    let pageTotal = api
                        .column(4, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Update footer
                    $(api.column(4).footer()).html(`${total} Poin`);
                },
            });

            $("#filter1").on('change', function() {
                table.draw();
                tablePenilaian.draw();
                tableTotalPenilaian.draw();
            });

        });
    </script>
@endsection
