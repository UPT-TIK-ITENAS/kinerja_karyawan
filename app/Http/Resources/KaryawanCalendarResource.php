<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class KaryawanCalendarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public static $wrap = null;

    public function toArray($request)
    {
		if($this->type == "attendance"){
			return [
				'id' => $this->id,
				'start' => $this->tanggal,
				'end' => $this->tanggal,
				'title' => ($this->is_cuti == 0 && $this->is_izin == 0) ? 'Hadir' : (($this->is_cuti == 1) ? 'Cuti' : 'Izin'),
				'allDay' => true,
				'color' => ($this->is_cuti == 0 && $this->is_izin == 0) ? '#24695c' : (($this->is_cuti == 1) ? '#03bd9e' : '#f44336'),
				'type' => 'attendance',
			];
		}
		elseif($this->type == "cuti"){
			return [
				'id' => $this->id_cuti,
				'start' => $this->tgl_awal_cuti,
				'end' => $this->tgl_akhir_cuti,
				'title' => 'Cuti',
				'allDay' => true,
				'color' => '#f44336',
				'type' => 'cuti',
			];
		}
		elseif($this->type == "izin"){
			return [
				'id' => $this->id_izinkerja,
				'start' => $this->tgl_awal_izin,
				'end' => $this->tgl_akhir_izin,
				'title' => 'Izin',
				'allDay' => true,
				'color' => '#f44336',
				'type' => 'izin',
			];
		}
		elseif($this->type == "libur"){
			return [
				'id' => $this->id,
				'start' => $this->tanggal,
				'end' => $this->tanggal,
				'title' => 'Libur Nasional',
				'allDay' => true,
				'display' => 'background',
				'color' => '#f44336',
				'type' => 'libur',
			];
		}
    }
}
