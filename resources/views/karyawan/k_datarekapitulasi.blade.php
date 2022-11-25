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
                        <div class="dt-ext table-responsive">
                            <table class="table table-bordered" id="table-rekapitulasi">
                                <thead>
                                    <th width="5%">No.</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Hadir</th>
                                    <th>Izin</th>
                                    <th>Sakit</th>
                                    <th>Cuti</th>
                                    {{-- <th>Tanpa Keterangan</th> --}}
                                    {{-- <th>Jumlah Hari per Bulan</th> --}}
                                </thead>
                                <tbody>
                                    @foreach ($data as $no => $r)
                                        <tr>
                                            <td align="center">{{ $no + 1 }}</td>
                                            <td>{{ getNamaBulan($r->bulan) }}</td>
                                            <td>{{ $r->tahun }}</td>
                                            <td> {{ $r->totalkerja }} hari</td>

                                            @if ($data2 == null)
                                                <td> 0 </td>
                                                <td> 0 </td>
                                            @else
                                                @foreach ($data2 as $d)
                                                    @if ($d->bulan == $r->bulan)
                                                        <td> {{ $d->totalizin }} hari</td>
                                                        <td> {{ $d->totalsakit }} hari</td>
                                                    @else
                                                        <td> </td>
                                                        <td> </td>
                                                    @endif
                                                @endforeach
                                            @endif

                                            @if ($cuti == null)
                                                <td> 0 </td>
                                            @else
                                                @foreach ($cuti as $d)
                                                    @if ($d->bulan == $r->bulan)
                                                        <td> {{ $d->totalcuti }} hari</td>
                                                    @else
                                                        <td></td>
                                                    @endif
                                                @endforeach
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
