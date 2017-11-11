<?php

namespace Modules\User\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\Comment\Entities\Comment;
use Modules\Post\Entities\Post;
use Modules\Role\Entities\Role;
use Laravel\Passport\HasApiTokens;

/**
 * @property string password
 * @property mixed email
 * @property mixed name
 * @property mixed avatar
 * @property mixed id
 * @property mixed role
 * @property mixed posts
 * @property mixed comments
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Illuminate\Database\Eloquent\Relations\BelongsToMany|\Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

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
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin()
    {
        return $this->hasRole('administrator');
    }

    public function isAuthor()
    {
        return $this->hasRole('author');
    }

    /**
     * @param $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role()->where('name', $role)->first() ? true : false;
    }

    /**
     * @param $roles
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        $args = [];
        if (is_array($roles)) {
            foreach ($roles as $role) {
                $args[] = $this->hasRole($role);
            }
            return in_array("true", $args) ? true : false;
        }else {
            return $this->hasRole($roles);
        }
    }

    /**
     * @param $roles
     * @return bool
     */
    public function authorizeRoles($roles)
    {
        if($this->hasAnyRole($roles)){
            return true;
        }else{
            return false;
        }
    }
}
