<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KuesionerKinerja extends Model
{
    use HasFactory;
    protected $table = 'kuisioner_periode';
    protected $guarded = [];

    public function pertanyaan()
    {
        return $this->hasMany(PertanyaanKinerja::class);
    }

    public function responden()
    {
        return $this->hasMany(RespondenKinerja::class);
    }
}
