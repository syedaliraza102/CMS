<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignTeacher extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_teacher_assign';
    protected $primaryKey = 'teacher_assign_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['teacher_assign_id'];
    }
}
