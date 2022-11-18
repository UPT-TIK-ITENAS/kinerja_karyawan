<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kuesioner;
use App\Models\Attendance;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\IzinKerja;
use App\Models\JenisCuti;
use App\Models\Jabatan;
use App\Models\JenisIzin;
use App\Models\JawabanKinerja;
use App\Models\KuesionerKinerja;
use App\Models\DetailRespondenKinerja;
use App\Models\PertanyaanKinerja;
use App\Models\RespondenKinerja;
use App\Models\QR;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\Debugbar\Facades\Debugbar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class KuesionerController extends Controller
{
    public function indexKuesioner()
    {
        //$kuesioner = KuesionerKinerja::forMahasiswa()->orderBy('id', 'desc')->get();
        $kuesioner = DB::table('kuisioner_periode')->get();
        return view('kuesioner.index_kuesioner', compact('kuesioner'));
    }

    public function showKuesioner($id)
    {
        $kuesioner = KuesionerKinerja::with(['pertanyaan' => function ($query) {
            return $query->orderBy('nomor', 'asc');
        }, 'pertanyaan.jawaban'])->find($id);
        return view('kuesioner.show_kuesioner', compact('kuesioner'));
    }

    public function storeKuesioner(Request $request, $id)
    {
        $request->validate([
            'responden.*.jawaban_kinerja' => 'required',
            'responden.*.pertanyaan_kuisioner_id' => 'required',
            'nopeg' => 'required',
            'nama_pegawai' => 'required',
        ]);

        $kuesioner = KuesionerKinerja::find($id);

        DB::transaction(function () use ($kuesioner, $request) {
            $total = 0;
            foreach ($request->responden as $key => $value) {
                $total += DB::table('jawaban_kinerja')->where('id', $value['jawaban_kinerja'])->value('nilai');
            }
            $indeks = round($total / count($request->responden), 2);

            $kuesionerResponden = RespondenKinerja::create([
                'kuesioner_kinerja_id' => $kuesioner->id,
                'nama_pegawai' =>  $request->nama_pegawai,
                'nopeg' => $request->nopeg,
                'unit' => $request->unit,
                'jabatan' => $request->jabatan,
                'nopeg_nilai' => $request->nopeg_nilai,
                'nama_penilai' => $request->nama_penilai,
                'jabatan_penilai' => $request->kode_rek,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // insert detail_respon_akademik
            foreach ($request->responden as $key => $value) {
                DB::table('detail_respon_kinerja')->insert([
                    'responden_kuisioner_id' => $kuesionerResponden->id,
                    'pertanyaan_kuisioner_id' => $value['pertanyaan_kuisioner_id'],
                    'jawaban_kinerja' => $value['jawaban_kinerja'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
        return redirect()->back()->with('success', 'Berhasil mengirimkan kuesioner!');
    }
}
