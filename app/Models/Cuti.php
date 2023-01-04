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
    protected $with = ['jeniscuti'];

    public function jeniscuti()
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_cuti', 'id_jeniscuti');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit', 'id');
    }

    public function jadwal_satpam()
    {
        return $this->morphMany(JadwalSatpam::class, 'tagable', 'tagable_type', 'tagable_id')->using(JenisCuti::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'nopeg', 'nopeg');
    }
}
