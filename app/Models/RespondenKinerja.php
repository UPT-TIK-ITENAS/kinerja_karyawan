<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespondenKinerja extends Model
{
    use HasFactory;
    protected $table = 'responden_kuisioner';
    protected $guarded = [];

    public function kuesioner()
    {
        return $this->belongsTo(KuesionerKinerja::class);
    }

    public function detail()
    {
        return $this->hasMany(DetailRespondenKinerja::class);
    }

    // public function prodi()
    // {
    //     return $this->belongsTo(Prodi::class, 'kode_prodi', 'kode', 'kode_fakultas', 'kode_fakultas');
    // }
}
