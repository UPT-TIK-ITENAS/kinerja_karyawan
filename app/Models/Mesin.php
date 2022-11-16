<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Mesin extends Model
{
    // use HasFactory;
    protected $table = 'biometricmachine';
    protected $guarded = [];
}
