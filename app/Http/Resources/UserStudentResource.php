<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserStudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (!is_null($this->profile)){
            $profile = array(
            'profile_id' => ((string)$this->profile->profile_id)??'N/A',
            'address' => $this->profile->address??'Details not filled by the user',
            'profile_image' => $this->profile->profile_image??'Details not filled by the user',
            'current_school' => $this->profile->current_school??'Details not filled by the user',
            'previous_school' => $this->profile->previous_school??'Details not filled by the user',
            'isApproved' => $this->profile->isApproved,
            );
        } else {
            $profile = array(
                "Error" => "User has not provided profile information.",
            );
        }
        
        if(!is_null($this->extendedStudent)){
            $extended = array(
                'student_id' => $this->extendedStudent->student_id,
                'parent_name' => $this->extendedStudent->parent_name,
                'parent_contact' => $this->extendedStudent->parent_contact,
            );
        } else {
            $extended = array(
                "Error" => "Student has not provided additional information.",
            );
        }
        return [
            'user' => [
                'user_id' => (string)$this->user_id,
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role->role,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'profile' => [
                $profile,
            ],
            'parent_details' => [
                $extended,
            ],
        ];
    }
}
