<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 12/08/2017
 * Time: 16:46
 */

namespace Modules\Post\Transformers;


use League\Fractal\TransformerAbstract;
use Modules\Tag\Entities\Tag;

class TagTransformer extends TransformerAbstract
{
    public function transform(Tag $tag)
    {
        return [
            'type'          => 'tag',
            'attributes'    => [
                'id'            => $tag->id,
                'name'          => $tag->name,
                'slug'          => $tag->slug,
            ]
        ];
    }

    public function includePosts(Tag $tag)
    {
        $post = $tag->posts;
        return $this->collection($post, new PostTransformer);
    }
}