<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MultiplayerRoom extends Model
{

    //use SoftDeletes;
    protected $table = 'tbl_multiplayer_room';
    protected $primaryKey = 'room_id'; // or null

    protected $appends = ['id'];

    //protected $casts = ['student_ids' => 'json'];

    public function getIdAttribute($value)
    {
        return $this->attributes['room_id'];
    }



    public function getStudentIdsAttribute($value)
    {
        if (!empty($value)) {
            return explode(',', $value);
        }
        return [];
    }

    public function setStudentIdsAttribute($value)
    {
        if (!empty($value) && is_array($value)) {
            $this->attributes['student_ids'] = implode(',', $value);
        } else {
            $this->attributes['student_ids'] = null;
        }
    }

    public function fragment()
    {
        return $this->hasOne(Fragment::class, 'fragment_id', 'fragment_id');
    }

    public function classTbl()
    {
        return $this->hasOne(ClassTbl::class, 'class_id', 'class_id');
    }
}
