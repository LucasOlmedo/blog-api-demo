<?php

namespace Modules\Tag\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Modules\Post\Transformers\TagTransformer;
use Modules\Tag\Entities\Tag;
use Modules\Tag\Exceptions\TagException;
use Modules\Tag\Services\TagService;

class TagController extends Controller
{
    private $tag;
    private $fractal;
    private $tagService;

    public function __construct(Tag $tag, Manager $manager, TagService $service)
    {
        $this->tag          = $tag;
        $this->fractal      = $manager;
        $this->tagService   = $service;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response|string
     */
    public function index(Request $request)
    {
        $filter = [
            'name'  =>  $request->input('filter.name'),
            'slug'  =>  $request->input('filter.slug'),
        ];

        $tags = $this->tagService->getAllPaginated($filter, 20);

        $resource = new Collection($tags, new TagTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($tags));

        if(!empty($request->input('include')))
            $this->fractal->parseIncludes($request->input('include'));

        $resource = $this->fractal->createData($resource);

        return $resource->toJson();
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response|string
     * @throws TagException
     */
    public function store(Request $request)
    {
        $data = $request->input('data.attributes');
        $this->tagService->validateRequest($data);
        $tag = new Tag();
        $result = $this->tagService->saveTag($tag, $data);

        if($result instanceof Tag){
            $resource = new Item($result, new TagTransformer());
            $resource = $this->fractal->createData($resource);

            return $resource->toJson();
        }
        throw new TagException('store_error');
    }

    /**
     * Show the specified resource.
     * @param $id
     * @return Response|string
     * @throws TagException
     */
    public function show($id)
    {
        if (!$id)
            throw new TagException('invalid_id');

        $tag = $this->tagService->getById($id);

        if ($tag instanceof Tag) {
            $resource = new Item($tag, new TagTransformer());
            $resource = $this->fractal->createData($resource);

            return $resource->toJson();
        }
        throw new TagException('not_found');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @param $id
     * @return Response|string
     * @throws TagException
     */
    public function update(Request $request, $id)
    {
        $data = $request->input('data.attributes');
        $this->tagService->validateRequest($data);
        $tag = $this->tagService->getById($id);

        if($tag instanceof Tag){
            $result = $this->tagService->saveTag($tag, $data);
            $resource = new Item($result, new TagTransformer());
            $resource = $this->fractal->createData($resource);

            return $resource->toJson();
        }

        throw new TagException('not_found');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return Response
     * @throws TagException
     */
    public function destroy($id)
    {
        if(!$id)
            throw new TagException('invalid_id');

        $tag = $this->tagService->getById($id);

        if($tag instanceof Tag){

            $delete = $this->tagService->deleteById($tag);

            if($delete) {
                return response()->json([
                    'message' => trans('tag.delete_success')
                ]);
            }
            throw new TagException('delete_error');
        }
        throw new TagException('not_found');
    }
}
