<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MultiplayerRoomStudent extends Model
{

    //use SoftDeletes;
    protected $table = 'tbl_multiplayer_room_student';
    protected $primaryKey = 'room_student_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['room_student_id'];
    }
}
