<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 10/09/2017
 * Time: 09:14
 */

namespace Modules\Comment\Repositories;


use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Comment\Entities\Comment;
use Modules\Post\Entities\Post;

class CommentRepository
{
    use SoftDeletes;

    private $post;
    private $comment;

    /**
     * CommentRepository constructor.
     * @param Post $post
     * @param Comment $comment
     */
    public function __construct(Post $post, Comment $comment)
    {
        $this->post = $post;
        $this->comment = $comment;
    }

    /**
     * @param Post $post
     * @param array $filter
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    private function queryByFilter(Post $post, array $filter = [])
    {
        $query = $post->comments();

        if(!empty($filter['author']))
            $query->join('users', 'users.id', '=', 'comments.user_id')
                ->where('users.name', 'LIKE', '%' . $filter['author'] . '%');

        return $query;
    }

    /**
     * @param $post
     * @param $filter
     * @param $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCommentsPaginated($post, $filter, $perPage)
    {
        $query = $this->queryByFilter($post, $filter);
        $paginated = $query->paginate($perPage);

        return $paginated;
    }

    /**
     * @param Post $post
     * @param Comment $comment
     * @return false|\Illuminate\Database\Eloquent\Model
     */
    public function save(Post $post, Comment $comment)
    {
        return $post->comments()->save($comment);
    }

    /**
     * @param Post $post
     * @param $comment_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function getById(Post $post, $comment_id)
    {
        $comment = $post->comments()->find($comment_id);
        return $comment;
    }

    /**
     * @param Comment $comment
     * @return bool
     */
    public function deleteComment(Comment $comment)
    {
        if($comment->delete())
            return true;
        else
            return false;
    }
}