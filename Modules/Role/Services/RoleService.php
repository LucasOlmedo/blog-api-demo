<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 16/09/2017
 * Time: 11:05
 */

namespace Modules\Role\Services;

use Modules\Role\Entities\Role;
use Modules\Role\Repositories\RoleRepository;

class RoleService
{
    private $role;
    private $roleRepository;

    /**
     * RoleService constructor.
     * @param Role $role
     * @param RoleRepository $repository
     */
    public function __construct(Role $role, RoleRepository $repository)
    {
        $this->role = $role;
        $this->roleRepository = $repository;
    }

    public function getAll(array $filter = [])
    {
        return $this->roleRepository->getAllRoles($filter);
    }

    public function getById($id)
    {
        return $this->roleRepository->getRoleById($id);
    }
}