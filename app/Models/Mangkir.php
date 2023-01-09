<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mangkir extends Model
{
    // use HasFactory;
    protected $table = 'mangkir';
    protected $guarded = [];
    protected $primaryKey = 'id_mangkir';

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit', 'id');
    }
}
