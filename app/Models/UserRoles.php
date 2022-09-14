<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class UserRoles extends Model
{
    protected $table = 'user_roles';

    protected $casts = ['actions' => 'json'];

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = str_slug($value);
    }
}
