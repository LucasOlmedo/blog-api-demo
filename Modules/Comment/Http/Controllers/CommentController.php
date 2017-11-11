<?php

namespace Modules\Comment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Modules\Comment\Entities\Comment;
use Modules\Comment\Exceptions\CommentException;
use Modules\Comment\Repositories\CommentRepository;
use Modules\Comment\Services\CommentService;
use Modules\Comment\Transformers\CommentTransformer;
use Modules\Post\Entities\Post;
use Modules\Post\Exceptions\PostException;
use Modules\Post\Services\PostService;
use Validator;

class CommentController extends Controller
{
    private $fractal;
    private $commentService;
    private $postService;
    private $commentRepository;

    /**
     * CommentController constructor.
     * @param Manager $manager
     * @param CommentRepository $repository
     * @param CommentService $service
     * @param PostService $postService
     */
    public function __construct(
        Manager $manager,
        CommentRepository $repository,
        CommentService $service,
        PostService $postService
    )
    {
        $this->fractal = $manager;
        $this->commentService = $service;
        $this->commentRepository = $repository;
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param $id
     * @return Response|string
     * @throws CommentException
     */
    public function index(Request $request, $id)
    {
        if(!$id)
            throw new CommentException('invalid_id');

        $filter = [
            'author' => $request->input('filter.author')
        ];

        $post = $this->getPostById($id);
        $comments = $this->commentService->getAllPaginated($post, $filter, 20);

        $resource = new Collection($comments, new CommentTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($comments));
        $resource = $this->fractal->createData($resource);

        return $resource->toJson();
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @param $id
     * @return Response|string
     * @throws CommentException
     * @throws ValidationException
     */
    public function store(Request $request, $id)
    {
        if(!$id)
            throw new CommentException('invalid_id');

        $validator  = Validator::make($request->all(), [
            'data.attributes.user_id' => 'integer|required',
            'data.attributes.body' => 'string|required'
        ]);

        if(!$validator->fails()){

            $post = $this->getPostById($id);
            $comment = new Comment();
            $data = $request->input('data.attributes');

            $result = $this->commentService->saveComment($post, $comment, $data);
            $resource = new Item($result, new CommentTransformer());
            $resource = $this->fractal->createData($resource);

            return $resource->toJson();
        }
        throw new ValidationException($validator);
    }

    /**
     * Show the specified resource.
     * @param $post_id
     * @param $comment_id
     * @return Response|string
     * @throws CommentException
     */
    public function show($post_id, $comment_id)
    {
        if(!$post_id || !$comment_id)
            throw new CommentException('invalid_id');

        $post = $this->getPostById($post_id);
        $comment = $this->commentService->getCommentById($post, $comment_id);

        if($comment instanceof Comment){
            $resource = new Item($comment, new CommentTransformer());
            $resource = $this->fractal->createData($resource);

            return $resource->toJson();
        }

        throw new CommentException('not_found');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @param $post_id
     * @param $comment_id
     * @return string
     * @throws CommentException
     * @throws ValidationException
     */
    public function update(Request $request, $post_id, $comment_id)
    {
        if(!$post_id || !$comment_id)
            throw new CommentException('invalid_id');

        $validator  = Validator::make($request->all(), [
            'data.attributes.user_id' => 'integer',
            'data.attributes.body' => 'string'
        ]);

        if(!$validator->fails()){
            $post = $this->getPostById($post_id);
            $comment = $this->commentService->getCommentById($post, $comment_id);
            $data = $request->input('data.attributes');

            $result = $this->commentService->saveComment($post, $comment, $data);
            $resource = new Item($result, new CommentTransformer());
            $resource = $this->fractal->createData($resource);

            return $resource->toJson();
        }
        throw new ValidationException($validator);
    }

    /**
     * Remove the specified resource from storage.
     * @param $post_id
     * @param $comment_id
     * @return Response
     * @throws CommentException
     * @internal param $id
     */
    public function destroy($post_id, $comment_id)
    {
        if(!$post_id || !$comment_id)
            throw new CommentException('invalid_id');

        $post = $this->getPostById($post_id);
        $comment = $this->commentService->getCommentById($post, $comment_id);

        if($comment instanceof Comment){
            $result = $this->commentService->deleteByComment($comment);

            if ($result)
                return response()->json([
                    'message'   =>  trans('comment.delete_success')
                ]);
            else
                throw new CommentException('delete_error');
        }
        throw new CommentException('not_found');
    }

    /**
     * @param $id
     * @return mixed|static
     * @throws PostException
     */
    public function getPostById($id)
    {
        $post = $this->postService->getById($id);
        if($post instanceof Post)
            return $post;

        throw new PostException('not_found');
    }
}
