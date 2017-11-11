<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 16/09/2017
 * Time: 11:15
 */

namespace Modules\Role\Repositories;

use Modules\Role\Entities\Role;

class RoleRepository
{
    private $role;

    /**
     * RoleRepository constructor.
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    private function queryByFilter($filter)
    {
        $query = $this->role;

        if(!empty($filter['name']))
            $query = $query->where('name', 'LIKE', '%'.$filter['name'].'%');

        return $query;
    }

    public function getAllRoles($filter)
    {
        $result = $this->queryByFilter($filter);
        $result = $result->get();
        return $result;
    }

    public function getRoleById($id)
    {
        $role = $this->role->find($id);
        return $role;
    }
}