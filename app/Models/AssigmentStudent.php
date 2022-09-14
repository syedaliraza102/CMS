<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssigmentStudent extends Model
{
    //use SoftDeletes;
    protected $table = 'tbl_assigment_student';
    protected $primaryKey = 'assigment_student_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['assigment_student_id'];
    }
}
