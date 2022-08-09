<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\JenisIzin;
use App\Models\IzinKerja;

if (!function_exists('getCheck')) {
    function getCheck($jenis_izin,$id,$tipe)
    {
        $cek = IzinKerja::where('jenis_izin', $jenis_izin)->where('id_izinkerja', $id)->first();
        if ($tipe == 'check') {
            if (empty($cek)) {
                $td = "";
            } else {
                $td = $cek->tgl_awal_izin.' s/d '.$cek->tgl_akhir_izin;
            }
        }elseif($tipe == 'sakit'){
            if($cek->jenis_izin == 'sakit'){
                $td = $cek->tgl_awal_izin.' s/d ' .$cek->tgl_akhir_izin;
            }else{
                $td = "................s/d................";
            }
        }
        return $td;
    }
}