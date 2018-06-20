<?php

namespace App\Transformers;

use App\Models\Job;
use League\Fractal\TransformerAbstract;

class JobTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
     
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];


    /**
     * resolove the profile
     *
     * @param Build $model
     * @return array
     */
    public function transform(Job $job)
    {
      return [
        'title' => $job->title,
        'description' => $job->description,
        'applicants' => $job->applicants,
        'recruiter' => $job->recruiter
      ];
    }

}