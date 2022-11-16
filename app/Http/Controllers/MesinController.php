<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Mesin;

class MesinController extends Controller
{
    public function index()
    {
        $att = Mesin::get();
        return view('admin.mesin', compact('att'));
    }

    public function editmesin($id)
    {
        $mesin = Mesin::where('id', $id)->first();
        return response()->json($mesin);
    }

    public function updatemesin(Request $request)
    {
        Mesin::where('id', $request->id)->update([
            'name' => $request->name,
            'remark' => $request->remark,
            'ipaddress' => $request->ipaddress,
            'port' => $request->port,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.mesinsidikjari')->with('success', 'Edit Data Berhasil!');
    }

    public function createmesin(Request $request)
    {
        Mesin::create([
            'name' => $request->namee,
            'remark' => $request->remarkk,
            'ipaddress' => $request->ipaddresss,
            'port' => $request->portt,
            'status' => $request->statuss,
        ]);

        return redirect()->route('admin.mesinsidikjari')->with('success', 'Add Data Berhasil!');
    }

    public function destroymesin($id)
    {
        Mesin::where('id', $id)->delete();
        return redirect()->route('admin.mesinsidikjari')->with('success', 'Data berhasil dihapus!');
    }
}
