<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AdminUser;
use App\Http\Controllers\Controller;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use App\Models\Recordinglesson;
use App\Models\Recordinglessonstudent;
use Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class StudentrecordingController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = Recordinglessonstudent::class;
         $this->user_id = $_SESSION['user']['id'] ?? '';
        $this->slug = 'teacherrecording';
        $this->gridCol = [ 'id' => 'Id', 'lesson_name' => 'Assignment Name', 'attempt'=>'Is Attempet', 'score'=>'Score'];

    }
     public function can_add($mperm)
    {
        if ($mperm['role'] == 'teachers') {
            return true;
        }
        return false;
    }
    
    public function getData($id) {
        $data = $this->model::with('lesson')->where('id', $id)->first();
        return response()->json($data);
    }

    public function get_griddata($request, $gridCol, $mperm)
    {
        $this->user_id = $request->id;
        $data = $this->model::with('lesson')->where('student_id', $this->user_id);
        //$data = $data->where('teacher_id', $this->user_id);
        

        //$request['sortby'] = $request['sortby'] == 'id' ? 'display_order' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray(); 

       
        $data['data'] = $this->format_griddata($data['data'], $mperm);

        return $data;
    }
     public function format_griddata($data, $mperm)
    {
        $view_icon = url('public/icons/20.png');

        foreach ($data as $key => $value) {
            $id = $value['id'];
            $actions = "<a class='btn btn-xs text-white btn-primary' title='Edit' ng-click='attempt($id)'> <i class='fa fa-eye' aria-hidden='true'></i> </a>";


            $temp = \DB::selectOne('SELECT  * from tbl_recording_lesson_student where student_id='.$this->user_id.' AND id='.$value['id']);
            $data[$key] = collect($temp)->toArray();
         
            
            $lesson_name = "";
            $lesson = "";
            $attempt = "";
            if(isset($value['lesson']['lesson'])) {
                $lesson = $value['lesson']['lesson'];
                $lesson_name = $value['lesson']['lesson_name'];
            }
            if($value['remarks']!='') {
                $lesson_name.="<span style='color:red'><br><b>Remarks</b><br>".$value['remarks']."</span>";   
            }
            if($value['attempt'] === "1") {
                $attempt = "Yes";
                $actions = "";
            }
            
            if($value['attempt'] === "0") {
                $attempt = "No";
            }

            $actions .= $this->action_formate($value, $mperm);
            $data[$key]['lesson_name'] = $lesson_name;
            $data[$key]['lesson'] = $lesson;
            $data[$key]['attempt'] = $attempt;
            $data[$key]['actions'] = $actions;
        }
        return $data;
    }
    
    public function studentrecordingattemptadd(Request $request)
    {
        
        $file_name = $_POST['audio-filename'];
        $errors= array();
        $file_size =$_FILES['audio-blob']['size'];
        $file_tmp =$_FILES['audio-blob']['tmp_name'];
        $uploadFileDir = public_path().'/uploads/audio/';
        $dest_path = $uploadFileDir .$file_name;
   
        move_uploaded_file($file_tmp, $dest_path);
      
        $audioData = $this->model::where('id', $_POST['lesson_id'])->first();
        $audioData->attempt = "1";
        $audioData->audio_name = $file_name;
        $audioData->save();
        return response()->json(['status'=>'true']);
    }
   
}
