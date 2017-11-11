<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 12/08/2017
 * Time: 16:46
 */

namespace Modules\Post\Transformers;


use League\Fractal\TransformerAbstract;
use Modules\Comment\Transformers\CommentTransformer;
use Modules\Post\Entities\Post;
use Modules\Role\Entities\Role;
use Modules\User\Entities\User;

class UserTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'role',
        'posts',
    ];

    protected $defaultIncludes = [
        'role'
    ];

    public function transform(User $user)
    {
        return [
            'id'            => $user->id,
            'name'          => $user->name,
            'email'         => $user->email,
            'avatar'        => $user->avatar,
        ];
    }

    public function includeRole(User $user)
    {
        $role = $user->role;
        if($role instanceof Role)
            return $this->item($role, new RoleTransformer());
    }

    public function includePosts(User $user)
    {
        $posts = $user->posts;
        return $this->collection($posts, new PostTransformer());
    }
}