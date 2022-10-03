<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRelationships;
    protected $primaryKey = 'user_id';
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id',
    ];

    public function getApproved()
    {
        return $this->hasOne('App\Models\Profile', 'user_id')->where('isApproved', 1);
    }

    public function getNotApproved()
    {
        return $this->hasOne('App\Models\Profile', 'user_id')->where('isApproved', 0);
    }

    public function track()
    {
        return $this->hasOne('App\Models\UserTracker', 'user_id');
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function accessTokens()
    {
        return $this->hasMany('App\Models\OAuthToken', 'user_id');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function profile()
    {
        return $this->hasOne('App\Models\Profile', 'user_id');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }

    public function extendedStudent()
    {
        return $this->hasOneThrough('App\Models\Student', 'App\Models\Profile', 'user_id', 'profile_id');
    }

    public function extendedTeacher()
    {
        return $this->hasOneThrough('App\Models\Teacher', 'App\Models\Profile', 'user_id', 'profile_id');
    }

    public function getAssignStudent()
    {
        return $this->hasOneDeep('App\Models\Assign', ['App\Models\Profile', 'App\Models\Student'], ['user_id', 'profile_id', 'student_id']);
    }

    public function getAssignTeacher()
    {
        return $this->hasOneDeep('App\Models\Assign', ['App\Models\Profile', 'App\Models\Teacher'], ['user_id', 'profile_id', 'teacher_id']);
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
