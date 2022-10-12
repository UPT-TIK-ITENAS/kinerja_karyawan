<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class JadwalSatpamCalendarResource extends JsonResource
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
        return [
            'id' => $this->id,
            'start' => $this->tanggal_awal,
            'end' => $this->tanggal_akhir,
            'title' => $this->whenLoaded('user', function () {
                return $this->user->nopeg . " - " . $this->user->name . " - " . Str::ucfirst($this->shift_awal);
            }),
            'allDay' => ($this->shift_awal == 'off') ? true : false,
            'color' => ($this->shift_awal == 'pagi') ? '#24695c' : (($this->shift_awal == 'pagi1') ? '#03bd9e' : (($this->shift_awal == 'siang') ? '#ba895d' : (($this->shift_awal == 'malam') ? '#000000' : '#f44336'))),
        ];
    }
}
