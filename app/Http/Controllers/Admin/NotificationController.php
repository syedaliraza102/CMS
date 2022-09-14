<?php

namespace App\Http\Controllers\Admin;

use App\Models\OfflineHomework;
use App\Http\Controllers\Controller;
use App\Models\ClassTbl;
use App\Models\AdminUser;
use App\Models\LessonStudent;
use App\Models\AssignTeacher;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use App\User;
use App\Models\Recordinglessonstudent;
use App\Models\AssigmentLesson;

class NotificationController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->user = $_SESSION['user'] ?? [];

    }

    public function notification () 
    {
        $student_id = $this->user['id'];
        
        $data  = [
           'pending_assingments' =>$this->pending_assingments(), 
           'home_work_pending' =>count($this->pending_home_works('ls')), 
           'exams_work_pending' =>count($this->pending_home_works('ex')),
           'challenges_work_pending' =>count($this->pending_home_works('challenges')),
           'recording_lesson' =>Recordinglessonstudent::where('attempt', '0')->where('student_id', $student_id)->count()
        ];
       
        return response()->json([$data]);
    }
    public function pending_assingments () 
    {
        $school_id = $_SESSION['user']['school_id'] ?? null;

        $data = AssigmentLesson::where('school_id', $school_id)->where('class_id', $_SESSION['user']['class_id'])->paginate(100000);
        $cls = ClassTbl::pluck('class_name', 'class_id')->toArray();
        $count_array = [];
        foreach ($data as $key => $value) {
            $temp = \DB::selectOne('SELECT  al.*, ast.*, al.lesson_id as lesson_id,al.created_at as created_at FROM `tbl_assigment_lesson` as al LEFT JOIN tbl_assigment_student as ast ON al.lesson_id = ast.lesson_id AND ast.student_id = ? WHERE al.lesson_id = ? group by al.lesson_id ', [$this->user['id'], $value['lesson_id']]);
            $data[$key] = collect($temp)->toArray();
            $status = "Pending";
            if (!empty($data[$key]['assigment_student_id'])) {
                $status = 'Submited';
            }
            if (!empty($data[$key]['is_checked']) && $data[$key]['is_checked'] == 'y') {
                $status  = 'Checked';
                if (!empty($data[$key]['is_student_checked']) && $data[$key]['is_student_checked'] == 'y') {
                    $status = 'Completed';
                }
            }
            if($status==='Pending') {
                 $count_array[] = 'pending';
            }
        }    
       
        return count($count_array);
       
    }

    public function pending_home_works ($type) 
    {
        $student_ids = [$this->user['id'] ?? 0] ?? [0];
        $class_ids = [$this->user['class_id'] ?? 0] ?? [0];

        $filterval =  [];
        $data = LessonStudent::scoreData($student_ids, $class_ids, $filterval);
        $data = $data->whereHas('lesson', function ($q) use($type) {
            return $q->where('lesson_type', $type);
        });
        $data = $data->paginate(100000)->toArray();
        $pending_status = [];
        foreach ($data['data'] as $home_data) {
            $lesson_fragment = $home_data['lesson']['lesson_fragment'] ?? [];
            $studentscore = collect($home_data['studentscore'] ?? []);


            $lesson_fragment = collect($lesson_fragment);
            
            $lg_ids = $lesson_fragment->pluck('lg_id')->toArray();
            
            $total_modules = $lesson_fragment->count();

            $completed_modules = $studentscore->whereIn('lg_id', $lg_ids)->groupBy('lg_id')->count();

            $pending_modules = $total_modules - $completed_modules;

            if($pending_modules>0) {
                $pending_status[] = ['1'];
            }
        }
        return $pending_status;
    }
}
