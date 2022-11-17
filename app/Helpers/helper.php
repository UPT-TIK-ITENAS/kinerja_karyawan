<?php

use App\Models\Cuti;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\JenisIzin;
use App\Models\IzinKerja;
use App\Models\User;
use App\Models\Izin;
use App\Models\Attendance;

if (!function_exists('getCheck')) {
    function getCheck($jenis_izin, $id, $tipe)
    {
        $cek = IzinKerja::where('jenis_izin', $jenis_izin)->where('id_izinkerja', $id)->first();
        if ($tipe == 'check') {
            if (empty($cek)) {
                $td = "";
            } else {
                $td = $cek->tgl_awal_izin . ' s/d ' . $cek->tgl_akhir_izin;
            }
        } elseif ($tipe == 'sakit') {
            if ($cek->jenis_izin == 'sakit') {
                $td = $cek->tgl_awal_izin . ' s/d ' . $cek->tgl_akhir_izin;
            } else {
                $td = "................s/d................";
            }
        }
        return $td;
    }
}

if (!function_exists('getApproval')) {
    function getApproval($id, $tipe, $alasan = "")
    {
        $url_batal_cuti = route('karyawan.batal_cuti', $id);
        $url_batal_izin = route('karyawan.batal_izin', $id);
        $for_html = "";
        if ($tipe == 'izin') {
            $getDataIzin = IzinKerja::where('id_izinkerja', $id)->first();
            if ($getDataIzin) {
                if ($getDataIzin->approval == 1) {
                    $for_html = '<span class="badge badge-primary">Disetujui</span>';
                } elseif ($getDataIzin->approval == 2) {
                    $for_html = '<span class="badge badge-success">Disetujui Atasan dari Atasan Langsung</span>';
                } elseif ($getDataIzin->approval == 3) {
                    $for_html = '<span class="badge badge-danger">Ditolak</span><br><span> | "' . $alasan . '"</span>';
                } else {
                    $for_html = '<span class="badge badge-warning">Menunggu</span> <a class="btn btn-danger btn-xs" href="' . $url_batal_izin . '" id="btnBatal">X</a>';
                }
            }
        } elseif ($tipe == 'cuti') {
            $getDataCuti = Cuti::where('id_cuti', $id)->first();
            if ($getDataCuti) {
                if ($getDataCuti->approval == 1) {
                    $for_html = '<span class="badge badge-primary">Disetujui Atasan Langsung</span>';
                } elseif ($getDataCuti->approval == 2) {
                    $for_html = '<span class="badge badge-success">Disetujui Atasan dari Atasan Langsung</span>';
                } elseif ($getDataCuti->approval == 3) {
                    $for_html = '<span class="badge badge-danger">Ditolak</span><br><span><b>note</b> : ' . $alasan . '</span>';
                } else {
                    $for_html = '<span class="badge badge-warning">Menunggu</span> <a class="btn btn-danger btn-xs" href="' . $url_batal_cuti . '" id="btnBatal">X</a>';
                }
            }
        }
        return $for_html;
    }
}

if (!function_exists('getNamaBulan')) {
    function getNamaBulan($angka)
    {
        $bulan = "";
        if ($angka == 1) {
            $bulan = "Januari";
        } elseif ($angka == 2) {
            $bulan = "Februari";
        } elseif ($angka == 3) {
            $bulan = "Maret";
        } elseif ($angka == 4) {
            $bulan = "April";
        } elseif ($angka == 5) {
            $bulan = "Mei";
        } elseif ($angka == 6) {
            $bulan = "Juni";
        } elseif ($angka == 7) {
            $bulan = "Juli";
        } elseif ($angka == 8) {
            $bulan = "Agustus";
        } elseif ($angka == 9) {
            $bulan = "September";
        } elseif ($angka == 10) {
            $bulan = "Oktober";
        } elseif ($angka == 11) {
            $bulan = "November";
        } elseif ($angka == 12) {
            $bulan = "Desember";
        }
        return $bulan;
    }
}

if (!function_exists('routeActive')) {
    function routeActive($routeName)
    {
        return    request()->routeIs($routeName) ? 'active' : '';
    }
}


if (!function_exists('getAksi')) {
    function getAksi($id, $tipe)
    {
        $printizin =  route('admin.printizinkerja', $id);
        $printcuti =  route('admin.printcuti', $id);
        $print =  route('admin.printizin', $id);
        $batal_cuti = route('admin.batal_cuti', $id);
        $batal_izin = route('admin.batal_izin', $id);
        $delete_url = route('admin.destroylibur', $id);

        $for_html = "";
        if ($tipe == 'izin') {
            if (auth()->user()->role == "admin" || auth()->user()->role == "admin_bsdm") {
                $for_html = '<a class="btn btn-success btn-xs" title="Print Surat" href="' . $printizin . '"><i class="icofont icofont-download-alt"></i></a> x';
            } elseif (auth()->user()->role == "kepalaunit") {
                $data = IzinKerja::where('id_izinkerja', $id)->first();
                $for_html = '
                        <a href="#" class="btn btn-primary btn-xs apprvIzin" data-bs-target="#apprvIzin" data-bs-toggle="modal" data-id="' . $data->id_izinkerja . '"><i class="icofont icofont-pencil-alt-2"></i></a>
                        <a class="btn btn-secondary btn-xs" href="' . $printizin . '"><i class="icofont icofont-download-alt"></i></a> 
                        <a class="btn btn-danger btn-xs batalizin" href="' . $batal_izin . '">X</a> ';
            }
        } elseif ($tipe == 'cuti') {
            if (auth()->user()->role == "admin" || auth()->user()->role == "admin_bsdm") {
                $for_html = '<a class="btn btn-success btn-xs" title="Print Surat" href="' . $printcuti . '"><i class="icofont icofont-download-alt"></i></a> ';
            } elseif (auth()->user()->role == "kepalaunit") {
                $data = Cuti::where('id_cuti', $id)->first();
                $for_html = '
                    <a href="#" class="btn btn-primary btn-xs apprvCuti" data-bs-target="#apprvCuti" data-bs-toggle="modal" data-id="' . $data->id_cuti . '"><i class="icofont icofont-pencil-alt-2"></i></a>
                    <a class="btn btn-success btn-xs" href="' . $printcuti . '"><i class="icofont icofont-download-alt"></i></a> ';
            }
        } else if ($tipe == 'liburnasional') {
            $for_html = "
                    <div class='d-block text-center'>
                    <a href='javascript:void(0)' data-toggle='tooltip' class='btn btn btn-warning btn-xs align-items-center editLibur' 
                    data-id='$id' data-original-title='Edit' title='Edit Libur'><i class='icofont icofont-edit-alt'></i></a>
                    <a href='$delete_url' title='Hapus Libur' class='btn btn-sm btn-danger btn-xs align-items-center hapusLibur'><i class='icofont icofont-trash'></i></a>
                    </div>
                    ";
        } else if ($tipe == 'att') {
            $data = Attendance::where('id', $id)->first();
            $izin = Izin::where('id_attendance', $id)->first();
            if ($izin == NULL) {
                $for_html = '
                <a href="#" class="btn btn-warning btn-xs editAtt" data-bs-toggle="modal" data-id="' . $data->id . '"><i class="icofont icofont-pencil-alt-2"></i></a>';
            } else {
                $for_html = '
                <a href="#" class="btn btn-warning btn-xs editAtt" data-bs-toggle="modal" data-id="' . $data->id . '"><i class="icofont icofont-pencil-alt-2"></i></a>
                <a class="btn btn-success btn-xs" href="' . $print . '"><i class="icofont icofont-download-alt"></i></a> ';
            }
        }
        return $for_html;
    }
}

if (!function_exists('getAprv')) {
    function getAprv($id, $tipe, $alasan = "")
    {
        $batal_cuti = route('admin.batal_cuti', $id);
        $batal_izin = route('admin.batal_izin', $id);
        $for_html = "";
        if ($tipe == 'izin') {
            $getDataIzin = IzinKerja::where('id_izinkerja', $id)->first();
            if ($getDataIzin) {
                if ($getDataIzin->approval == 1) {
                    $for_html = '<span class="badge badge-primary">Disetujui Atasan Langsung</span>';
                } else {
                    $for_html = '<span class="badge badge-warning">Menunggu Persetujuan</span><a class="btn btn-danger btn-xs batalizin" title="Batal Izin" href="' . $batal_izin . '">X</a>';
                }
            }
        } elseif ($tipe == 'cuti') {
            $getDataCuti = Cuti::where('id_cuti', $id)->first();
            if ($getDataCuti) {
                if ($getDataCuti->approval == 1) {
                    $for_html = '<span class="badge badge-primary">Disetujui Atasan Langsung</span>';
                } elseif ($getDataCuti->approval == 2) {
                    $for_html = '<span class="badge badge-success">Disetujui Atasan dari Atasan Langsung</span>';
                } elseif ($getDataCuti->approval == 3) {
                    $for_html = '<span class="badge badge-danger">Ditolak</span><br><p><b>note</b> : ' . $alasan . '</p>';
                } else {
                    $for_html = '<span class="badge badge-warning">Menunggu Persetujuan</span> <a class="btn btn-danger btn-xs batalcuti" title="Batal Cuti" href="' . $batal_cuti . '">X</a>';
                }
            }
        }
        return $for_html;
    }
}

if (!function_exists('getWorkingDays')) {
    function getWorkingDays($startDate, $endDate)
    {
        $begin = strtotime($startDate);
        $end   = strtotime($endDate);
        $curentYear = date('Y', $begin);
        $endYear = date('Y', $end);
        $libur_nasional = DB::table('libur_nasional')->whereYear('tanggal', '=', $curentYear)->whereYear('tanggal', '=', $endYear)->get();
        if ($begin > $end) {
            return 0;
        } else {
            $no_days  = 0;
            $weekends = 0;
            while ($begin <= $end) {
                $no_days++; // no of days in the given interval
                $what_day = date("N", $begin);
                if ($what_day > 5) { // 6 and 7 are weekend days
                    $weekends++;
                }
                // cek libur nasional
                foreach ($libur_nasional as $key => $value) {
                    if (date('Y-m-d', $begin) == $value->tanggal) {
                        $weekends++;
                    }
                }
                $begin += 86400; // +1 day
            };
            $working_days = $no_days - $weekends;

            return $working_days;
        }
    }
}

if (!function_exists('getJawabanPertanyaan')) {
    function getJawabanPertanyaan($pertanyaan_kuesioner)
    {
        $jawaban_pertanyaan = DB::table('jawaban_pertanyaan')->where('id_pertanyaan', $pertanyaan_kuesioner)->get();
        return $jawaban_pertanyaan;
    }
}



if (!function_exists('getNama')) {
    function getNama($nopeg)
    {
        $nama = User::select('name')->where('nopeg', $nopeg)->first();
        return $nama;
    }
}
