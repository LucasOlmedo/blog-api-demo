<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 13/08/2017
 * Time: 11:17
 */

namespace Modules\User\Repositories;

use Modules\Role\Entities\Role;
use Modules\User\Entities\User;

class UserRepository
{
    private $user;

    /**
     * UserRepository constructor.
     * @param User $user
     * @internal param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param $filters
     * @return User|UserRepository
     */
    public function makeQuery($filters)
    {
        $query = $this->user;

        if(!empty($filters['name']))
            $query = $query->where('name', 'LIKE', '%' . $filters['name'] . '%');

        if(!empty($filters['email']))
            $query = $query->where('name', 'LIKE', '%' . $filters['email'] . '%');

        return $query;
    }

    /**
     * @param $filter
     * @param $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|User|UserRepository
     */
    public function allUsersPaginated($filter, $perPage)
    {
        $result = $this->makeQuery($filter);
        $result = $result->paginate($perPage);
        return $result;
    }

    /**
     * @param $id
     * @return mixed|static
     */
    public function getUserById($id)
    {
        $result = $this->user->find($id);
        return $result;
    }

    /**
     * @param User $user
     * @return bool|User
     * @internal param User $user
     */
    public function save(User $user)
    {
        if($user->save())
            return $user;

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function deleteUser(User $user)
    {
        if($user->posts()->count() > 0)
            return false;

        $user->delete();
        return true;
    }

    /**
     * @param User $user
     * @param Role $role
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function makeAssociateUserRole(User $user, Role $role)
    {
        return $user->role()->associate($role);
    }
}