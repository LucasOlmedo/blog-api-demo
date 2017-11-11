<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 13/08/2017
 * Time: 11:07
 */

namespace Modules\Category\Services;

use Validator;
use Illuminate\Validation\ValidationException;
use Modules\Category\Entities\Category;
use Modules\Category\Repositories\CategoryRepository;

class CategoryService
{
    private $categoryRepository;

    /**
     * CategoryService constructor.
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param array $filter
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Category|CategoryRepository
     */
    public function getAllPaginated($filter = [], $perPage = 20)
    {
        return $this->categoryRepository->allCategoriesPaginated($filter, $perPage);
    }

    /**
     * @param Category $category
     * @param array $data
     * @return bool|Category
     */
    public function saveCategory(Category $category, array $data)
    {
        if (!empty($data['name']))
            $category->name         = $data['name'];

        if (!empty($data['slug']))
            $category->slug         = $data['slug'];

        if (!empty($data['description']))
            $category->description  = $data['description'];

        return $this->categoryRepository->save($category);
    }

    /**
     * @param $id
     * @return mixed|static
     */
    public function getById($id)
    {
        return $this->categoryRepository->getCategoryById($id);
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function deleteByCategory(Category $category)
    {
        return $this->categoryRepository->deleteCategory($category);
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
            'name'          =>  'required|string',
            'slug'          =>  'required|string',
            'description'   =>  'string',
        ];
    }
}