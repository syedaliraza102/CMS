<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AdminUser;
use App\Http\Controllers\Controller;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use App\Models\Recordinglesson;
use App\Models\Recordinglessonstudent;
use App\User;
use Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class RecordingController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = Recordinglesson::class;
        $this->user_id = $_SESSION['user']['id'] ?? '';
        $this->slug = 'teacherrecording';
        $this->gridCol = [ 'id' => 'Id', 'lesson_name' => 'Assignment Name', 'not_attempted'=>'Not Attempted', 'submited'=>'Submited', 'completed'=>'Completed'];

    }
     public function can_add($mperm)
    {
        if ($mperm['role'] == 'teachers') {
            return true;
        }
        return false;
    }
    public function addteacherrec (Request $request) 
    {
     
        $classStudent = AdminUser::where('class_id', $request->class_id)->get();
        $recordingLesson = new Recordinglesson();
        $recordingLesson->lesson_name = $request->lesson_name;
        $recordingLesson->lesson = $request->lesson;
        $recordingLesson->class_id = $request->class_id;
        $recordingLesson->teacher_id = $this->user_id;
        $recordingLesson->save();

        foreach ( $request->student_ids as $student ) {
            
            $recordingLessonStudent = new Recordinglessonstudent();
            $recordingLessonStudent->lesson_id = $recordingLesson->id; 
            $recordingLessonStudent->student_id =$student;
            $recordingLessonStudent->attempt = '0';
            $recordingLessonStudent->save();

        }

        return response()->json(['status'=>'true', 'msg'=>'Recording Lesson has been added']);
    }
    
    public function get_griddata($request, $gridCol, $mperm)
    {
        $this->user_id = $_SESSION['user']['id'] ?? '';
        
        $data = $this->model::where('teacher_id', $this->user_id);
        
        //$data = $data->where('teacher_id', $this->user_id);
        

        $request['sortby'] = $request['sortby'] == 'id' ? 'display_order' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            //$data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray(); 

       
        $data['data'] = $this->format_griddata($data['data'], $mperm);

        return $data;
    }
    
    public function format_griddata($data, $mperm)
    {
        $copy_icon = url('public/icons/17.png');
        $edit_icon = url('public/icons/18.png');
        $delete_icon = url('public/icons/19.png');

        foreach ($data as $key => $value) {
            $id = $value['id'];
             $actions = "<a class='btn btn-xs text-white btn-primary' title='Edit' ng-click='edit($id)'> <i class='fa fa-pencil' aria-hidden='true'></i> </a>
            <a class='btn btn-xs text-white btn-danger' title='Delete' ng-click='deleteRecord($id)'> <i class='fa fa-trash' aria-hidden='true'></i> </a";
           

            $temp = \DB::selectOne('SELECT  * from tbl_recording_lesson where teacher_id='.$this->user_id.' AND id='.$value['id']);
            $data[$key] = collect($temp)->toArray();

            $not_attempted = Recordinglessonstudent::where('lesson_id', $value['id'])->where('attempt', '0')->count();
            $attempted = Recordinglessonstudent::where('lesson_id', $value['id'])->where('attempt', '1')->count();
            $completed = Recordinglessonstudent::where('lesson_id', $value['id'])->where('is_completed', '1')->count();

            $data[$key]['not_attempted'] = "<a class='btn btn-xs text-white btn-danger ' ng-click='notattempted($id)' style='font-weight:1000;font-size:18px;cursor:pointer'> $not_attempted </a>";
            $data[$key]['submited'] = "<a class='btn btn-xs text-white btn-primary' style='font-weight:1000;font-size:18px;cursor:pointer' ng-click='attempted($id)'> $attempted </a>";
            $data[$key]['completed'] = "<a class='btn btn-xs text-white btn-success' style='font-weight:1000;font-size:18px;cursor:pointer' ng-click='completed($id)'> $completed </a>";

            $actions .= $this->action_formate($value, $mperm);
            $data[$key]['actions'] = $actions;
        }
        return $data;
    }
    public function delete(Request $request, $id ) {
        Recordinglesson::where('id', $id)->delete();
        return response()->json(['status'=>'true']);
    }
    public function getData( $id ) {
        $recordinglesson = Recordinglesson::where('id', $id)->first();
        $recordingLessonStudent =Recordinglessonstudent::where('lesson_id', $id)->get();
        $student_ids = [];
        foreach ($recordingLessonStudent as $recordingstud) {
            $student_ids[]=$recordingstud->student_id;
        }
        return response()->json(['status'=>'true', 'recordinglesson'=>$recordinglesson, 'student_ids'=>$student_ids]);
    }
    public function updateteacherrec(Request $request) {
        $is_class_change = "0";
        $recordingLesson = Recordinglesson::find($request->id);
        $recordingLesson->lesson_name = $request->lesson_name;
        $recordingLesson->lesson = $request->lesson;
        if($request->class_id!=$recordingLesson->class_id) {
            $is_class_change = "1";
        }
        $recordingLesson->class_id = $request->class_id;
        $recordingLesson->teacher_id = $this->user_id;
        $recordingLesson->save();
        if($is_class_change==="1"){
            Recordinglessonstudent::where('lesson_id', $request->id)->delete();
            
            foreach ( $request->student_ids as $student ) {
            
                $recordingLessonStudent = new Recordinglessonstudent();
                $recordingLessonStudent->lesson_id = $request->id; 
                $recordingLessonStudent->student_id =$student;
                $recordingLessonStudent->attempt = '0';
                $recordingLessonStudent->save();
                
            }
        }
        return response()->json(['status'=>'true', 'msg'=>'Recording Lesson has been updated']);
    }
    
    public function studentnotattempt ($id) {
        $not_attempted = Recordinglessonstudent::with('students')->where('lesson_id', $id)->where('attempt', '0')->get();
        return response()->json(['data'=>$not_attempted]);
    }
    public function studentcompleted ($id) {
        $completed = Recordinglessonstudent::with('students')->where('lesson_id', $id)->where('is_completed', '1')->get();
        return response()->json(['data'=>$completed]);
    } 
    public function studentattempt ($id) {
        $attempted = Recordinglessonstudent::with('lesson')->with('students')->where('lesson_id', $id)->where('attempt', '1')->get();
        foreach($attempted as $key=>$value) {
            if(isset($attempted[$key]->audio_name) && $attempted[$key]->audio_name!="") {
                $attempted[$key]->audio_name = url('public/uploads/audio/').'/'.$attempted[$key]->audio_name;
            }    
        }
        
        return response()->json(['data'=>$attempted]);
    } 

    public function teacherstudiorecaddpoint (Request $request) {
        $data = Recordinglessonstudent::where('id', $request->id)->first();
        $data->remarks = $request->remarks;
        if(isset($request->is_completed)) {
            $data->is_completed = $request->is_completed;
        }
        $data->score = $request->score;
        $data->save();
        return response()->json(['status'=>true]);
    }
}
