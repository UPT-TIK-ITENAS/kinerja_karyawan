<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ListKaryawanController extends Controller
{

    public function index()
    {
        $peg =  $user = User::SelectRaw('users.*,unit.*, jb1.nopeg as peg_jab, jb1.nama as name_jab, jb2.nopeg as peg_jab2, jb2.nama as name_jab2')
            ->join('unit', 'users.unit', '=', 'unit.id')
            ->join('jabatan as jb1', 'users.atasan', '=', 'jb1.id')
            ->join('jabatan as jb2', 'users.atasan_lang', '=', 'jb2.id')
            ->wherenot('unit', '29')
            ->get();

        return view('admin.karyawan', compact('peg'));
    }
}
