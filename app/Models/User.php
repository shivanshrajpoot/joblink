<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the job applications associated with the user.
     */

    public function appliedJobs()
    {
        return $this->belongsToMany(Job::class,'applications')->withTimestamps();
    }

    public function createdJobs()
    {
        return $this->hasMany(Job::class);
    }
}
