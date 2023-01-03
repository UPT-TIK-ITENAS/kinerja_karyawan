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
            'start' => $this->tanggal,
            'end' => $this->tanggal,
            'title' => $this->whenLoaded('user', function () {
                return  'Hadir';
            }),
            'allDay' => true,
            'color' => '#24695c',
        ];

        // if ($this->whenLoaded('tagable')) {
        //     $resources['title'] = "Pengganti|" . $this->tagable->nopeg . " - " . $this->tagable->name . " - " . Str::ucfirst($this->shift_awal);
        // }
        return $resources;
    }
}
