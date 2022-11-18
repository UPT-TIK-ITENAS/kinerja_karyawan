<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailRespondenKinerja extends Model
{
    use HasFactory;
    protected $table = 'detail_respon_kinerja';
    protected $guarded = [];

    public function responden()
    {
        return $this->belongsTo(RespondenKinerja::class);
    }
}
