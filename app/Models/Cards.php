<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cards extends Model
{

    protected $table = 'tbl_card';
    protected $primaryKey = 'card_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['card_id'];
    }

    public function classtbl()
    {
        return $this->hasOne(ClassTbl::class, 'class_id', 'class_id');
    }
}
