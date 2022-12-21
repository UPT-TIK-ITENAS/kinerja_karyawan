<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Unit;
use App\Models\Jabatan;

class ListKaryawanController extends Controller
{

    public function index()
    {
        $peg = User::SelectRaw('users.*,users.id as iduser,unit.*, jb1.nopeg as peg_jab, jb1.nama as name_jab, jb2.nopeg as peg_jab2, jb2.nama as name_jab2')
            ->join('unit', 'users.unit', '=', 'unit.id')
            ->join('jabatan as jb1', 'users.atasan', '=', 'jb1.id')
            ->join('jabatan as jb2', 'users.atasan_lang', '=', 'jb2.id')
            ->where('unit', '!=', '29')
            ->get();
        $unit = Unit::get();
        $jabatan = Jabatan::get();

        return view('admin.karyawan', compact('peg', 'unit', 'jabatan'));
    }

    public function editKaryawan($id)
    {
        $user =  User::SelectRaw('users.*,users.id as iduser,unit.*, jb1.nopeg as peg_jab, jb1.nama as name_jab, jb2.nopeg as peg_jab2, jb2.nama as name_jab2')
            ->join('unit', 'users.unit', '=', 'unit.id')
            ->join('jabatan as jb1', 'users.atasan', '=', 'jb1.id')
            ->join('jabatan as jb2', 'users.atasan_lang', '=', 'jb2.id')
            ->where('unit', '!=', '29')
            ->where('users.id', $id)->first();
        return response()->json($user);
    }
}
