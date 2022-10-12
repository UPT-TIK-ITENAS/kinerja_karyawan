<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    // use HasFactory;
    protected $table = 'cuti';
    protected $guarded = [];
    protected $primaryKey = 'id_cuti';

    public function jenis_cuti()
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_cuti', 'id_jeniscuti');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit', 'id');
    }
}
