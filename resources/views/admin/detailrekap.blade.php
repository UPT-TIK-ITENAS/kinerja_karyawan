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
                                    <th>NIP</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Total Telat Pagi</th>
                                    <th>Total Telat Siang</th>
                                    <th>Total Telat Keseluruhan</th>
                                    <th>Skor</th>
                                </thead>
                                <tbody>
                                    @foreach ($data as $no => $r)
     
                                    <tr>
                                        <td align="center">{{ $no + 1 }}</td>
                                        <td>{{ $r->nip }}</td>
                                        <td>{{ getNamaBulan($r->bulan)}}</td>
                                        <td>{{ $r->tahun }}</td>
                                        <td>{{ date('H:i:s', strtotime($r->total_telat_pagi)) }}</td>
                                        <td>{{ date('H:i:s', strtotime($r->total_telat_siang)) }}</td>
                                        <td>{{ date('H:i:s', strtotime($r->total_telat_siang) + strtotime($r->total_telat_pagi)) }}</td>
                                        @if (date('i', strtotime($r->total_telat_siang) + strtotime($r->total_telat_pagi)) > 300)
                                        <td> 0</td>
                                        @else
                                            <td>{{ 15*(300- date('i', strtotime($r->total_telat_siang) + strtotime($r->total_telat_pagi)))/300 }} </td>
                                            
                                        @endif
                                      
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <h6 class="font-primary">Jumlah Izin di luar Izin Resmi (jam)</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-rekapitulasi">
                                <thead>
                                    <th width="5%">No.</th>
                                    <th>NIP</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Total Izin</th>
                                </thead>
                                <tbody>
                                    @foreach ($dataizinkerja as $no => $r)
                                    <tr>
                                        <td align="center">{{ $no + 1 }}</td>
                                        <td>{{ $r->nip }}</td>
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
                                    <th>NIP</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Total Izin</th>
                                </thead>
                                <tbody>
                                    @foreach ($datasakit as $no => $r)
                                    <tr>
                                        <td align="center">{{ $no + 1 }}</td>
                                        <td>{{ $r->nip }}</td>
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

@endsection
