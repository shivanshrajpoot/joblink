<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model 
{

	protected $fillable = [
		'title',
		'description',
		'user_id'
	];

	public function recruiter()
	{
		return $this->belongsTo(User::class,'user_id');
	}

	public function applicants(){
		return $this->belongsToMany(User::class,'applications');
	}
}