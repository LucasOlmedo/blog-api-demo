<?php

namespace Modules\Post\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Modules\Post\Entities\Post;
use Modules\Post\Exceptions\PostException;
use Modules\Post\Repositories\PostRepository;
use Modules\Post\Services\PostService;
use Modules\Post\Transformers\PostTransformer;
use Validator;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    private $fractal;
    private $postService;
    private $postRepository;

    /**
     * PostController constructor.
     * @param PostService $postService
     * @param PostRepository $postRepository
     * @param Manager $manager
     * @internal param PostRepository $postRepositor
     */
    public function __construct(PostService $postService, PostRepository $postRepository, Manager $manager)
    {
        $this->fractal = $manager;
        $this->postService = $postService;
        $this->postRepository = $postRepository;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response|string
     */
    public function index(Request $request)
    {
        $filter = [
            'author' => $request->input('filter.author'),
            'category' => $request->input('filter.category'),
            'tag' => $request->input('filter.tag'),
            'title' => $request->input('filter.title'),
            'slug' => $request->input('filter.slug'),
            'status' => $request->input('filter.status')
        ];

        $posts = $this->postService->getAllPaginated($filter, 20);

        $resource = new Collection($posts, new PostTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($posts));
        $resource = $this->fractal->createData($resource);

        return $resource->toJson();
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response|string
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data.attributes.user_id' => 'required|integer',
            'data.attributes.category_id' => 'required|integer',
            'data.attributes.title' => 'string|required',
            'data.attributes.slug' => 'required',
            'data.attributes.body' => 'string',
            'data.attributes.status' => 'required|in:published,pending,trashed',
            'data.attributes.tags' => 'array'
        ]);

        if(!$validator->fails()){

            $data = $request->input('data.attributes');
            $post = new Post();
            $result = $this->postService->savePost($post, $data);

            if($result instanceof Post){
                $resource = new Item($post, new PostTransformer());
                $resource = $this->fractal->createData($resource);

                return $resource->toJson();
            }
        }
        throw new ValidationException($validator);
    }

    /**
     * Show the specified resource.
     * @param $id
     * @return Response|string
     * @throws PostException
     */
    public function show($id)
    {
        if(!$id)
            throw new PostException('invalid_id');

        $post = $this->postService->getById($id);

        if($post instanceof Post){
            $resource = new Item($post, new PostTransformer());
            $resource = $this->fractal->createData($resource);
            return $resource->toJson();
        }
        throw new PostException('not_found');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @param $id
     * @return Response|string
     * @throws PostException
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        if(!$id)
            throw new PostException('invalid_id');

        $validator = Validator::make($request->all(), [
            'data.attributes.user_id' => 'integer',
            'data.attributes.category_id' => 'integer',
            'data.attributes.title' => 'string',
            'data.attributes.slug' => 'string',
            'data.attributes.body' => 'string',
            'data.attributes.status' => 'in:published,pending,trashed',
            'data.attributes.tags' => 'array'
        ]);

        $data = $request->input('data.attributes');

        if(!$validator->fails()){

            $post = $this->postService->getById($id);

            if($post instanceof Post){
                $result = $this->postService->savePost($post, $data);
                $resource = new Item($result, new PostTransformer());
                $resource = $this->fractal->createData($resource);
                return $resource->toJson();
            }
        }
        throw new ValidationException($validator);
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return Response
     * @throws PostException
     */
    public function destroy($id)
    {
        if(!$id)
            throw new PostException('invalid_id');

        $post = $this->postService->getById($id);

        if($post instanceof Post){
            $result = $this->postService->deleteByPost($post);

            if ($result)
                return response()->json([
                    'message'   =>  trans('post.delete_success')
                ]);
            else
                throw new PostException('delete_error');
        }
        throw new PostException('not_found');
    }
}
