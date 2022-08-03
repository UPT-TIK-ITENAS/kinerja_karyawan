<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JenisCuti extends Model
{
    use HasFactory;
    public function jenis_cuti(){
        $jenis_cuti=DB::table('jenis_cuti')->get();
        return $jenis_cuti;
    }
}
