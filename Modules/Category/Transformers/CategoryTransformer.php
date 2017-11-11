<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 12/08/2017
 * Time: 14:40
 */

namespace Modules\Category\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Category\Entities\Category;
use Modules\Post\Transformers\PostTransformer;


class CategoryTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'post'
    ];

    public function transform(Category $category)
    {
        return [
            'type'          => 'category',
            'attributes'    => [
                'id'            => $category->id,
                'name'          => $category->name,
                'slug'          => $category->slug,
                'description'   => $category->description,
            ]
        ];
    }

    public function includePost(Category $category)
    {
        $post = $category->posts;
        return $this->collection($post, new PostTransformer);
    }
}