<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanKinerja extends Model
{
    use HasFactory;
    protected $table = 'jawaban_kinerja';
    protected $guarded = [];

    public function pertanyaan()
    {
        return $this->belongsTo(PertanyaanKinerja::class);
    }

    public function responden()
    {
        return $this->belongsTo(RespondenKinerja::class);
    }
}
