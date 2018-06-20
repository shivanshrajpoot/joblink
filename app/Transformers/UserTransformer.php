<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
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
    public function transform(User $model)
    {
      return [
        'email' => $model->email,
        'name' => $model->name,
        'type' => $model->type == 1 ? 'Provider' : ($model->type == 2 ? 'Seeker' : 'Admin'),
      ];
    }

    public function includeForumComments(User $user)
    {
      return $this->collection($user->forum_comments()->sort()->get(), new ForumCommentTransformer);
    }

    public function includeForumPosts(User $user)
    {
      return $this->collection($user->forum_posts()->sort()->get(), new ForumPostTransformer);
    }

}