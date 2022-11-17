@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Detail Rekapitulasi Kehadiran Karyawan</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Detail Rekapitulasi Kehadiran Karyawan</li>
                        <li class="breadcrumb-item active">Rekapitulasi Kehadiran</li>
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
                    {{-- <div class="card-header">
                        <h5>Daftar Detail Rekapitulasi Kehadiran Karyawan</h5>
                        <span>Daftar hasil rekapitulasi kehadiran karyawan terhitung dari tanggal 01 Juli 2022</span>
                    </div> --}}
                    <div class="card-body">

                        <h6 class="font-primary">Rekapitulasi Kehadiran</h6>
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
                                    <th>Tanpa Keterangan</th>
                                    <th>Jumlah Hari per Bulan</th>
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
                                                    <td> {{ $d->totalizin }} hari</td>
                                                    <td> {{ $d->totalsakit }} hari</td>
                                                @endforeach
                                            @endif

                                            @if ($cuti == null)
                                                <td> 0 </td>
                                            @else
                                                @foreach ($cuti as $d)
                                                    <td> {{ $d->totalcuti }} hari</td>
                                                @endforeach
                                            @endif
                                            <td> 0 </td>
                                            <td><span class="jumlah" data-bulan="{{ $r->bulan }}"
                                                    data-tahun="{{ $r->tahun }}" data-total="{{ $r->totalkerja }}"
                                                    data-libur="{{ $r->jumlah_hari_libur }}"></span></td>
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
        function getDaysInMonth(month, year) {
            month--; // lets fix the month once, here and be done with it
            var date = new Date(year, month, 1);
            var days = [];
            while (date.getMonth() === month) {

                // Exclude weekends
                var tmpDate = new Date(date);
                var weekDay = tmpDate.getDay(); // week day
                var day = tmpDate.getDate(); // day

                if (weekDay % 6) { // exclude 0=Sunday and 6=Saturday
                    days.push(day);
                }

                date.setDate(date.getDate() + 1);
            }

            return days;
        }

        $(".jumlah").each(function() {
            var bulan = $(this).data('bulan');
            var tahun = $(this).data('tahun');
            var total = $(this).data('total');
            var libur = $(this).data('libur');
            var hasil = getDaysInMonth(bulan, tahun);
            var jmlh_hari = hasil - libur;
            var totalhari = hasil.length - libur;
            // $(this).text(jmlh_hari);
            // var total = Math.round((total / jmlh_hari) * 100, 2);
            // $(this).text(total + '%');
            $(this).text(totalhari + ' hari');
            console.log(totalhari);
        })
    </script>
@endsection
