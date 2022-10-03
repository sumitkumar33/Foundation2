<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class defaultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // var_dump($request);
        return ([
            $request->name,
        ]); 
    }
}
