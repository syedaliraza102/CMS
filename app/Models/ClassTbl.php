<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassTbl extends Model
{

    use SoftDeletes;
    protected $table = 'tbl_class';
    protected $primaryKey = 'class_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['class_id'];
    }

    public function assign_teacher()
    {
        return $this->hasMany(AssignTeacher::class, 'class_id', 'class_id');
    }
}
