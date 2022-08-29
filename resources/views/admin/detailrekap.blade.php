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
                    <div class="card-header">
                        <h5>Daftar Detail Rekapitulasi Kehadiran Karyawan</h5>
                        <span>Daftar hasil rekapitulasi kehadiran karyawan terhitung dari tanggal 01 Juli 2022</span>
                    </div>
                    <div class="card-body">
                        <h6 class="font-primary">Keterlambatan/pulang cepat - Jumlah Kurang Jam (menit)</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-rekapitulasi">
                                <thead>
                                    <th width="5%">No.</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Total Telat Pagi</th>
                                    <th>Total Telat Siang</th>
                                    <th>Total Telat Keseluruhan</th>
                                    <th>Izin</th>
                                    <th>Sakit</th>
                                    <th>Total Hadir</th>
                                    <th>Skor</th>
                                    <th>Persentase</th>
                                </thead>
                                <tbody>
                                    @foreach ($data as $no => $r)
                                    <tr>
                                        <td align="center">{{ $no + 1 }}</td>
                                        <td>{{ getNamaBulan($r->bulan) }}</td>
                                        <td>{{ $r->tahun }}</td>
                                        <td>{{ date('H:i:s', strtotime($r->total_telat_pagi)) }}</td>
                                        <td>{{ date('H:i:s', strtotime($r->total_telat_siang)) }}</td>
                                        <td>{{ date('H:i:s', strtotime($r->total_telat_siang) + strtotime($r->total_telat_pagi)) }}</td>
                                        @foreach ($dataizinkerja as $no => $d)
                                        @if ($d->bulan == $r->bulan &&  $d->tahun == $r->tahun)
                                            <td>{{ $d->total }}</td>
                                        @else
                                            <td> </td>
                                        @endif  
                                        @endforeach

                                        
                                        @foreach ($datasakit as $no => $s)
                                        @if ($s->bulan == $r->bulan &&  $s->tahun == $r->tahun)
                                            <td>{{ $s->total }}</td>
                                        @else
                                            <td> </td>
                                        @endif  
                                        @endforeach
                                        <td> {{ $r->totalkerja }}</td>
                                        @if (date('i', strtotime($r->total_telat_siang) + strtotime($r->total_telat_pagi)) > 300)
                                            <td> 0</td>
                                        @else
                                            <td>{{ 15*(300- date('i', strtotime($r->total_telat_siang) + strtotime($r->total_telat_pagi)))/300 }} </td> 
                                        @endif                                       
                                        <td><span class="jumlah" data-id="{{ $r->id }}" data-bulan="{{ $r->bulan }}" data-tahun="{{ $r->tahun }}" data-total="{{ $r->totalkerja }}"></span></td>

                                       

                                    </tr>
                                    @endforeach
                                    </tbody>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <h6 class="font-primary">Jumlah Izin di luar Izin Resmi (jam)</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered a1" id="table-rekapitulasi">
                                <thead>
                                    <th width="5%">No.</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                
                                    <th>Total Izin</th>
                                </thead>
                                <tbody>
                                    @foreach ($dataizinkerja as $no => $r)
                                    <tr>
                                        <td align="center">{{ $no + 1 }}</td>
                                        <td>{{ getNamaBulan($r->bulan)}}</td>
                                        <td>{{ $r->tahun }}</td>
                                        <td>{{ $r->total }}</td>
                                    
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <h6 class="font-primary">Jumlah Sakit (Ajuan Sakit) </h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-rekapitulasi">
                                <thead>
                                    <th width="5%">No.</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Total Izin</th>
                                </thead>
                                <tbody>
                                    @foreach ($datasakit as $no => $r)
                                    <tr>
                                        <td align="center">{{ $no + 1 }}</td>
                                        <td>{{ getNamaBulan($r->bulan)}}</td>
                                        <td>{{ $r->tahun }}</td>
                                        <td>{{ $r->total }}</td>
                                    
                                    </tr>
                                    @endforeach
                                    </tbody>
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

                if (weekDay%6) { // exclude 0=Sunday and 6=Saturday
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
            var hasil = getDaysInMonth(bulan, tahun);
            var jmlh_hari = hasil.length;
            // $(this).text(jmlh_hari);
            var total = Math.round((total/jmlh_hari)*100,2);
            $(this).text(total);
        })
    </script>

@endsection
