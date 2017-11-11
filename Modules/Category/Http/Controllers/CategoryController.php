<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Modules\Category\Entities\Category;
use Modules\Category\Exceptions\CategoryException;
use Modules\Category\Services\CategoryService;
use Modules\Category\Transformers\CategoryTransformer;

class CategoryController extends Controller
{
    private $category;
    private $fractal;
    private $categoryService;

    /**
     * CategoryController constructor.
     * @param Category $category
     * @param Manager $manager
     * @param CategoryService $categoryService
     */
    public function __construct(Category $category, Manager $manager, CategoryService $categoryService)
    {
        $this->category         = $category;
        $this->fractal          = $manager;
        $this->categoryService  = $categoryService;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return string
     */
    public function index(Request $request)
    {
        $filter = [
            'name'          =>  $request->input('filter.name'),
            'slug'          =>  $request->input('filter.slug'),
            'description'   =>  $request->input('filter.description'),
        ];

        $categories = $this->categoryService->getAllPaginated($filter, 20);

        $resource = new Collection($categories, new CategoryTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($categories));

        if(!empty($request->input('include')))
            $this->fractal->parseIncludes($request->input('include'));

        $resource = $this->fractal->createData($resource);

        return $resource->toJson();
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return string
     * @throws CategoryException
     */
    public function store(Request $request)
    {
        $data = $request->input('data.attributes');
        $this->categoryService->validateRequest($data);

        $category   = new Category();
        $result     = $this->categoryService->saveCategory($category, $data);

        if ($result instanceof Category) {
            $resource = new Item($result, new CategoryTransformer());
            $resource = $this->fractal->createData($resource);

            return $resource->toJson();
        }

        throw new CategoryException('store_error');
    }

    /**
     * Show the specified resource.
     * @param $id
     * @return string
     * @throws CategoryException
     */
    public function show($id)
    {
        if(!$id)
            throw new CategoryException('invalid_id');

        $category = $this->categoryService->getById($id);

        if($category instanceof Category){
            $resource = new Item($category, new CategoryTransformer());
            $resource = $this->fractal->createData($resource);

            return $resource->toJson();
        }

        throw new CategoryException('not_found');
    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     * @throws CategoryException
     */
    public function update(Request $request, $id)
    {
        if(!$id)
            throw new CategoryException('invalid_id');

        $data = $request->input('data.attributes');
        $this->categoryService->validateRequest($data);

        $category   = $this->categoryService->getById($id);

        if($category instanceof Category){

            $result     = $this->categoryService->saveCategory($category, $data);
            $resource   = new Item($result, new CategoryTransformer());
            $resource   = $this->fractal->createData($resource);

            return $resource->toJson();
        }

        throw new CategoryException('not_found');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return bool
     * @throws CategoryException
     */
    public function destroy($id)
    {
        if(!$id)
            throw new CategoryException('invalid_id');

        $category = $this->categoryService->getById($id);

        if($category instanceof Category){
            $result = $this->categoryService->deleteByCategory($category);

            if ($result)
                return response()->json([
                    'message'   =>  trans('category.delete_success')
                ]);
            else
                throw new CategoryException('delete_error');
        }
        throw new CategoryException('not_found');
    }
}
