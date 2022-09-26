<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kuesioner;
use Illuminate\Support\Facades\DB;

class KuesionerController extends Controller
{
    public function kuesioner()
    {
        $pertanyaan_kuesioner = Kuesioner::get();
        $jawaban_pertanyaan = DB::table('jawaban_pertanyaan')->get();

        return view('admin.kuesioner', compact('pertanyaan_kuesioner','jawaban_pertanyaan'));
    }

    public function storekuesioner(Request $request)
    {
        $request->validate([
            // 'nrp' => 'required',
            'pertanyaan1' => 'required',
            'pertanyaan2' => 'required',
            'pertanyaan3' => 'required',
            'pertanyaan4' => 'required',
            'pertanyaan5' => 'required',
            'pertanyaan6' => 'required',
            'pertanyaan7' => 'required',
        ]);
        return redirect()->route('pertanyaan')->with('success', 'Kuesioner Berhasil Disimpan!');
    }
}
