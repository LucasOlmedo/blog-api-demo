<?php

namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Post\Entities\Post;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed slug
 * @property mixed description
 * @property mixed posts
 */
class Category extends Model
{

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    protected $visible = [
        'id',
        'name',
        'slug',
        'description'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
