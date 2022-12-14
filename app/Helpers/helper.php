<?php

use App\Models\Attendance;
use App\Models\Cuti;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\JenisIzin;
use App\Models\IzinKerja;
use App\Models\User;
use App\Models\Izin;
use Carbon\Carbon;

if (!function_exists('getCheck')) {
    function getCheck($jenis_izin, $id, $tipe)
    {
        $cek = IzinKerja::where('jenis_izin', $jenis_izin)->where('id_izinkerja', $id)->first();
        if ($tipe == 'check') {
            if (empty($cek)) {
                $td = "";
            } else {
                $td = \Carbon\Carbon::parse($cek->tgl_awal_izin)->isoFormat('D MMMM')  . ' s/d ' . \Carbon\Carbon::parse($cek->tgl_akhir_izin)->isoFormat('D MMMM Y');
            }
        } elseif ($tipe == 'sakit') {
            if ($cek->jenis_izin == '9') {
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
                    $for_html = '<span class="badge badge-primary">Disetujui Atasan</span>';
                } elseif ($getDataIzin->approval == 3) {
                    $for_html = '<span class="badge badge-danger">Ditolak</span><br><span> | "' . $alasan . '"</span>';
                } elseif ($getDataIzin->approval == 2) {
                    $for_html = '<span class="badge badge-success">Disetujui Atasan dari Atasan Langsung</span>';
                } else {
                    $for_html = '<span class="badge badge-warning">Menunggu Persetujuan</span> <a class="btn btn-danger btn-xs batalizin" href="' . $url_batal_izin . '" id="btnBatal"><i class="fa fa-times"></i></a>';
                }
            }
        } elseif ($tipe == 'cuti') {
            $getDataCuti = Cuti::where('id_cuti', $id)->first();
            if ($getDataCuti) {
                if ($getDataCuti->approval == 1) {
                    $for_html = '<span class="badge badge-primary">Disetujui Atasan</span>';
                } elseif ($getDataCuti->approval == 3) {
                    $for_html = '<span class="badge badge-danger">Ditolak</span><br><span><b>note</b> : ' . $alasan . '</span>';
                } elseif ($getDataCuti->approval == 2) {
                    $for_html = '<span class="badge badge-success">Disetujui Atasan dari Atasan Langsung</span>';
                } else {
                    $for_html = '<span class="badge badge-warning">Menunggu Persetujuan</span> <a class="btn btn-danger btn-xs batalcuti" href="' . $url_batal_cuti . '" id="btnBatal"><i class="fa fa-times"></i></a>';
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
    function getAksi($id, $tipe, $user = null)
    {
        $printizin =  route('admin.print.izinkerja', $id);
        $printcuti =  route('admin.print.cuti', $id);
        $print =  route('admin.print.izin', $id);
        $delete_url = route('admin.libur-nasional.destroylibur', $id);

        $batal_cuti = route('admin.cuti.batal_cuti', $id);
        $batal_izin = route('admin.izin-resmi.batal_izin', $id);

        $for_html = "";
        if ($tipe == 'izin') {
            $for_html = '<a class="btn btn-success btn-xs" title="Print Surat" href="' . $printizin . '"><i class="icofont icofont-download-alt"></i></a>
            <a class="btn btn-danger btn-xs batalizin" href="' . $batal_izin . '">X</a>';
        } elseif ($tipe == 'cuti') {
            $for_html = '<a class="btn btn-success btn-xs" title="Print Surat" href="' . $printcuti . '"><i class="icofont icofont-download-alt"></i></a>
            <a class="btn btn-danger btn-xs batalcuti" href="' . $batal_cuti . '">X</a>';
        } else if ($tipe == 'liburnasional') {
            $for_html = "
                    <div class='d-block text-center'>
                    <a href='javascript:void(0)' data-toggle='tooltip' class='btn btn btn-warning btn-xs align-items-center editLibur'
                    data-id='$id' data-original-title='Edit' title='Edit Libur'><i class='icofont icofont-edit-alt'></i></a>
                    <a href='$delete_url' title='Hapus Libur' class='btn btn-sm btn-danger btn-xs align-items-center hapusLibur'><i class='icofont icofont-trash'></i></a>
                    </div>
                    ";
        } else if ($tipe == 'att_edit') {
            $data = Attendance::where('id', $id)->first();
            if (auth()->user()->role == "admin" || auth()->user()->role == "admin_bsdm") {
                $for_html = ' <a href="#" class="btn btn-info btn-xs editAttendance" data-bs-toggle="modal" data-id="' . $data->id . '"><i class="icofont icofont-pencil-alt-2"></i></a>';
            } else {
                $for_html = '';
            }
        }
        return $for_html;
    }
}


// if (!function_exists('getAksiKu')) {
//     function getAksiKu($id, $tipe, $user = null)
//     {
//         $printizin =  route('admin.print.izinkerja', $id);
//         $printcuti =  route('admin.print.cuti', $id);
//         $print =  route('admin.print.izin', $id);
//         $batal_cuti = route('admin.batal_cuti', $id);
//         $batal_izin = route('admin.batal_izin', $id);
//         $delete_url = route('admin.destroylibur', $id);

//         $for_html = "";
//         if ($tipe == 'izin') {
//             if (auth()->user()->role == "admin" || auth()->user()->role == "admin_bsdm") {
//                 $for_html = '<a class="btn btn-success btn-xs" title="Print Surat" href="' . $printizin . '"><i class="icofont icofont-download-alt"></i></a>';
//             } elseif (auth()->user()->role == "kepalaunit") {
//                 $data = IzinKerja::where('id_izinkerja', $id)->first();
//                 $for_html = '
//                         <a href="#" class="btn btn-primary btn-xs apprvIzin" data-bs-target="#apprvIzin" data-bs-toggle="modal" data-id="' . $data->id_izinkerja . '"><i class="icofont icofont-pencil-alt-2"></i></a>
//                         <a class="btn btn-secondary btn-xs" href="' . $printizin . '"><i class="icofont icofont-download-alt"></i></a>
//                         <a class="btn btn-danger btn-xs batalizin" href="' . $batal_izin . '">X</a> ';
//             }
//         } elseif ($tipe == 'cuti') {
//             if (auth()->user()->role == "admin" || auth()->user()->role == "admin_bsdm") {
//                 $for_html = '<a class="btn btn-success btn-xs" title="Print Surat" href="' . $printcuti . '"><i class="icofont icofont-download-alt"></i></a> ';
//             } elseif (auth()->user()->role == "kepalaunit") {
//                 $data = Cuti::where('id_cuti', $id)->first();
//                 $for_html = '
//                     <a href="#" class="btn btn-primary btn-xs apprvCuti" data-bs-target="#apprvCuti" data-bs-toggle="modal" data-id="' . $data->id_cuti . '"><i class="icofont icofont-pencil-alt-2"></i></a>
//                     <a class="btn btn-success btn-xs" href="' . $printcuti . '"><i class="icofont icofont-download-alt"></i></a> ';
//             }
//         } else if ($tipe == 'liburnasional') {
//             $for_html = "
//                     <div class='d-block text-center'>
//                     <a href='javascript:void(0)' data-toggle='tooltip' class='btn btn btn-warning btn-xs align-items-center editLibur'
//                     data-id='$id' data-original-title='Edit' title='Edit Libur'><i class='icofont icofont-edit-alt'></i></a>
//                     <a href='$delete_url' title='Hapus Libur' class='btn btn-sm btn-danger btn-xs align-items-center hapusLibur'><i class='icofont icofont-trash'></i></a>
//                     </div>
//                     ";
//         } else if ($tipe == 'att') {
//             $data = Attendance::where('id', $id)->first();
//             $izin = Izin::where('id_attendance', $id)->first();
//             if ($izin == NULL) {
//             } else {
//                 $for_html = '
//                 <a class="btn btn-success btn-xs" href="' . $print . '"><i class="icofont icofont-download-alt"></i></a> ';
//             }
//         }
//         return $for_html;
//     }
// }


if (!function_exists('getAprv')) {
    function getAprv($id, $tipe, $alasan = "")
    {
        $batal_cuti = route('admin.cuti.batal_cuti', $id);
        $batal_izin = route('admin.izin-resmi.batal_izin', $id);
        $for_html = "";
        if ($tipe == 'izin') {
            $getDataIzin = IzinKerja::where('id_izinkerja', $id)->first();
            if ($getDataIzin) {
                if ($getDataIzin->approval == 1) {
                    $for_html = '<span class="badge badge-primary">Disetujui Atasan Langsung</span>';
                } else {
                    $for_html = '<span class="badge badge-warning">Menunggu Persetujuan</span> <a class="btn btn-danger btn-xs batalizin" title="Batal Izin" href="' . $batal_izin . '">X</a>';
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
        } else if ($tipe == 'att') {
            $getIzin = Izin::where('id_attendance', $id)->first();
            if ($getIzin) {
                if ($getIzin->approval == 1) {
                    $for_html = '<span class="badge badge-primary">Disetujui Atasan Langsung</span>';
                } else {
                    $for_html = '<span class="badge badge-warning">Menunggu Persetujuan</span>';
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
                    if (date('Y-m-d', $begin) == $value->tanggal  && date("N", $begin) < 5) {
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

if (!function_exists('getPresensi')) {
    function getPresensi($type)
    {
        $data = Attendance::selectRaw('attendance.*, users.name, users.awal_tugas, users.akhir_tugas')->join('users', 'attendance.nip', '=', 'users.nopeg')->get();
        foreach ($data as $row) {
            if ($type == 'duration') {
                if ($row->jam_masuk != NULL && $row->jam_siang == NULL && $row->jam_pulang == NULL) {
                    return '00:00:00';
                } else if ($row->jam_masuk == NULL && $row->jam_siang != NULL && $row->jam_pulang != NULL) {
                    $duration = strtotime($row->jam_pulang) - strtotime($row->jam_siang);
                    $durationwork = date("H:i:s", $duration);
                    return $durationwork;
                } else if ($row->jam_masuk == NULL && $row->jam_siang == NULL && $row->jam_pulang != NULL) {
                    return '00:00:00';
                } else {
                    $time_awalreal =  strtotime($row->jam_masuk);
                    $time_akhirreal = strtotime($row->jam_pulang);
                    $duration = ceil(abs($time_akhirreal - $time_awalreal) - strtotime('01:00:00'));
                    $durationwork = date("H:i:s", $duration);
                    return $durationwork;
                }
            }
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

if (!function_exists('actualDurationWorks')) {
    function actualDurationWorks($jam_masuk, $jam_siang, $jam_pulang, $hari)
    {
        if ($jam_masuk == NULL && $jam_siang == NULL && $jam_pulang != NULL) {
            $durationwork = date('00:00:00');
        } else if ($jam_masuk == NULL && $jam_siang != NULL && $jam_pulang == NULL) {
            $durationwork = date('00:00:00');
        } else if ($jam_masuk != NULL && $jam_siang == NULL && $jam_pulang == NULL) {
            $durationwork = date('00:00:00');
        } else if ($jam_masuk == NULL && $jam_siang != NULL && $jam_pulang != NULL) {
            $akhir = Carbon::createFromFormat("Y-m-d H:i:s", $jam_pulang);
            $awal = Carbon::createFromFormat("Y-m-d H:i:s", $jam_siang);
            $durationwork = $akhir->diff($awal)->format('%H:%I:%S');
        } else if ($jam_masuk != NULL && $jam_siang == NULL && $jam_pulang != NULL) {
            if ($hari == '5') {
                $akhir = Carbon::parse('13:00:00')->format('H:i:s');
                $awal = Carbon::parse($jam_masuk)->format('H:i:s');
                $durasi = strtotime($akhir) - strtotime($awal);
                $durationwork = Carbon::parse($durasi)->format('H:i:s');
            } else {
                $akhir = Carbon::parse('13:30:00')->format('H:i:s');
                $awal = Carbon::parse($jam_masuk)->format('H:i:s');
                $durasi = strtotime($akhir) - strtotime($awal);
                $durationwork = Carbon::parse($durasi)->format('H:i:s');
            }
        } else if ($jam_masuk != NULL && $jam_siang != NULL && $jam_pulang == NULL) {
            $akhir = Carbon::createFromFormat("Y-m-d H:i:s", $jam_siang);
            $awal = Carbon::createFromFormat("Y-m-d H:i:s", $jam_masuk);
            $durationwork = $akhir->diff($awal)->format('%H:%I:%S');
        } else {
            $akhir = Carbon::createFromFormat("Y-m-d H:i:s", $jam_pulang)->subHour(1);
            $awal = Carbon::createFromFormat("Y-m-d H:i:s", $jam_masuk);
            $durationwork = $akhir->diff($awal)->format('%H:%I:%S');
        }
        return $durationwork;
    }
}

if (!function_exists('lateMasuk')) {
    function lateMasuk($jam_masuk, $jam_siang, $hari)
    {
        $masuk = Carbon::parse($jam_masuk)->format('H:i:s');
        $keluar = Carbon::parse('08:00:00')->format('H:i:s');
        if ($hari != '6' && $hari != '0') {
            if ($jam_masuk == NULL &&  $jam_siang != NULL || $jam_masuk == NULL && $jam_siang == NULL) {
                $total = Carbon::parse("00:00:00")->format('H:i:s');
            } else {
                if ($masuk > $keluar) {
                    $durasi = strtotime($masuk) - strtotime($keluar);
                    $total = Carbon::parse($durasi)->format('H:i:s');
                } else {
                    $total = '';
                }
            }
        } else {
            $total = '';
        }
        return $total;
    }
}

if (!function_exists('lateSiang')) {
    function lateSiang($jam_siang, $jam_pulang, $hari)
    {
        $siang = Carbon::parse($jam_siang)->format('H:i:s');
        $keluar1 = Carbon::parse('13:00:00')->format('H:i:s');
        $keluar2 = Carbon::parse('13:30:00')->format('H:i:s');

        if ($hari == '5') {
            if ($jam_siang == NULL && $jam_pulang != NULL) {
                $durasi = strtotime(Carbon::parse($jam_pulang)->format('H:i:s')) - strtotime($keluar2);
                $total = Carbon::parse($durasi)->format('H:i:s');
            } else {
                if ($siang > $keluar2) {
                    $durasi = strtotime($siang) - strtotime($keluar2);
                    $total = Carbon::parse($durasi)->format('H:i:s');
                } else {
                    $total = '';
                }
            }
        } elseif ($hari != '6' && $hari != '0') {
            if ($jam_siang == NULL && $jam_pulang != NULL) {
                $durasi = strtotime(Carbon::parse($jam_pulang)->format('H:i:s')) - strtotime($keluar1);
                $total = Carbon::parse($durasi)->format('H:i:s');
            } else {
                if ($siang > $keluar1) {
                    $durasi = strtotime($siang) - strtotime($keluar1);
                    $total = Carbon::parse($durasi)->format('H:i:s');
                } else {
                    $total = '';
                }
            }
        } else {
            $total = '';
        }
        return $total;
    }
}

if (!function_exists('lateSiang2')) {
    function lateSiang2($jam_siang, $jam_pulang, $hari)
    {
        if ($hari != 6 && $hari != 0) {
            if ($jam_pulang != null &&  $jam_siang != null) {
                $tanggal = Carbon::parse($jam_pulang)->format('Y-m-d');
                $base_time_siang_akhir = ($hari == 5) ? Carbon::parse("$tanggal 13:30:00") : Carbon::parse("$tanggal 13:00:00");
                $base_time_siang_awal = ($hari == 5) ? Carbon::parse("$tanggal 13:15:00") : Carbon::parse("$tanggal 12:45:00");
                $base_time_keluar = Carbon::parse("$tanggal 17:00:00");
                $jam_siang = Carbon::parse($jam_siang);
                $jam_pulang = Carbon::parse($jam_pulang);

                if ($jam_siang->greaterThan($base_time_siang_akhir)) {
                    $telat_siang = $jam_siang->diff($base_time_siang_akhir)->format("%H:%I:%S");
                    $telat_siang = Carbon::parse($telat_siang);
                } else if ($jam_siang->lessThan($base_time_siang_awal)) {
                    $telat_siang = $jam_siang->diff($base_time_siang_awal)->format("%H:%I:%S");
                    $telat_siang = Carbon::parse($telat_siang);
                } else {
                    $telat_siang = Carbon::parse("$tanggal 00:00:00");
                }
                if ($jam_pulang->lessThan($base_time_keluar)) {
                    $telat_pulang = $jam_pulang->diff($base_time_keluar)->format("%H:%I:%S");
                    $telat_pulang = Carbon::parse($telat_pulang);
                } else {
                    $telat_pulang = Carbon::parse("$tanggal 00:00:00");
                }
                $total = $telat_siang->addHours($telat_pulang->format('H'))->addMinutes($telat_pulang->format('i'))->addSeconds($telat_pulang->format('s'))->format("H:i:s");
                return $total;
            } else if ($jam_siang == null && $jam_pulang != null) {
                $tanggal = Carbon::parse($jam_pulang)->format('Y-m-d');
                $base_time_siang_akhir = ($hari == 5) ? Carbon::parse("$tanggal 13:30:00") : Carbon::parse("$tanggal 13:00:00");
                $base_time_siang_awal = ($hari == 5) ? Carbon::parse("$tanggal 13:15:00") : Carbon::parse("$tanggal 12:45:00");
                $base_time_keluar = Carbon::parse("$tanggal 17:00:00");
                $jam_siang = $base_time_siang_akhir;
                $jam_pulang = Carbon::parse($jam_pulang);
                $telat_siang = Carbon::parse("$tanggal 00:00:00");

                if ($jam_pulang->lessThan($base_time_keluar)) {
                    $telat_pulang = $jam_pulang->diff($base_time_keluar)->format("%H:%I:%S");
                    $telat_pulang = Carbon::parse($telat_pulang);
                } else {
                    $telat_pulang = Carbon::parse("$tanggal 00:00:00");
                }

                $total = $telat_siang->addHours($telat_pulang->format('H'))->addMinutes($telat_pulang->format('i'))->addSeconds($telat_pulang->format('s'))->format("H:i:s");
                return $total;
            } else if ($jam_siang != null && $jam_pulang == null) {
                $tanggal = Carbon::parse($jam_siang)->format('Y-m-d');
                $base_time_siang_akhir = ($hari == 5) ? Carbon::parse("$tanggal 13:30:00") : Carbon::parse("$tanggal 13:00:00");
                $base_time_siang_awal = ($hari == 5) ? Carbon::parse("$tanggal 13:15:00") : Carbon::parse("$tanggal 12:45:00");
                $base_time_keluar = Carbon::parse("$tanggal 17:00:00");
                $jam_siang = Carbon::parse($jam_siang);
                $jam_pulang = $base_time_keluar;
                $telat_pulang = Carbon::parse("$tanggal 00:00:00");

                if ($jam_siang->greaterThan($base_time_siang_akhir)) {
                    $telat_siang = Carbon::parse('00:00:00');
                } else {
                    $telat_siang = Carbon::parse("$tanggal 00:00:00");
                }

                $total = $telat_siang->addHours($telat_pulang->format('H'))->addMinutes($telat_pulang->format('i'))->addSeconds($telat_pulang->format('s'))->format("H:i:s");
                return $total;
            } else {
                $total = Carbon::parse('00:00:00')->format('H:i:s');
                return $total;
            }
        } else {
            $total = Carbon::parse('00:00:00')->format('H:i:s');
            return $total;
        }
    }
}

if (!function_exists('getDurasi')) {
    function getDurasi($jam_masuk, $jam_siang, $jam_pulang)
    {
        if (!empty($jam_masuk) && !empty($jam_siang) && !empty($jam_pulang)) {
            return '08:00:00';
        }
        // Jika tidak ada sama sekali
        else if (empty($jam_masuk) && empty($jam_siang) && empty($jam_pulang)) {
            return '00:00:00';
        }
        // Jika hanya ada sore yang terisi
        else if (empty($jam_masuk) && empty($jam_siang) && !empty($jam_pulang)) {
            return '00:00:00';
        }
        // Jika hanya ada siang yang terisi
        else if (empty($jam_masuk) && !empty($jam_siang) && empty($jam_pulang)) {
            return '00:00:00';
        }
        // Jika hanya ada pagi yang terisi
        else if (!empty($jam_masuk) && empty($jam_siang) && empty($jam_pulang)) {
            return '00:00:00';
        }
        // Jika hanya ada siang dan sore terisi
        else if (empty($jam_masuk) && !empty($jam_siang) && !empty($jam_pulang)) {
            return '04:00:00';
        }
        // Jika hanya ada pagi dan sore terisi
        else if (!empty($jam_masuk) && empty($jam_siang) && !empty($jam_pulang)) {
            return '04:00:00';
        }
        // Jika hanya ada pagi dan siang terisi
        else if (!empty($jam_masuk) && !empty($jam_siang) && empty($jam_pulang)) {
            return '04:00:00';
        }
    }
}
