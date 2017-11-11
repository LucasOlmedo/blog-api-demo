<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 13/08/2017
 * Time: 11:07
 */

namespace Modules\User\Services;

use Modules\Role\Entities\Role;
use Validator;
use Illuminate\Validation\ValidationException;
use Modules\User\Entities\User;
use Modules\User\Repositories\UserRepository;

class UserService
{
    private $userRepository;

    /**
     * CategoryService constructor.
     * @param UserRepository $repository
     * @internal param UserRepository $userRepository
     */
    public function __construct(UserRepository $repository)
    {
        $this->userRepository = $repository;
    }

    /**
     * @param array $filter
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|User|UserRepository
     */
    public function getAllPaginated($filter = [], $perPage = 20)
    {
        return $this->userRepository->allUsersPaginated($filter, $perPage);
    }

    /**
     * @param User $user
     * @param array $data
     * @return bool|User
     * @internal param User $category
     */
    public function saveUser(User $user, array $data)
    {
        if (!empty($data['name']))
            $user->name = $data['name'];

        if (!empty($data['email']))
            $user->email = $data['email'];

        if (!empty($data['avatar']))
            $user->avatar = $data['avatar'];

        if (!empty($data['password']))
            $user->password = bcrypt($data['password']);

        if (!empty($data['role']))
            $this->associateRole($user, $data['role']);

        return $this->userRepository->save($user);
    }

    /**
     * @param $id
     * @return mixed|static
     */
    public function getById($id)
    {
        return $this->userRepository->getUserById($id);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function deleteByUser(User $user)
    {
        return $this->userRepository->deleteUser($user);
    }

    /**
     * @param $request
     * @throws ValidationException
     */
    public function validateRequest($request)
    {
        $rules      = $this->rules();
        $validator  = Validator::make($request, $rules);

        if($validator->fails())
            throw new ValidationException($validator);
    }

    /**
     * @return array
     */
    private function rules(){
        return [
            'name'              => 'required',
            'email'             => 'required|email',
            'password'          => 'required',
            'confirm_password'  => 'required|same:password',
        ];
    }

    public function associateRole(User $user, Role $role)
    {
        return $this->userRepository->makeAssociateUserRole($user, $role);
    }
}