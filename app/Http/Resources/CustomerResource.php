<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'name' => $this->first_name . ' ' . $this->last_name,
            'name_kana' => $this->first_name_kana . ' ' . $this->last_name_kana,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'residence_type' => $this->residence_type,
            'phone' => $this->phone,
            'residence_years' => $this->residence_years,
            'email' => $this->email,
            'contact_method' => $this->contact_method,
            'visit_date' => $this->visit_date,
            'budget' => $this->budget,
            'deposit' => $this->deposit,
            'purchase_purpose' => $this->purchase_purpose,
            'purchase_time' => $this->purchase_time,
            'advertising_forms' => $this->advertising_forms,
        ];
    }
}
