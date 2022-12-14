<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceBaru extends Model
{
    // use HasFactory;
    protected $table = 'attendance_baru';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'nip', 'nopeg');
    }

    public function izin()
    {
        return $this->hasOne(Izin::class, 'id_attendance', 'id');
    }
}
