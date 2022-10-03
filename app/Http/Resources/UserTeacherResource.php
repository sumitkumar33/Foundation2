<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserTeacherResource extends JsonResource
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
        
        if(!is_null($this->extendedTeacher)){
            $extended = array(
                'teacher_id' => ((string)$this->extendedTeacher->teacher_id),
                'expertise_subject' => $this->extendedTeacher->expertise_subject,
                'experience' => $this->extendedTeacher->experience,
            );
        } else {
            $extended = array(
                "Error" => "Teacher has not provided professional details.",
            );
        }
        return [
            'user' => [
                'user_id' => ((string)$this->user_id),
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role->role,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'profile' => [
                $profile,
            ],
            'professional_details' => [
                $extended,
            ],
        ];
    }
}
