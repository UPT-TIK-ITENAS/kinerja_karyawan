<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class MigrateAttendance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	
	protected $from_table, $to_table;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($from_table, $to_table)
	{
		$this->from_table = $from_table;
		$this->to_table = $to_table;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
	    $data = DB::table($this->from_table)->get();
	    foreach ($data as $d) {
		    // Jika full terisi
		    if (!empty($d->jam_masuk) && !empty($d->jam_siang) && !empty($d->jam_pulang)) {
			    DB::table($this->to_table)->insert([
				    'nip' => $d->nip,
				    'hari' => $d->hari,
				    'tanggal' => $d->tanggal,
				    'jam_masuk' => $d->jam_masuk,
				    'jam_siang' => $d->jam_siang,
				    'jam_pulang' => $d->jam_pulang,
				    'status' => 1,
				    'telat_masuk' => lateMasuk($d->jam_masuk, $d->jam_siang, $d->hari),
				    'telat_siang' => lateSiang2($d->jam_siang, $d->jam_pulang, $d->hari),
				    'durasi' => '08:00:00',
				    'modify_by' => $d->modify_by,
				    'is_cuti' => $d->is_cuti,
				    'is_izin' => $d->is_izin,
				    'is_dispen' => $d->is_dispen,
			    ]);
		    }
		    // Jika tidak ada sama sekali
		    else if (empty($d->jam_masuk) && empty($d->jam_siang) && empty($d->jam_pulang)) {
			    DB::table($this->to_table)->insert([
				    'nip' => $d->nip,
				    'hari' => $d->hari,
				    'tanggal' => $d->tanggal,
				    'jam_masuk' => $d->jam_masuk,
				    'jam_siang' => $d->jam_siang,
				    'jam_pulang' => $d->jam_pulang,
				    'status' => 0,
				    'telat_masuk' => lateMasuk($d->jam_masuk, $d->jam_siang, $d->hari),
				    'telat_siang' => lateSiang2($d->jam_siang, $d->jam_pulang, $d->hari),
				    'durasi' => '00:00:00',
				    'modify_by' => $d->modify_by,
				    'is_cuti' => $d->is_cuti,
				    'is_izin' => $d->is_izin,
				    'is_dispen' => $d->is_dispen,
			    ]);
		    }
		    // Jika hanya ada sore yang terisi
		    else if (empty($d->jam_masuk) && empty($d->jam_siang) && !empty($d->jam_pulang)) {
			    DB::table($this->to_table)->insert([
				    'nip' => $d->nip,
				    'hari' => $d->hari,
				    'tanggal' => $d->tanggal,
				    'jam_masuk' => $d->jam_masuk,
				    'jam_siang' => $d->jam_siang,
				    'jam_pulang' => $d->jam_pulang,
				    'status' => 0,
				    'telat_masuk' => lateMasuk($d->jam_masuk, $d->jam_siang, $d->hari),
				    'telat_siang' => lateSiang2($d->jam_siang, $d->jam_pulang, $d->hari),
				    'durasi' => '00:00:00',
				    'modify_by' => $d->modify_by,
				    'is_cuti' => $d->is_cuti,
				    'is_izin' => $d->is_izin,
				    'is_dispen' => $d->is_dispen,
			    ]);
		    }
		    // Jika hanya ada siang yang terisi
		    else if (empty($d->jam_masuk) && !empty($d->jam_siang) && empty($d->jam_pulang)) {
			    DB::table($this->to_table)->insert([
				    'nip' => $d->nip,
				    'hari' => $d->hari,
				    'tanggal' => $d->tanggal,
				    'jam_masuk' => $d->jam_masuk,
				    'jam_siang' => $d->jam_siang,
				    'jam_pulang' => $d->jam_pulang,
				    'status' => 0,
				    'telat_masuk' => lateMasuk($d->jam_masuk, $d->jam_siang, $d->hari),
				    'telat_siang' => lateSiang2($d->jam_siang, $d->jam_pulang, $d->hari),
				    'durasi' => '00:00:00',
				    'modify_by' => $d->modify_by,
				    'is_cuti' => $d->is_cuti,
				    'is_izin' => $d->is_izin,
				    'is_dispen' => $d->is_dispen,
			    ]);
		    }
		    // Jika hanya ada pagi yang terisi
		    else if (!empty($d->jam_masuk) && empty($d->jam_siang) && empty($d->jam_pulang)) {
			    DB::table($this->to_table)->insert([
				    'nip' => $d->nip,
				    'hari' => $d->hari,
				    'tanggal' => $d->tanggal,
				    'jam_masuk' => $d->jam_masuk,
				    'jam_siang' => $d->jam_siang,
				    'jam_pulang' => $d->jam_pulang,
				    'status' => 0,
				    'telat_masuk' => lateMasuk($d->jam_masuk, $d->jam_siang, $d->hari),
				    'telat_siang' => lateSiang2($d->jam_siang, $d->jam_pulang, $d->hari),
				    'durasi' => '00:00:00',
				    'modify_by' => $d->modify_by,
				    'is_cuti' => $d->is_cuti,
				    'is_izin' => $d->is_izin,
				    'is_dispen' => $d->is_dispen,
			    ]);
		    }
		    // Jika hanya ada siang dan sore terisi
		    else if (empty($d->jam_masuk) && !empty($d->jam_siang) && !empty($d->jam_pulang)) {
			    DB::table($this->to_table)->insert([
				    'nip' => $d->nip,
				    'hari' => $d->hari,
				    'tanggal' => $d->tanggal,
				    'jam_masuk' => $d->jam_masuk,
				    'jam_siang' => $d->jam_siang,
				    'jam_pulang' => $d->jam_pulang,
				    'status' => 0,
				    'telat_masuk' => lateMasuk($d->jam_masuk, $d->jam_siang, $d->hari),
				    'telat_siang' => lateSiang2($d->jam_siang, $d->jam_pulang, $d->hari),
				    'durasi' => '04:00:00',
				    'modify_by' => $d->modify_by,
				    'is_cuti' => $d->is_cuti,
				    'is_izin' => $d->is_izin,
				    'is_dispen' => $d->is_dispen,
			    ]);
		    }
		    // Jika hanya ada pagi dan sore terisi
		    else if (!empty($d->jam_masuk) && empty($d->jam_siang) && !empty($d->jam_pulang)) {
			    DB::table($this->to_table)->insert([
				    'nip' => $d->nip,
				    'hari' => $d->hari,
				    'tanggal' => $d->tanggal,
				    'jam_masuk' => $d->jam_masuk,
				    'jam_siang' => $d->jam_siang,
				    'jam_pulang' => $d->jam_pulang,
				    'status' => 0,
				    'telat_masuk' => lateMasuk($d->jam_masuk, $d->jam_siang, $d->hari),
				    'telat_siang' => lateSiang2($d->jam_siang, $d->jam_pulang, $d->hari),
				    'durasi' => '04:00:00',
				    'modify_by' => $d->modify_by,
				    'is_cuti' => $d->is_cuti,
				    'is_izin' => $d->is_izin,
				    'is_dispen' => $d->is_dispen,
			    ]);
		    }
		    // Jika hanya ada pagi dan siang terisi
		    else if (!empty($d->jam_masuk) && !empty($d->jam_siang) && empty($d->jam_pulang)) {
			    DB::table($this->to_table)->insert([
				    'nip' => $d->nip,
				    'hari' => $d->hari,
				    'tanggal' => $d->tanggal,
				    'jam_masuk' => $d->jam_masuk,
				    'jam_siang' => $d->jam_siang,
				    'jam_pulang' => $d->jam_pulang,
				    'status' => 0,
				    'telat_masuk' => lateMasuk($d->jam_masuk, $d->jam_siang, $d->hari),
				    'telat_siang' => lateSiang2($d->jam_siang, $d->jam_pulang, $d->hari),
				    'durasi' => '04:00:00',
				    'modify_by' => $d->modify_by,
				    'is_cuti' => $d->is_cuti,
				    'is_izin' => $d->is_izin,
				    'is_dispen' => $d->is_dispen,
			    ]);
		    }
	    }
    }
}
