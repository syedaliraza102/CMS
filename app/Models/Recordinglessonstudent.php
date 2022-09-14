<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recordinglessonstudent extends Model
{

    protected $table = 'tbl_recording_lesson_student';

    public function lesson()
    {
        return $this->hasOne(Recordinglesson::class, 'id', 'lesson_id');
    } 
    public function students()
    {
        return $this->belongsTo(\App\User::class, 'student_id', 'id');
    }
}
