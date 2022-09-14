<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users extends Model
{

    use SoftDeletes;
    protected $table = 'users';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('user_type', function (Builder $builder) {
            $builder->where('user_type', 'c');
        });
    }
}
