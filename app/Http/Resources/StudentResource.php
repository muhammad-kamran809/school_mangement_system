<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'class_name' => $this->class_name,
            'section' => $this->section,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'address' => $this->address,
            'guardian_name' => $this->guardian_name,
            'guardian_phone' => $this->guardian_phone,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
