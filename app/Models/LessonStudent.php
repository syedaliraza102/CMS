<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LessonStudent extends Model
{
    protected $table = 'tbl_lesson_student';
    protected $primaryKey = 'lesson_student_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['lesson_student_id'];
    }

    public function lesson()
    {
        return $this->hasOne(Lesson::class, 'lesson_id', 'lesson_id');
    }

    public function lesson_fragment()
    {
        return $this->hasOne(LessonFragment::class, 'lesson_id', 'lesson_id');
    }

    public function student()
    {
        return $this->hasOne(AdminUser::class, 'id', 'student_id');
    }

    public function studentscore()
    {
        return $this->hasMany(StudentScore::class, 'ls_id', 'ls_id');
    }


    public static function scoreData($student_ids, $class_ids, $request)
    {
        //dd($student_ids);
        $student_idsnew = [0];
        $data = self::whereIn('student_id', $student_ids)->groupBy('ls_id');
        $data = $data->whereHas('lesson.lesson_fragment');
        if (!empty($class_ids)) {
            //$student_idsnew = AdminUser::whereIn('class_id', $class_ids)->pluck('id')->toArray();
            //dd($student_idsnew);
            //$data = $data->where('student_id', $student_idsnew ?? [0]);
            $data = $data->whereHas('student', function ($q) use ($class_ids) {
                return $q->whereIn('class_id', $class_ids);
            });
        }
        if (!empty($request['filter_lesson_name'])) {
            $data = $data->whereHas('lesson', function ($q) use ($request, $student_ids) {
                return $q->where('lesson_name', 'like', "%" . $request['filter_lesson_name'] . "%");
            });
        }

        if (!empty($request['filter_status']) && $request['filter_status'] != 'all') {
            $query = "SELECT ls.*, count(lf.lesson_fragment_id) as total,
            (SELECT COUNT( DISTINCT(lg_id)) FROM `tbl_student_score` WHERE ls_id = ls.lesson_id AND lg_id = lf.lg_id) as completed
            FROM `tbl_lesson_student` as ls
            LEFT JOIN tbl_lesson_fragment as lf on ls.lesson_id = lf.lesson_id
            GROUP by ls.ls_id";

            if ($request['filter_status'] == 'c') {
                $query .= ' HAVING total = completed ';
            }
            if ($request['filter_status'] == 'p') {
                $query .= ' HAVING total != completed ';
            }
            $ldata = \DB::select($query);
            $ids = !empty($ldata) ? array_column($ldata, 'ls_id') : [0];
            $data = $data->whereIn('ls_id', $ids);
        }
        $data = $data->with([
            'student.classtbl',
            'lesson.lesson_fragment.game', 'lesson.lesson_fragment', 'studentscore' => function ($q) use ($student_ids, $class_ids) {
                return $q->whereIn('student_id', $student_ids)->whereIn('class_id', $class_ids);
            }
        ]);
        return $data;
    }
}
