<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonFragment extends Model
{
    protected $table = 'tbl_lesson_fragment';
    protected $primaryKey = 'lesson_fragment_id'; // or null
    protected $casts = ['fragment_ids' => 'json'];

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['lesson_fragment_id'];
    }

    public function lesson()
    {
        return $this->hasOne(Lesson::class, 'lesson_id', 'lesson_id');
    }

    public function game()
    {
        return $this->hasOne(Game::class, 'game_id', 'game_id');
    }

    public function student_score()
    {
        return $this->hasMany(StudentScore::class, 'lg_id', 'lg_id');
    }

    // protected static function boot()
    // {
    //     parent::boot();
    //     // auto-sets values on creation

    //     static::created(function ($query) {
    //         $query->lg_id = $query->id . '_' . $query->lesson_id . '_' . $query->game_id;
    //         $query->update();
    //     });

    //     static::updating(function ($query) {
    //         dd('called');
    //         //$query->lg_id = $query->id . '_' . $query->lesson_id . '_' . $query->game_id;
    //         //$query->update();
    //         $query->lg_id = 'kalpak';
    //         $query->update();
    //     });
    // }
}
