<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\Cuti;
use App\Models\IzinKerja;
use App\Models\JenisIzin;
use App\Models\Jabatan;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintController extends Controller
{
    public function printcuti($id)
    {

        $data = Cuti::join('unit', 'cuti.unit', '=', 'unit.id')->join('users', 'cuti.nopeg', '=', 'users.nopeg')->join('jenis_cuti', 'cuti.jenis_cuti', '=', 'jenis_cuti.id_jeniscuti')->where('id_cuti', $id)->first();
        $atasan = Jabatan::selectRaw('users.atasan,jabatan.*')->join('users', 'users.atasan', '=', 'jabatan.id')->where('users.atasan', $data->atasan)->first();
        $atasan_lang = Jabatan::selectRaw('users.atasan_lang,jabatan.*')->join('users', 'users.atasan_lang', '=', 'jabatan.id')->where('users.atasan_lang', $data->atasan_lang)->first();
        // dd($atasan_lang);

        $pdf = PDF::loadview('print.printcuti', compact('data', 'atasan', 'atasan_lang'))->setPaper('potrait');
        return $pdf->stream();
    }

    public function printizin($id)
    {
        $data = Izin::join('users', 'izin.nopeg', '=', 'users.nopeg')->join('unit', 'users.unit', '=', 'unit.id')->where('id_izin', $id)->first();
        $atasan = Jabatan::selectRaw('users.atasan,jabatan.*')->join('users', 'users.atasan', '=', 'jabatan.id')->where('users.atasan', $data->atasan)->first();
        // dd($data);

        if ($data->jenis == 1) {
            $pdf = PDF::loadview('print.printizin', compact('data', 'atasan'))->setPaper('A5', 'landscape');
            return $pdf->stream();
        } else if ($data->jenis == 2) {
            $pdf = PDF::loadview('print.printizinsj', compact('data', 'atasan'))->setPaper('A5', 'landscape');
            return $pdf->stream();
        } else {
            $pdf = PDF::loadview('print.printizindp', compact('data', 'atasan'))->setPaper('A5', 'landscape');
            return $pdf->stream();
        }
    }

    public function printizinkerja($id)
    {
        $data = IzinKerja::join('users', 'izin_kerja.nopeg', '=', 'users.nopeg')->join('unit', 'izin_kerja.unit', '=', 'unit.id')->where('id_izinkerja', $id)->first();
        // $atasan = User::selectRaw('jabatan.*')->join('jabatan', 'jabatan.id', 'users.atasan')->where('jabatan.nopeg', $data->atasan)->first();
        $atasan = Jabatan::selectRaw('users.atasan,jabatan.*')->join('users', 'users.atasan', '=', 'jabatan.id')->where('users.atasan', $data->atasan)->first();
        $jenisizin = JenisIzin::where('jenis_izin', '!=', 'sakit')->get();

        $pdf = PDF::loadview('print.printizinkerja', compact('data', 'jenisizin', 'atasan'))->setPaper('potrait');
        return $pdf->stream();
    }
}
