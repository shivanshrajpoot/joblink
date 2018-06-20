<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Application;
use App\Validations\JobValidator;
use App\Exceptions\AccessException;
use App\Repositories\JobRepository;

class JobService
{
    function __construct(
        JobRepository $jobRepo,
        JobValidator $jobValidator
    )
    {
        $this->validator = $jobValidator;
        $this->jobRepo = $jobRepo;
    }

    /**
     * Returns all paginated jobs
     * 
     * @return App\Models\Job Job Collection
     */
    public function getAllJobs($perPage,$user=NULL)
    {
        $user_id = NULL;
        if($user){
            if ($user->type === 1)  throw new AccessException('Not Authorized.');
            $user_id = $user->id;
        }
        $jobs = $this->jobRepo->getAllPaginatedJobs($perPage,$user_id);

        return $jobs; 
    }


    /**
     * Returns jobs
     * 
     * @param  Array  $inputs User Input
     * @param  App\Models\User $user   User
     * @return App\Models\Job         Jobs
     */
    public function createJob($inputs,$user)
    {
        // Check if the user is recruiter
        if ($user->type !== 1) throw new AccessException('Not Authorized.');

        $this->validator->fire($inputs, 'create', []);
            
        $job = new Job([
                    'title'       => $inputs['title'],
                    'description' => $inputs['description'],
                ]);
        $job->recruiter()->associate($user);

        $job->save();

        return $user->createdJobs();   
    }

    /**
     * Returns jobs
     * 
     * @param  Array  $inputs User Input
     * @param  App\Models\User $user   User
     * @return App\Models\Job         Jobs
     */
    public function updateJob($inputs,$user)
    {
        // Check if the user is recruiter
        if ($user->type !== 1) throw new AccessException('Not Authorized.');

        $this->validator->fire($inputs, 'update', []);

        $job_id = array_get($inputs, 'id');

        $createdJobs = $user->createdJobs()->pluck('id')->toArray();

        if(!in_array($job_id, $createdJobs)) throw new AccessException('Job does not exists.'); 

        $job = Job::find($job_id);
        $job->title = array_get($inputs, 'title');
        $job->description = array_get($inputs, 'description');
        $job->save();

        return $user->createdJobs();   
    }

    /**
     * Returns the created job
     * 
     * @param  Array  $inputs  User Input
     * @param  App\Models\User $user   User
     * @return App\Models\User Instance of User-Job Relation
     */
    public function deleteJob($inputs,$user)
    {
        // Check if the user is recruiter
        if ($user->type !== 1) throw new AccessException('Not Authorized.');

        $this->validator->fire($inputs, 'delete', []);

        $job_id = array_get($inputs, 'id');

        $createdJobs = $user->createdJobs()->pluck('id')->toArray();

        if(!in_array($job_id, $createdJobs)) throw new AccessException('Job does not exists.'); 

        $job = Job::find($job_id);
        $job->recruiter()->dissociate($user);
        $job->save();

        return $user->createdJobs();   
    }

    /**
     * Applies for an existing job.
     * 
     * @param  Array  $inputs User Input
     * @param  App\Models\User $user   User
     * @return App\Models\Job         Jobs
     */
    public function applyForJob($inputs, $user)
    {
        // Check if the user is job seeker
        if ($user->type !== 2) throw new AccessException('Not Authorized.');

        $this->validator->fire($inputs, 'apply', []);
        
        $job_id = array_get($inputs, 'job_id');

        $hasAlreadyApplied = in_array($job_id, $user->appliedJobs->pluck('id')->toArray());

        //Check if already applied for the job
        if ($hasAlreadyApplied) throw new AccessException('Already applied.'); 

        $user->appliedJobs()->attach(['job_id'=>$job_id]);

        return $user->appliedJobs();
    }

    /**
     * Reverts Application for an existing job.
     * 
     * @param  Array  $inputs User Input
     * @param  App\Models\User $user   User
     * @return App\Models\Job         Jobs
     */
    public function revertApplication($inputs, $user)
    {
        // Check if the user is job seeker
        if ($user->type !== 2) throw new AccessException('Not Authorized.');

        $this->validator->fire($inputs, 'apply', []);
        
        $job_id = array_get($inputs, 'job_id');

        $hasAlreadyApplied = in_array($job_id, $user->appliedJobs->pluck('id')->toArray());

        //Check if already applied for the job
        if (!$hasAlreadyApplied) throw new AccessException('Not Authorized.'); 

        $user->appliedJobs()->detach(['job_id'=>$job_id]);

        return $user->appliedJobs();
    }

    public function allCreatedJobs($user)
    {
        // Check if the user is recruiter
        if ($user->type !== 1) throw new AccessException('Not Authorized.');
        return $user->createdJobs();
    }

    public function allAppliedJobs($user)
    {
        // Check if the user is recruiter
        if ($user->type !== 2) throw new AccessException('Not Authorized.');
        return $user->appliedJobs();
    }
}