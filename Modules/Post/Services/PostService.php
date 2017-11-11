<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 08/09/2017
 * Time: 16:59
 */

namespace Modules\Post\Services;


use Illuminate\Support\Facades\DB;
use Modules\Category\Entities\Category;
use Modules\Post\Entities\Post;
use Modules\Post\Exceptions\PostException;
use Modules\Post\Repositories\PostRepository;
use Modules\Tag\Entities\Tag;
use Modules\User\Entities\User;

class PostService
{
    private $post;
    private $user;
    private $category;
    private $tag;
    private $postRepository;

    /**
     * PostService constructor.
     * @param Post $post
     * @param PostRepository $postRepository
     */
    public function __construct(Post $post, PostRepository $postRepository, User $user, Category $category, Tag $tag)
    {
        $this->post = $post;
        $this->user = $user;
        $this->category = $category;
        $this->tag = $tag;
        $this->postRepository = $postRepository;
    }

    /**
     * @param $filter
     * @param int $perPage
     * @return mixed
     */
    public function getAllPaginated($filter, $perPage = 20)
    {
        return $this->postRepository->allPostsPaginated($filter, $perPage);
    }

    /**
     * @param Post $post
     * @param array $data
     * @return Post
     */
    public function savePost(Post $post, array $data)
    {
        if(!empty($data['title']))
            $post->title = $data['title'];

        if(!empty($data['slug']))
            $post->slug = $data['slug'];

        if(!empty($data['body']))
            $post->body = $data['body'];

        if(!empty($data['status']))
            $post->status = $data['status'];

        return $this->postRepository->savePost($post, $data);
    }

    /**
     * @param $id
     * @return mixed|static
     */
    public function getById($id)
    {
        return $this->postRepository->postById($id);
    }

    /**
     * @param $post
     * @return mixed
     */
    public function deleteByPost($post)
    {
        return $this->postRepository->softDeletePost($post);
    }
}