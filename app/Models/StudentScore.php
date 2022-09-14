<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentScore extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_student_score';
    protected $primaryKey = 'student_score_id'; // or null

    protected $appends = ['id'];

    protected $casts = ['score_data' => 'json'];

    public function getIdAttribute($value)
    {
        return $this->attributes['student_score_id'];
    }
}
