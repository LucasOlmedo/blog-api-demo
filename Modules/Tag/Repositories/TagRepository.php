<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 24/08/2017
 * Time: 21:48
 */

namespace Modules\Tag\Repositories;


use Modules\Tag\Entities\Tag;

class TagRepository
{
    private $tag;

    /**
     * TagRepository constructor.
     * @param Tag $tag
     */
    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * @param $filter
     * @return $this|Tag
     */
    private function makeQuery($filter)
    {
        $query = $this->tag;

        if(!empty($filter['name']))
            $query = $query->where('name', 'LIKE', '%' . $filter['name'] . '%');

        if(!empty($filter['slug']))
            $query = $query->where('name', 'LIKE', '%' . $filter['slug'] . '%');

        return $query;
    }

    /**
     * @param $filter
     * @param $perPage
     * @return mixed
     */
    public function allTagsPaginated($filter, $perPage)
    {
        $result = $this->makeQuery($filter);
        $result = $result->paginate($perPage);
        return $result;
    }

    public function save(Tag $tag)
    {
        if($tag->save())
            return $tag;

        return false;
    }

    /**
     * @param $id
     * @return mixed|static
     */
    public function tagById($id)
    {
        $result = $this->tag->find($id);
        return $result;
    }

    /**
     * @param Tag $tag
     * @return bool
     */
    public function deleteTag(Tag $tag)
    {
        if($tag->delete())
            return true;

        return false;
    }
}