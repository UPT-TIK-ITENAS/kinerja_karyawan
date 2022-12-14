<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nopeg',
        'role',
        'npp',
        'unit',
        'atasan',
        'atasan_lang',
        'masuk_kerja',
        'tempat',
        'tanggal_lahir',
        'status',
        'fungsi',
        'awal_tugas',
        'akhir_tugas',
        'gedung',
        'email',
        'nohp',
        'sisacuti',
        'last_login'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function jadwal_satpam()
    {
        return $this->hasMany(JadwalSatpam::class, 'nip', 'nopeg');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit', 'id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'nip', 'nopeg');
    }

    public function izin()
    {
        return $this->hasMany(Izin::class, 'nopeg', 'nopeg');
    }
	public function atasan()
	{
		return $this->belongsTo(Jabatan::class, 'atasan', 'id');
	}
	public function atasan_langsung()
	{
		return $this->belongsTo(Jabatan::class, 'atasan_lang', 'id');
	}
}
