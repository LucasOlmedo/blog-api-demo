<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 13/08/2017
 * Time: 11:17
 */

namespace Modules\Category\Repositories;

use Modules\Category\Entities\Category;

class CategoryRepository
{
    private $category;

    /**
     * CategoryRepository constructor.
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @param $filters
     * @return Category|CategoryRepository
     */
    private function makeQuery($filters)
    {
        $query = $this->category;

        if(!empty($filters['name']))
            $query = $query->where('name', 'LIKE', '%' . $filters['name'] . '%');

        if(!empty($filters['slug']))
            $query = $query->where('slug', 'LIKE', '%' . $filters['slug'] . '%');

        if(!empty($filters['description']))
            $query = $query->where('description', 'LIKE', '%' . $filters['description'] . '%');

        return $query;
    }

    /**
     * @param $filter
     * @param $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Category|CategoryRepository
     */
    public function allCategoriesPaginated($filter, $perPage)
    {
        $result = $this->makeQuery($filter);
        $result = $result->paginate($perPage);
        return $result;
    }

    /**
     * @param $id
     * @return mixed|static
     */
    public function getCategoryById($id)
    {
        $result = $this->category->find($id);
        return $result;
    }

    /**
     * @param Category $category
     * @return bool|Category
     */
    public function save(Category $category)
    {
        if($category->save())
            return $category;

        return false;
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function deleteCategory(Category $category)
    {
        if($category->posts()->count() > 0)
            return false;

        $category->delete();
        return true;
    }
}