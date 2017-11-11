<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 12/08/2017
 * Time: 16:46
 */

namespace Modules\Post\Transformers;


use League\Fractal\TransformerAbstract;
use Modules\Role\Entities\Role;

class RoleTransformer extends TransformerAbstract
{
    public function transform(Role $role)
    {
        return [
            'type' => 'role',
            'attributes' => [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
            ]
        ];
    }

    public function includeUsers(Role $role)
    {
        $users = $role->users;
        return $this->collection($users, new UserTransformer());
    }

}