<?php

namespace Modules\Role\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

/**
 * @property string name
 * @property string description
 * @property mixed id
 * @property mixed users
 */
class Role extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    protected $visible = [
        'id',
        'name',
        'description'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
