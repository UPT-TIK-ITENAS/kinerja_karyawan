<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalSatpam extends Model
{
    use HasFactory;

    protected $table = 'jadwal_satpam';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'nip', 'nopeg');
    }

    public function tagable()
    {
        return $this->morphTo('tagable');
    }

    public function pengganti()
    {
        return $this->belongsTo(User::class, 'nip_pengganti', 'nopeg');
    }
}
