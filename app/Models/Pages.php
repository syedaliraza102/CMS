<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Pages extends Model
{
    protected $table = 'pages';
    protected $casts = ['image' => 'json', 'seo' => 'json'];

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = str_slug($value);
    }
}
