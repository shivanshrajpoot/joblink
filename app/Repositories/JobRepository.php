<?php

namespace App\Repositories;

use App\Models\Job;
use App\Traits\DbRepositoryTrait;

class JobRepository
{
	use DbRepositoryTrait;

	protected $model = Job::class;

	/**
	 * Returns All paginated job records
	 * @param  Integer $perPage Items Per Page
	 * @param  Integer $user_id User Id
	 * @return App\Models\Job   Collection of Job
	 */
	public function getAllPaginatedJobs($perPage,$user_id)
	{
		return $user_id ? Job::whereNotIn('user_id',[$user_id]) : Job::all();
	}

}