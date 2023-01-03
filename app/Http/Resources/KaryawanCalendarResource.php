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
		
        $resources =  [
            'id' => $this->id,
            'start' => ($this->type == "attendance") ? $this->tanggal : $this->tgl_awal_cuti,
            'end' => ($this->type == "attendance") ? $this->tanggal : $this->tgl_akhir_cuti,
            'title' => ($this->type == "attendance" && $this->is_cuti == 0) ? 'Hadir' : ($this->type == "attendance" && $this->is_cuti == 1 ? 'Cuti' : 'Cuti'),
            'allDay' => true,
            'color' => ($this->type == "attendance" && $this->is_cuti == 0) ? '#24695c' : ($this->type == "attendance" && $this->is_cuti == 1 ? '#03bd9e' : '#f44336'),
        ];

        return $resources;
    }
}
