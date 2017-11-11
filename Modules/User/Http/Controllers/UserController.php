<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Modules\Post\Transformers\UserTransformer;
use Modules\Role\Entities\Role;
use Modules\Role\Services\RoleService;
use Modules\User\Exceptions\UserException;
use Validator;
use Modules\User\Entities\User;
use Modules\User\Services\UserService;

class UserController extends Controller
{
    private $successStatus = 200;
    private $user;
    private $fractal;
    private $userService;
    private $roleService;

    public function __construct(User $user, Manager $manager, UserService $userService, RoleService $service)
    {
        $this->user         =   $user;
        $this->fractal      =   $manager;
        $this->userService  =   $userService;
        $this->roleService  =   $service;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response|string
     */
    public function index(Request $request)
    {
        $filter = [
            'name' => $request->input('filter.name'),
            'email' => $request->input('filter.email'),
            'role' => $request->input('filter.role'),
        ];

        $users = $this->userService->getAllPaginated($filter, 20);

        $resource = new Collection($users, new UserTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($users));

        if(!empty($request->input('include')))
            $this->fractal->parseIncludes($request->input('include'));

        $resource = $this->fractal->createData($resource);
        return $resource->toJson();
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response|string
     */
    public function store(Request $request)
    {
        $this->validateRequestStore($request);
        $data = [
            'name' => $request->input('data.attributes.name'),
            'email' => $request->input('data.attributes.email'),
            'avatar' => $request->input('data.attributes.avatar'),
            'password' => $request->input('data.attributes.password'),
            'role' => $this->validateRole($request),
        ];

        $user = new User();
        $result = $this->userService->saveUser($user, $data);

        if($result instanceof User){
            $resource = new Item($result, new UserTransformer());
            $resource = $this->fractal->createData($resource);
            return $resource->toJson();
        }
        throw new UserException('store_error');
    }

    /**
     * Show the specified resource.
     * @param $id
     * @return Response|string
     * @throws UserException
     */
    public function show($id)
    {
        if(!$id)
            throw new UserException('invalid_id');

        $user = $this->userService->getById($id);

        if($user instanceof User){
            $resource = new Item($user, new UserTransformer());
            $this->fractal->parseIncludes([
                'role',
                'posts'
            ]);

            $resource = $this->fractal->createData($resource);
            return $resource->toJson();
        }
        throw new UserException('not_found');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response|string
     */
    public function update(Request $request, $id)
    {
        if(!$id)
            throw new UserException('invalid_id');

        $this->validateRequestUpdate($request);
        $data = $this->updateDataArray($request);
        $user = $this->userService->getById($id);

        if($user instanceof User){
            $result = $this->userService->saveUser($user, $data);
            $resource = new Item($result, new UserTransformer());
            $resource = $this->fractal->createData($resource);
            return $resource->toJson();
        }
        throw new UserException('not_found');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return Response
     * @throws UserException
     */
    public function destroy($id)
    {
        if(!$id)
            throw new UserException('invalid_id');

        $user = $this->userService->getById($id);

        if($user instanceof User){
            $delete = $this->userService->deleteByUser($user);
            if($delete) {
                return response()->json([
                    'message' => trans('user.delete_success')
                ]);
            }
            throw new UserException('delete_error');
        }
        throw new UserException('not_found');
    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     * @throws UserException
     */
//    public function associateRole(Request $request, $id)
//    {
//        if(!$id)
//            throw new UserException('invalid_id');
//
//        $role = $this->validateRole($request);
//        $user = $this->userService->getById($id);
//
//        if($user instanceof User){
//            $result = $this->userService->associateRole($user, $role);
//            return $result;
//        }
//        throw new UserException('not_found');
//    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = [
            'email'     =>  request('email'),
            'password'  =>  request('password'),
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $success['token'] = $user->createToken('Blog')->accessToken;
            return response()->json([
                'success'   =>  $success,
            ], $this->successStatus);
        }

        return response()->json([
            'error' =>  'Unauthorized',
        ], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws UserException
     */
    public function register(Request $request)
    {
        $this->validateRegister($request);
        $data = $request->input('data.attributes');
        $data['role'] = $this->roleService->getById(2);
        $user = new User();
        $result = $this->userService->saveUser($user, $data);

        if($result instanceof User){
            $success['username'] = $result->name;
            $success['email'] = $result->email;
            $success['role'] = $result->role;

            return response()->json([
                'success' => $success
            ], $this->successStatus);
        }
        throw new UserException('register_error');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json([
            'details'   => $user
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @return mixed|static
     * @throws UserException
     * @throws ValidationException
     */
    public function validateRole(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'data.attributes.roleId' => 'integer|required'
        ]);

        if(!$validator->fails()){
            $data = $request->input('data.attributes.roleId');
            $role = $this->roleService->getById($data);
            if($role instanceof Role){
                return $role;
            }
            throw new UserException('role_not_found');
        }
        throw new ValidationException($validator);
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    private function validateRequestStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'data.attributes.name' => 'string|required',
            'data.attributes.email' => 'email|required',
            'data.attributes.avatar' => 'string',
            'data.attributes.password' => 'string|required',
            'data.attributes.roleId' => 'integer|required'
        ]);

        if($validator->fails())
            throw new ValidationException($validator);
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    private function validateRequestUpdate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'data.attributes.name' => 'string',
            'data.attributes.email' => 'email',
            'data.attributes.avatar' => 'string',
            'data.attributes.password' => 'string',
            'data.attributes.roleId' => 'integer'
        ]);

        if($validator->fails())
            throw new ValidationException($validator);
    }

    private function updateDataArray(Request $request)
    {
        $data = [];

        if($request->input('data.attributes.name'))
            $data['name'] = $request->input('data.attributes.name');

        if($request->input('data.attributes.email'))
            $data['email'] = $request->input('data.attributes.email');

        if($request->input('data.attributes.avatar'))
            $data['avatar'] = $request->input('data.attributes.avatar');

        if($request->input('data.attributes.password'))
            $data['password'] = $request->input('data.attributes.password');

        if($request->input('data.attributes.roleId'))
            $data['role'] = $this->validateRole($request);

        return $data;
    }

    private function validateRegister(Request $request)
    {
        $validator = Validator::make($request->input('data.attributes'),[
            'name' => 'string|required',
            'email' => 'email|required',
            'avatar' => 'string',
            'password' => 'required|string',
            'confirm_password'  => 'required|same:password'
        ]);

        if($validator->fails())
            throw new ValidationException($validator);
    }
}
