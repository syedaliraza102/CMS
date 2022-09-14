<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_package';
    protected $primaryKey = 'package_id'; // or null

    protected $appends = ['id'];


    public function getIdAttribute($value)
    {
        return $this->attributes['package_id'];
    }
}
