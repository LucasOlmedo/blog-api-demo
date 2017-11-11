<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 24/08/2017
 * Time: 21:34
 */

namespace Modules\Tag\Services;


use Illuminate\Validation\ValidationException;
use Modules\Tag\Entities\Tag;
use Modules\Tag\Repositories\TagRepository;
use Validator;

class TagService
{
    private $tagRepository;

    /**
     * TagService constructor.
     * @param TagRepository $repository
     */
    public function __construct(TagRepository $repository)
    {
        $this->tagRepository = $repository;
    }

    /**
     * @param array $filter
     * @param int $perPage
     * @return mixed
     */
    public function getAllPaginated($filter = [], $perPage = 20)
    {
        return $this->tagRepository->allTagsPaginated($filter, $perPage);
    }

    public function saveTag(Tag $tag, array $data)
    {
        if(!empty($data['name']))
            $tag->name = $data['name'];

        if(!empty($data['slug']))
            $tag->slug = $data['slug'];

        return $this->tagRepository->save($tag);
    }

    /**
     * @param $id
     * @return mixed|static
     */
    public function getById($id)
    {
        return $this->tagRepository->tagById($id);
    }

    /**
     * @param Tag $tag
     * @return bool
     */
    public function deleteById(Tag $tag)
    {
        return $this->tagRepository->deleteTag($tag);
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
        ];
    }
}