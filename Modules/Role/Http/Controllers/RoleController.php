<?php

namespace Modules\Role\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Modules\Post\Transformers\RoleTransformer;
use Modules\Role\Entities\Role;
use Modules\Role\Exceptions\RoleException;
use Modules\Role\Services\RoleService;

class RoleController extends Controller
{
    private $fractal;
    private $roleService;
    private $role;

    /**
     * RoleController constructor.
     * @param Role $role
     * @param Manager $manager
     * @param RoleService $service
     */
    public function __construct(Role $role, Manager $manager, RoleService $service)
    {
        $this->fractal = $manager;
        $this->roleService = $service;
        $this->role = $role;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response|string
     */
    public function index(Request $request)
    {
        $filter = [
            'name' => $request->input('filter.name')
        ];

        $roles = $this->roleService->getAll($filter);
        $resource = new Collection($roles, new RoleTransformer());

        if(!empty($request->input('include')))
            $this->fractal->parseIncludes($request->input('include'));

        $resource = $this->fractal->createData($resource);
        return $resource->toJson();
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @param $id
     * @return string
     * @throws RoleException
     */
    public function show($id)
    {
        if(!$id)
            throw new RoleException('invalid_id');

        $role = $this->roleService->getById($id);

        if($role instanceof Role){
            $resource = new Item($role, new RoleTransformer());
            $resource = $this->fractal->createData($resource);
            return $resource->toJson();
        }
        throw new RoleException('not_found');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
