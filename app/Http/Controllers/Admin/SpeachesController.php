<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AdminUser;
use App\Http\Controllers\Controller;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use App\Models\Speachesapi;
use App\Models\Speachesresult;
use App\User;
use Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class SpeachesController extends Controller
{
    use AdminCrud, Base;
	public function __construct()
    {
        $this->model = Speachesapi::class;
        $this->user_id = $_SESSION['user']['id'] ?? '';
        $this->slug = 'Speaches Lab';
        $this->gridCol = [ 'lesson_id' => 'Lesson', 'user_id'=>'Student', 'total_request'=>'Total Request'];
    }
    public function get_griddata ($request, $gridCol, $mperm)
    {
        $this->user_id = $_SESSION['user']['id'] ?? '';
        
        $data = $this->model::with('students')->with('lesson')->groupBy('user_id')->groupBy('lesson_id');
        if(isset($_GET['class_id']) && $_GET['class_id']>0) {
        	$class_id = $_GET['class_id'];
        	$data->whereHas('students', function($q) use($class_id) {
			    $q->where('class_id', $class_id);
			});
        }
		
        $request['sortby'] = $request['sortby'] == 'id' ? 'display_order' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            //$data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray(); 

       
        $data['data'] = $this->format_griddata($data['data']);

        return $data;
    }
    public function format_griddata ($data) 
    {
    	foreach ($data as $key => $value) {
            $id = $value['id'];
            $actions = "<a class='btn btn-xs text-white btn-primary' title='Edit' ng-click='view($id)'> <i class='fa fa-eye' aria-hidden='true'></i> </a>";
            $student_name = "";
            $lesson_name = "";
            if(isset($value['students']['firstname'])) {
				$student_name = $value['students']['firstname'].' '.$value['students']['lastname'];
            }
            if(isset($value['lesson']['lesson_name'])) {
				$lesson_name = $value['lesson']['lesson_name'];
            }
           $request_count = $this->model::where('user_id', $value['user_id'])->where('lesson_id', $value['lesson_id'])->count();

            $data[$key]['lesson_id'] = $lesson_name;
            $data[$key]['user_id'] = $student_name;
            $data[$key]['total_request'] = $request_count;
            $data[$key]['actions'] = $actions;
            unset($data[$key]['lesson']);
            unset($data[$key]['students']);
        }
        return $data;
    }
    public function getspeeachresult (Request $request) 
    {
        $speachApiData = $this->model::find($request->id);
        if(isset($speachApiData->user_id)) {
            $speachesresult = Speachesresult::where('user_id', $speachApiData->user_id)->where('lesson_id', $speachApiData->lesson_id)->get();
            $response_data = [];
            foreach ($speachesresult as $key => $value) {
                $result = [];
                $response_json = json_decode($value->response_json, true);
                
                foreach ($response_json['text_score']['word_score_list'] as $text) {
                   
                    $phone_score_list_ar = [];
                    foreach ($text['phone_score_list'] as  $phone_score_list) {
                        $result_grade = "Bad";
                        if($phone_score_list['quality_score']>=85) {
                            $result_grade = "Good";
                        }
                        $phone_score_list_ar [] = [
                            'phone'=> $phone_score_list['phone'],
                            'quality_score'=> number_format($phone_score_list['quality_score'], 2),
                            'sound_most_like'=> $phone_score_list['sound_most_like'],
                            'result_grade'=> $result_grade,
                        ];
                    }
                    $result[] = [
                        'text'=>$text['word'],
                        'quality_score'=>number_format($text['quality_score'],2),
                        'phone_score_list'=>$phone_score_list_ar,
                    ]; 
                    
                }
                
                $response_data [] = [
                    'student_name' =>$value->students->firstname.' '.$value->students->lastname,
                    'lesson_name' =>$value->lesson->lesson_name,
                    'result' =>$result,
                ];
            }
            if(count($response_data)==0) {
                return response()->json([
                    'status'=>'false',
                    'message'=>'No Record found'
                ]);    
            } else {
                return response()->json([
                    'status'=>'true',
                    'message'=>'Record found',
                    'data'=>$response_data
                ]);
            }
            

        } else {
            return response()->json([
                'status'=>'false',
                'message'=>'No Record found'
            ]);
        }
        print_r();
        die();
        echo $request->id;
        die("as");
    }
}