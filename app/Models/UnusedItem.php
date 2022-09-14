<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnusedItem extends Model
{

    protected $table = 'tbl_unused_item';
    protected $primaryKey = 'unused_item_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['unused_item_id'];
    }
}
