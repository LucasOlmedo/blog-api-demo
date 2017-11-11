<?php

namespace Modules\Post\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Category\Entities\Category;
use Modules\Comment\Entities\Comment;
use Modules\Tag\Entities\Tag;
use Modules\User\Entities\User;

/**
 * @property mixed id
 * @property mixed user_id
 * @property mixed category_id
 * @property mixed title
 * @property mixed slug
 * @property mixed body
 * @property mixed status
 * @property mixed user
 * @property mixed category
 * @property mixed tags
 * @property mixed comments
 * @property mixed created_at
 */
class Post extends Model
{
    use SoftDeletes;

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'title',
        'slug',
        'body',
        'status'
    ];

    protected $visible = [
        'id',
        'title',
        'slug',
        'body',
        'status',
        'created_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
