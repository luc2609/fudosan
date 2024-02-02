<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->first_name . ' ' . $this->last_name,
            'furigana' => $this->first_name_kata . ' ' . $this->last_name_kata,
            'avatar' => $this->avatar,
            'sex' => $this->sex,
            'dob' => $this->dob,
            'employee_code' => $this->employee_code,
            'division' => $this->division,
            'position' => $this->position,
            'manager' => $this->manager,
            'mail' => $this->email,
            'phone' => $this->phone,
            'certificate' => $this->certificate,
            'role' => $this->roles->first()->id,
        ];
    }
}
