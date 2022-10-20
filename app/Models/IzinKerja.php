<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinKerja extends Model
{
    // use HasFactory;
    protected $table = 'izin_kerja';
    protected $guarded = [];
    protected $primaryKey = 'id_izinkerja';
    protected $with = ['jenisizin'];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit', 'id');
    }

    public function jadwal_satpam()
    {
        $this->morphMany(JadwalSatpam::class, 'tagable', 'tagable_type', 'tagable_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'nopeg', 'nopeg');
    }

    public function jenisizin()
    {
        return $this->belongsTo(JenisIzin::class, 'jenis_izin', 'id_jenisizin');
    }
}
