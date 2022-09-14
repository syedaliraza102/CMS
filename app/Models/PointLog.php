<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PointLog extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_point_log';
    protected $primaryKey = 'point_id'; // or null

    protected $appends = ['id'];

    protected $casts = ['point_data' => 'json'];

    public function getIdAttribute($value)
    {
        return $this->attributes['point_id'];
    }


    public static function updatePoints($student_id)
    {
        $sobj = AdminUser::find($student_id);
        if (!empty($sobj)) {
            $finalPoints = PointLog::where('student_id', $student_id)->sum('points');
            $sobj->points = $finalPoints;
            $sobj->update();
        }
        //return $this->attributes['point_id'];
    }
}
