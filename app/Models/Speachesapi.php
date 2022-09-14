<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Speachesapi extends Model
{
    protected $table = 'tbl_speachesapi';
    
    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id', 'lesson_id');
    } 
    public function students()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }

}
