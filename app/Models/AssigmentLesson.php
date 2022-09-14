<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssigmentLesson extends Model
{
    //use SoftDeletes;
    protected $table = 'tbl_assigment_lesson';
    protected $primaryKey = 'lesson_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['lesson_id'];
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->display_order = $model->lesson_id;
            $model->update();
        });
    }
}
