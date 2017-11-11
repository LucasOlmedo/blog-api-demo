<?php

namespace Modules\Tag\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Post\Entities\Post;

/**
 * @property mixed name
 * @property mixed slug
 * @property mixed id
 * @property mixed posts
 */
class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug'
    ];

    protected $visible = [
        'id',
        'name',
        'slug'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
