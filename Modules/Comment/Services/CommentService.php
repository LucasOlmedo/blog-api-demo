<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 10/09/2017
 * Time: 09:14
 */

namespace Modules\Comment\Services;


use Modules\Comment\Entities\Comment;
use Modules\Comment\Exceptions\CommentException;
use Modules\Comment\Repositories\CommentRepository;
use Modules\Post\Entities\Post;
use Modules\User\Entities\User;

class CommentService
{
    private $commentRepository;
    private $user;

    /**
     * CommentService constructor.
     * @param CommentRepository $repository
     */
    public function __construct(CommentRepository $repository, User $user)
    {
        $this->commentRepository = $repository;
        $this->user = $user;
    }

    public function getAllPaginated(Post $post, array $filter = [], $perPage = 20)
    {
        return $this->commentRepository->getCommentsPaginated($post, $filter, $perPage);
    }

    /**
     * @param Post|\Illuminate\Database\Eloquent\Model $post
     * @param Comment|\Illuminate\Database\Eloquent\Model $comment
     * @param array $data
     * @return false|\Illuminate\Database\Eloquent\Model
     * @throws CommentException
     */
    public function saveComment(Post $post, Comment $comment, array $data)
    {
        $user = $this->user->find($data['user_id']);
        if($user instanceof User){
            $comment->user_id = $user->id;
            $comment->post_id = $post->id;
            $comment->body = $data['body'];

            return $this->commentRepository->save($post, $comment);
        }

        throw new CommentException('store_error');
    }

    /**
     * @param Post $post
     * @param $comment_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function getCommentById(Post $post, $comment_id)
    {
        return $this->commentRepository->getById($post, $comment_id);
    }

    /**
     * @param $comment
     * @return bool
     */
    public function deleteByComment($comment)
    {
        return $this->commentRepository->deleteComment($comment);
    }
}