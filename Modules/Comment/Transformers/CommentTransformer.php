<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 12/08/2017
 * Time: 16:46
 */

namespace Modules\Comment\Transformers;


use League\Fractal\TransformerAbstract;
use Modules\Comment\Entities\Comment;

class CommentTransformer extends TransformerAbstract
{
    public function transform(Comment $comment)
    {
        return [
            'id'        => $comment->id,
            'post_id'   => $comment->post_id,
            'user_id'   => $comment->user_id,
            'body'      => $comment->body,
        ];
    }

}