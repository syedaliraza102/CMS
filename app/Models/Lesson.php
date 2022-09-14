<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_lesson';
    protected $primaryKey = 'lesson_id'; // or null

    protected $appends = ['id'];


    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->display_order = $model->id;
            $model->update();
        });
    }

    protected $casts = ['student_ids' => 'json', "package_ids" => "json"];

    public function getIdAttribute($value)
    {
        return $this->attributes['lesson_id'];
    }

    public function lesson_student()
    {
        return $this->hasMany(LessonStudent::class, 'lesson_id', 'lesson_id');
    }

    public function lesson_fragment()
    {
        return $this->hasMany(LessonFragment::class, 'lesson_id', 'lesson_id');
    }

    public function lesson_fragment_completed()
    {
        return $this->hasMany(LessonFragment::class, 'lesson_id', 'lesson_id');
    }


    public function score()
    {
        return $this->hasMany(StudentScore::class, 'lesson_id', 'lesson_id');
    }
}
