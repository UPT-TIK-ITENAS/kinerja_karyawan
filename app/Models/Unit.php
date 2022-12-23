<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'unit';
    protected $guarded = [];


    public function user()
    {
        return $this->hasMany(User::class, 'unit', 'id');
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }
}
