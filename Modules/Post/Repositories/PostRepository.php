<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 08/09/2017
 * Time: 16:59
 */

namespace Modules\Post\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Category\Entities\Category;
use Modules\Post\Entities\Post;
use Modules\Post\Exceptions\PostException;
use Modules\Tag\Entities\Tag;
use Modules\User\Entities\User;

class PostRepository
{
    private $post;
    private $user;
    private $category;
    private $tag;

    /**
     * PostRepository constructor.
     * @param Post $post
     * @param User $user
     * @param Category $category
     * @param Tag $tag
     */
    public function __construct(Post $post, User $user, Category $category, Tag $tag)
    {
        $this->post = $post;
        $this->user = $user;
        $this->category = $category;
        $this->tag = $tag;
    }

    private function makeQuery($filter)
    {
        $query = $this->post;

        if(!empty($filter['author']))
            $query = $query->join('users', 'users.id', '=', 'post_user.user_id')
                ->where('users.name', 'LIKE', '%' . $filter['author'] . '%');

        if(!empty($filter['category']))
            $query = $query->join('categories', 'categories.id', '=', 'posts.category_id')
                ->where('categories.name', 'LIKE', '%' . $filter['category'] . '%');

        if(!empty($filter['tag']))
            $query = $query->tags()->where('name', '=', $filter['tag']);

        if(!empty($filter['title']))
            $query = $query->where('title', 'LIKE', '%' . $filter['title'] . '%');

        if(!empty($filter['slug']))
            $query = $query->where('slug', '=', $filter['slug']);

        if(!empty($filter['status']))
            $query = $query->where('status', '=', $filter['status']);

        return $query;
    }

    /**
     * @param $filter
     * @param $perPage
     * @return $this|\Illuminate\Contracts\Pagination\LengthAwarePaginator|Post
     */
    public function allPostsPaginated($filter, $perPage)
    {
        $result = $this->makeQuery($filter);
        $result = $result->paginate($perPage);
        return $result;
    }

    /**
     * @param Post $post
     * @param array $params
     * @return Post
     * @throws PostException
     */
    public function savePost(Post $post, array $params)
    {
        try{
            DB::transaction(function () use ($post, $params){
                $post = $this->setAuthor($post, $params);
                $post = $this->setCategory($post, $params);
                $post->save();
                $this->setTags($post, $params);
            });
        }catch(PostException $e){
            throw $e;
        }

        return $post;
    }

    public function postById($id)
    {
        return $this->post->find($id);
    }

    private function verifyUser($user)
    {
        if(!$user instanceof User)
            throw new PostException('user_not_found');
    }

    private function verifyCategory($category)
    {
        if(!$category instanceof Category)
            throw new PostException('category_not_found');
    }

    private function setAuthor(Post $post, $params)
    {
        if(!empty($params['user_id'])){
            $user = $this->user->find($params['user_id']);
            $this->verifyUser($user);
            $post->user()->associate($user);
        }
        return $post;
    }

    private function setCategory(Post $post, $params)
    {
        if(!empty($params['category_id'])){
            $category = $this->category->find($params['category_id']);
            $this->verifyCategory($category);
            $post->category_id = $category->id;
        }
        return $post;
    }

    private function setTags(Post $post, $params)
    {
        if(!empty($params['tags'])) {
            foreach ($params['tags'] as $value){
                $id = data_get($value, 'id');
                $tag = $this->tag->find($id);
                if ($tag instanceof Tag){
                    $post->tags()->attach($tag->id);
                }else{
                    $tag = new Tag();
                    $tag->name = data_get($value, 'name');
                    $tag->slug = data_get($value, 'slug');
                    $tag->save();
                    $post->tags()->attach($tag->id);
                }
            }
        }
    }

    /**
     * @param $post
     * @return bool
     */
    public function softDeletePost(Post $post)
    {
        if($post->delete())
            return true;
        else
            return false;
    }
}