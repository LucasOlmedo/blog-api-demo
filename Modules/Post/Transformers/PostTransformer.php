<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 12/08/2017
 * Time: 16:46
 */

namespace Modules\Post\Transformers;


use League\Fractal\TransformerAbstract;
use Modules\Category\Transformers\CategoryTransformer;
use Modules\Comment\Transformers\CommentTransformer;
use Modules\Post\Entities\Post;

class PostTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'author',
        'comments',
        'tags',
        'category'
    ];

    /**
     * @param Post $post
     * @return array
     */
    public function transform(Post $post)
    {
        return [
            'type'          => 'post',
            'attributes'    => [
                'id'            => $post->id,
                'user_id'       => $post->user_id,
                'category_id'   => $post->category_id,
                'title'         => $post->title,
                'slug'          => $post->slug,
                'body'          => $post->body,
                'status'        => $post->status,
                'created_at'    => $post->created_at,
            ]
        ];
    }

    /**
     * @param Post $post
     * @return \League\Fractal\Resource\Item
     */
    public function includeAuthor(Post $post)
    {
        $author = $post->user;
        return $this->item($author, new UserTransformer());
    }

    /**
     * @param Post $post
     * @return \League\Fractal\Resource\Collection
     */
    public function includeComments(Post $post)
    {
        $comments = $post->comments();
        return $this->collection($comments, new CommentTransformer());
    }

    /**
     * @param Post $post
     * @return \League\Fractal\Resource\Collection
     */
    public function includeTags(Post $post)
    {
        $tags = $post->tags;
        return $this->collection($tags, new TagTransformer());
    }

    /**
     * @param Post $post
     * @return \League\Fractal\Resource\Item
     */
    public function includeCategory(Post $post)
    {
        $categories = $post->category;
        return $this->item($categories, new CategoryTransformer());
    }
}