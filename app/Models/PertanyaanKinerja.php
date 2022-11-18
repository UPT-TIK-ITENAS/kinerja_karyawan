<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertanyaanKinerja extends Model
{
    use HasFactory;
    protected $table = 'pertanyaan_kinerja';
    protected $guarded = [];

    public function jawaban()
    {
        return $this->hasMany(JawabanKinerja::class);
    }

    public function kuesioner()
    {
        return $this->belongsTo(KuesionerKinerja::class);
    }
}
