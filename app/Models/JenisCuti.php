<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JenisCuti extends Model
{
    // use HasFactory;
    protected $table = 'jenis_cuti';
    protected $guarded = [];
    protected $primaryKey = ' id_jeniscuti';

    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }
}
