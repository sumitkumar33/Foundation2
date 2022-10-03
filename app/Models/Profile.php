<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $primaryKey = 'profile_id';
    protected $fillable = [
        'address', 'profile_image', 'current_school', 'previous_school', 'isApproved', 'user_id',
    ];
}
