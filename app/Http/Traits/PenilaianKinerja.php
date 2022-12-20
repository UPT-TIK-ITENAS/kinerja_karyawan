<?php

namespace App\Http\Traits;

use App\Models\RespondenKinerja;
use Illuminate\Support\Facades\DB;

trait PenilaianKinerja
{
    protected $bobot_izin = 13;
    protected $bobot_sakit = 11;
    protected $bobot_mangkir = 21;
    protected $bobot_terlambat = 15;

    protected $maks_izin = 16; // 16 jam
    // protected $maks_izin = 2; // 2 hari
    // protected $maks_sakit = 16; // 16 jam
    protected $maks_sakit = 2; // 2 hari
    protected $maks_mangkir = 0; // 1 hari
    protected $maks_terlambat = 300; // 300 menit

    protected $skor = [];


    public function penilaian($nopeg, $periode)
    {
        $data = DB::select("CALL HitungTotalHariKerja('$nopeg', '$periode->batas_awal', '$periode->batas_akhir')");

        // $this->bobot_izin = 13;
        // $this->bobot_sakit = 11;
        // $this->bobot_mangkir = 21;
        // $this->bobot_terlambat = 15;
        // // $this->maks_izin = 16; // 16 jam
        // $this->maks_izin = 2; // 2 hari
        // $this->maks_sakit = 16; // 16 jam
        // $this->maks_mangkir = 0; // 1 hari
        // $this->maks_terlambat = 300; // 300 menit

        $skor = [];
        foreach ($data as $key => $value) {
            if ($value->total_izin >= $this->maks_izin) {
                $skor['izin'][$key] = 0;
                $skor['avg']['izin'] = 0;
            } else {
                $skor['izin'][$key] = round($this->bobot_izin * ($this->maks_izin - $value->total_izin) / $this->maks_izin, 2);
            }

            if ($value->izin_sakit >= $this->maks_sakit) {
                $skor['sakit'][$key] = 0;
                $skor['avg']['sakit'] = 0;
            } else {
                $skor['sakit'][$key] = round($this->bobot_sakit * ($this->maks_sakit - $value->izin_sakit) / $this->maks_sakit, 2);
            }

            if ($value->total_hari_mangkir >= $this->maks_mangkir) {
                $skor['mangkir'][$key] = 0;
                $skor['avg']['mangkir'] = 0;
            } else {
                $skor['mangkir'][$key] = $this->bobot_mangkir;
            }

            if ($value->kurang_jam > $this->maks_terlambat) {
                $skor['kurang_jam'][$key] = 0;
                $skor['avg']['kurang_jam'] = 0;
            } else {
                $skor['kurang_jam'][$key] = round($this->bobot_terlambat * ($this->maks_terlambat - $value->kurang_jam) / $this->maks_terlambat, 2);
            }
        }

        $skor['avg']['izin'] = (array_sum(array_column($data, 'total_izin')) > $this->maks_izin) ? 0 : round(array_sum($skor['izin']) / count($skor['izin']), 2);
        $skor['avg']['sakit'] = (array_sum(array_column($data, 'izin_sakit')) > $this->maks_sakit) ? 0 : round(array_sum($skor['sakit']) / count($skor['sakit']), 2);
        $skor['avg']['mangkir'] = (array_sum(array_column($data, 'total_hari_mangkir')) >= $this->maks_mangkir) ? 0 : round(array_sum($skor['mangkir']) / count($skor['mangkir']), 2);
        $skor['avg']['kurang_jam'] = (array_sum(array_column($data, 'kurang_jam')) >= $this->maks_terlambat) ? 0 : round(array_sum($skor['kurang_jam']) / count($skor['kurang_jam']), 2);

        $this->skor = $skor;

        return $skor;
    }

    public function penilaian_atasan($nopeg, $periode)
    {
        $data = RespondenKinerja::where('nopeg', $nopeg)->where('kuisioner_kinerja_id', $periode->id)->first();
        $indeks = $data->indeks ?? 0;
        // convert indeks from 1-4 to 1-100
        $indeks = ($indeks / 4) * 100;
        return $indeks;
    }
}
