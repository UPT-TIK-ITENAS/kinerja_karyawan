<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    // use HasFactory;
    protected $table = 'attendance';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'nip', 'nopeg');
    }
}
