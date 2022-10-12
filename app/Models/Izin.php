<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    // use HasFactory;
    protected $table = 'izin';
    protected $guarded = [];
    protected $primaryKey = 'id_izin';

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit', 'id');
    }

    public function jadwal_satpam()
    {
        $this->morphMany(JadwalSatpam::class, 'tagable', 'tagable_type', 'tagable_id');
    }
}
