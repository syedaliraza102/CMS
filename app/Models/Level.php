<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{

    use SoftDeletes;
    protected $table = 'tbl_level';
    protected $primaryKey = 'level_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['level_id'];
    }
}
