<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'company_id'   => (int) $this->company_id,
            'name'         => $this->name,
            'phone_number' => $this->phone_number,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'notes'        => NoteResource::collection($this->whenLoaded('notes')),
        ];
    }
}
