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
                        <form action="{{ route('admin.SyncAndInsertBiometric') }}" method="POST" class="mt-3">
                            @csrf
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
                        </form>
                        <hr>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
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
                    url: "{{ route('admin.rekapitulasi.listdetailrekap', auth()->user()->nopeg) }}",
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
                        data: 'izin',
                        name: 'izin'
                    },
                    {
                        data: 'kurang_jam',
                        name: 'kurang_jam'
                    },
                ],
            });

            $("#filter1").on('change', function() {
                table.draw();
            });
        });
    </script>
@endsection
