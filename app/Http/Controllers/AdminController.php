<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use \App\Models\SiteSetting;
use \App\Models\School;
use \App\Common;
use App\Models\AdminUser;
use App\Models\AssignTeacher;
use App\Models\Cards;
use App\Models\ClassTbl;
use App\Models\LessonStudent;
use App\Models\OfflineHomework;
use App\Models\PointLog;
use App\Models\RelaxationClass;
use App\Models\SchoolSubscription;
use App\Models\Level;

use \App\Traits\Base;
use Hash;

use Faker\Provider\Image;
use Faker\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AdminController extends Controller
{
    use Base;

    public function __construct()
    {
        $this->mperm = Common::user_roles();
    }

    public function getalllevels()
    {
        return response()->json(Level::all());
    }

    public function index()
    {
        //dd(public_path('images'));
        return view('admin', ['mlist' => json_encode(Common::module_list())]);
    }

    public function login()
    {
        return view('adminauth', ['mlist' => json_encode(Common::module_list())]);
    }

    public function userprofile(Request $request)
    {
        Common::check_access();
        $email = str_replace('Basic ', '', str_replace('BiZDE5NTAwIn0%3D', '', $request->header('Authorization')));
        $feildvalues = User::where('user_name', $email)->first();
        $forms = [
            'firstname' => ['rules' => 'required'],
            'lastname' => ['rules' => 'required'],
            'user_name' => ['rules' => 'required'],
            'email' => ['type' => 'email'],
            'phone_no' => ['rules' => 'required|maxlength:255'],
            'address' => ['rules' => 'required|maxlength:255'],
            'avatar' => ['type' => 'image', 'dir' => 'user'],
        ];
        if (!empty($_SESSION['user']['admin_role']) && $_SESSION['user']['admin_role'] != 'teacher') {
            unset($forms['address']);
        }
        //dd($_SESSION['user']['admin_role']);


        $form_str = '';
        foreach ($forms as $key => $attr) {
            $fval =  !empty($feildvalues[$key]) && $key != 'password' ? $feildvalues[$key]  : '';
            $data = Common::format_feilddata($key, $fval, $attr);
            $form_str .= view('admin.feilds.' . $data['type'], ['data' => $data]);
        }


        $school = School::find($feildvalues['school_id'] ?? 0);

        if (!empty($school['allow_print']) && $school['allow_print'] == 'y') {
            $cards = Cards::where('student_id', $feildvalues['id'])->where('is_buy', 'y')->orderBy('created_at', 'desc')->get()->toArray();
        } else {
            $cards = Cards::where('student_id', $feildvalues['id'])->orderBy('created_at', 'desc')->get()->toArray();
        }

        $formData = [
            'cards' => $cards,
            "allow_print" => $school['allow_print'] ?? 'n',
        ];
        return ['form_str' => $form_str, 'flag' => 1, 'formData' => $formData];
        return $request->all();
    }

    public function updateuserprofile(Request $request)
    {
        $input = $request->all();
        //dd($input);
        Common::check_access('admin.userprofile');
        $email = str_replace('Basic ', '', str_replace('BiZDE5NTAwIn0%3D', '', $request->header('Authorization')));
        $feildvalues = User::where('user_name', $email)->first();
        $user = User::find($feildvalues['id']);

        $data = $request->all();
        unset($data['_token']);
        unset($data['card_id']);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }



        foreach ($data as $key => $value) {
            $user->$key = $value;
        }

        if ($user->update()) {
            if (!empty($input['card_id'])) {
                Cards::where('student_id', $feildvalues['id'])->update(['is_avatar' => 'd']);
                Cards::where('card_id', $input['card_id'])->update(['is_avatar' => 'a']);
            }
            return ['flag' => 1, 'msg' =>  'Profile Updated Successfully.', 'data' => $user->toArray()];
        } else {
            return ['flag' => 2, 'msg' => ' Oops! Something went worng.', 'data' => []];
        }
        return $request->all();
    }

    public function sitesetting(Request $request)
    {
        //return \Request::route()->getName();
        Common::check_access();
        $feildvalues = SiteSetting::get_inputval();
        $data =  SiteSetting::all()->toArray();
        $final = [];
        foreach ($data as $key => $value) {
            $field = ['title' => $value['title'], 'type' => $value['input_type']];
            if (empty($final[$value['group_id']]['fields'])) {
                $final[$value['group_id']]['title'] = $value['group_id'];
                $final[$value['group_id']]['fields'][$value['input_key']] = $field;
            } else {
                $final[$value['group_id']]['fields'][$value['input_key']] = $field;
            }
        }
        $tabs = [];
        foreach ($final as $key => $value) {
            array_push($tabs, $value);
        }

        return ['form_str' => Common::render_tabs($tabs, $feildvalues), 'flag' => 1];
        return $request->all();
    }

    public function updatesitesetting(Request $request)
    {
        Common::check_access('admin.sitesetting');
        $data = $request->all();
        unset($data['_token']);
        foreach ($data as $key => $value) {
            $obj = SiteSetting::where('input_key', $key)->first();
            if (!empty($obj)) {
                $sobj = SiteSetting::find($obj['id']);
                $sobj->config_value = $value;
                $sobj->update();
            }
        }
        return ['flag' => 1, 'msg' =>  'Site Settings Updated Successfully.'];
    }



    public function userlogin(Request $request)
    {
        $user = User::where('user_name', $request['email'])->where('user_type', 'a')->first();
        if (empty($user)) {
            return ['flag' => 2, 'msg' => 'Username or password is not valid.'];
        }


        $validCredentials = Hash::check($request['password'], $user->getAuthPassword());

        if ($validCredentials) {
            if ($user->admin_role != 'admin') {
                $scl = School::find($user->school_id);
                if (empty($scl)) {
                    return ['flag' => 2, 'msg' => 'This User school is deleted or deactivated'];
                }
            }

            $user->email = base64_encode($user->email) . 'BiZDE5NTAwIn0%3D';
            $adminuser = User::where('user_name', $user->user_name)->with('admin_role_data')->where('user_type', 'a')->first();
            $extraData = [
                'sidebar' => Common::sidebar(),
                'title' => [],
                'adminuser' => $adminuser,
                'permission' => $adminuser['admin_role_data']['actions'] ?? [],
                'footer' => [],
                'notification' => $this->get_notification(),
            ];
            return ['flag' => 1, 'msg' => 'Login Successfully', 'data' => $user, 'extraData' => $extraData];
        } else {
            return ['flag' => 2, 'msg' => 'Email or password is not valid'];
        }

        return $request->all();
    }



    public function get_notification()
    {

        return ['count' => 0, 'data' => []];
    }

    public function image_upload(Request $request)
    {
        $files = $_FILES['file'];
        $request['dir'] = $request['dir'] ? trim($request['dir'], '/') . '/' : 'uploads/';
        $upload_dir =  'images/' . $request['dir'];
        $upload_path = str_replace('/', DIRECTORY_SEPARATOR, base_path('public/' . $upload_dir));
        $result_path = url($upload_dir) . '/';
        $response = [];

        if (!empty($files)) {

            //if (!file_exists($upload_path)) {
            @mkdir($upload_path, 0777, true);
            //}

            $total = count($files['name']);
            for ($i = 0; $i < $total; $i++) {
                $tmpFilePath = $files['tmp_name'][$i];
                if ($tmpFilePath != "") {
                    $newfile = md5(date('YmdHisu') . $i) . '.' . strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                    $newFilePath = $upload_path  . $newfile;
                    if (@move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $response[$i] = Common::format_sfile($upload_dir . $newfile);
                    }
                }
            }
        }
        //return strtolower(pathinfo($files[' name '][0], PATHINFO_EXTENSION));
        return ['flag' => 1, 'files' => $response];
    }

    public function image_remove(Request $request)
    {
        $file = str_replace('/', DIRECTORY_SEPARATOR, base_path('public/' . $request['path']));
        if (file_exists($file) && !is_dir($file)) {
            unlink($file);
        }
        return  ['flag' => 1];
    }

    public function fill_form($forms, $feildvalues)
    {
        $form_str = '';
        foreach ($forms as $key => $attr) {
            $fval =  !empty($feildvalues[$key]) && $key != 'password' ? $feildvalues[$key]  : '';
            $data = Common::format_feilddata($key, $fval, $attr);
            $form_str .= view('admin.feilds.' . $data['type'], ['data' => $data]);
        }
        return $form_str;
    }

    public function customaction($actionslug, $id, Request $request)
    {
        if ($actionslug == 'sendnewsletter') {
            return $this->getsendnewsletter($id, $request);
        } else {
            return ['flag' => 2, 'msg' => 'page Not Found'];
        }
    }

    public function submitcustomaction($actionslug, $id, Request $request)
    {
        if ($actionslug == 'sendnewsletter') {
            return $this->submitsendnewsletter($id, $request);
        } else {
            return ['flag' => 2, 'msg' => 'Page Not Found'];
        }
    }

    public function getsendnewsletter($id, $request)
    {
        $feildvalues = '';
        if (empty($feildvalues)) {
            return ['flag' => 1, 'mform' => 'Page Not Found'];
        }
        $feildvalues = $feildvalues->toArray();
        $forms = [

            'title' => ['rules' => 'required'],
            'subject' => ['rules' => 'required'],
            'description' => ['type' => 'textarea', 'rules' => 'required'],
        ];
        $mform = $this->fill_form($forms, $feildvalues);
        return ['flag' => 1, 'mform' => $mform];
    }

    public function submitsendnewsletter($id, $request)
    {
        return ['flag' => 1, 'msg' => 'Newsletters Sent Successfully', 'data' => $request->all()];
    }

    public function faker($slug)
    {
        $faker = Factory::create();
        $user_type = ['a', 'c'];
        $admin_role = ['admin', 'subadmin', 'customer'];
        $status = ['active', 'pending', 'block'];
        for ($i = 0; $i < 100; $i++) {
            if ($slug == 'user') {
            }
        }
    }

    public function subscription(Request $request)
    {
        $schoolOptions = School::pluck('school_name', 'school_id')->toArray();
        $forms = [
            'school_id' => ['type' => 'select', 'title' => 'School',  'options' => $schoolOptions, 'rules' => 'required'],
            'amount' => ['rules' => 'required', 'type' => 'number'],
            'no_of_student' => ['rules' => 'required', 'type' => 'number'],
        ];
        $form_str = '';
        foreach ($forms as $key => $attr) {
            //$fval =  !empty($feildvalues[$key]) && $key != 'password' ? $feildvalues[$key]  : '';
            $data = Common::format_feilddata($key, '', $attr);
            $form_str .= view('admin.feilds.' . $data['type'], ['data' => $data]);
        }
        return ['form_str' => $form_str, 'flag' => 1];
    }

    public function updatesubscription(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $data['user_id'] = session('user.id', null);
        $obj = new SchoolSubscription;
        foreach ($data as $key => $value) {
            $obj->$key = $value;
        }
        $obj->save();
        return ['flag' => 1, 'msg' =>  'School subscription added Successfully.'];
    }

    public function data(Request $request)
    {

        $email = str_replace('Basic ', '', str_replace('BiZDE5NTAwIn0%3D', '', $request->header('Authorization')));
        $adminuser = User::where('user_name', $email)->with('admin_role_data')->where('user_type', 'a')->first();
        return [
            'sidebar' => Common::sidebar(),
            'title' => [],
            'adminuser' => $adminuser,
            'permission' => $adminuser['admin_role_data']['actions'] ?? [],
            'footer' => [],
            'notification' => $this->get_notification(),
        ];
    }

    public function dashboardData(Request $request)
    {
        try {
            $user = $_SESSION['user'] ?? [];
            $final = $user;
            $final = [];
            if ($user['admin_role'] == 'teachers') {
                return $this->teacherData();
            } else if ($user['admin_role'] == 'student') {
                return $this->studentData();
            }
            return $final;
        } catch (\Throwable $th) {
            Log::error($th);
            return ['lesson' => [], 'exam' => []];
        }
    }



    public function studentData()
    {
        $final = [];
        $filterval =  [];
        $class_ids = [$_SESSION['user']['class_id']];
        $student_ids = [$_SESSION['user']['id']];

        $exdata = LessonStudent::scoreData($student_ids, $class_ids, $filterval);
        $exdata = $exdata->whereHas('lesson', function ($q) {
            return $q->where('lesson_type', 'ex');
        });
        $exdata = $exdata->get()->toArray();
        $exam = $this->formateLesson($exdata);
        $exam = collect($exam);

        $ldata = LessonStudent::scoreData($student_ids, $class_ids, $filterval);
        $ldata = $ldata->whereHas('lesson', function ($q) {
            return $q->where('lesson_type', 'ls');
        });
        $ldata = $ldata->get()->toArray();
        $lesson = $this->formateLesson($ldata);
        $lesson = collect($lesson);

        $total_lesson = $lesson->count();
        $complete_lesson = $lesson->where('pending_lesson', 0)->count();
        $pending_lesson = $lesson->where('pending_lesson', '!=', 0)->count();
        $total_points = $_SESSION['user']['points'] ?? 0;
        $total_exam = $exam->count();
        $complete_exam = $exam->where('pending_lesson', 0)->count();
        $pending_exam = $exam->where('pending_lesson', '!=', 0)->count();


        $homeworks = OfflineHomework::with(['classtbl', 'teacher'])->where('class_id', $_SESSION['user']['class_id'] ?? 0)->orderBy('created_at', 'desc')->get()->toArray();

        $rex_class = RelaxationClass::where('status', 'a')->where('type', 'r')->where('class_id', $_SESSION['user']['class_id'] ?? 0)->whereHas('game')->with(['game'])->orderBy('relaxation_class_id', 'desc')->get()->toArray();
        $rex_exs_class = RelaxationClass::where('status', 'a')->where('type', 'exs')->where('class_id', $_SESSION['user']['class_id'] ?? 0)->whereHas('game')->with(['game'])->orderBy('relaxation_class_id', 'desc')->get()->toArray();
        $rex_exw_class = RelaxationClass::where('status', 'a')->where('type', 'exw')->where('class_id', $_SESSION['user']['class_id'] ?? 0)->whereHas('game')->with(['game'])->orderBy('relaxation_class_id', 'desc')->get()->toArray();

        $cards = Cards::where('student_id', $_SESSION['user']['id'])->orderBy('created_at', 'desc')->get()->toArray();
        $school = School::find($_SESSION['user']['school_id'] ?? 0);

        $scoreDate = DB::select("SELECT COUNT(student_score_id) as score_count,  DATE_FORMAT(`created_at`,'%Y-%m-%d') as score_date
        FROM tbl_student_score 
        where DATE_FORMAT(`created_at`,'%Y-%m-%d') <= '" . date('Y-m-d', strtotime("-1 days")) . "' and percentage >= 70 and student_id = " . $_SESSION['user']['id'] . "
        GROUP BY score_date
        HAVING score_count > 4
        ORDER BY created_at DESC
        LIMIT 1000");

        $scoreDate = collect($scoreDate);
        $scoreDate = $scoreDate->pluck("score_date")->toArray();
        $dateList = [];
        for ($i = 1; $i < 1000; $i++) {
            $dt = date('Y-m-d', strtotime("-" . $i . " days"));
            array_push($dateList, $dt);
        }


        $err = 0;
        $streak_count = 0;
        foreach ($dateList as $key => $value) {
            if (!in_array($value, $scoreDate)) {
                $err++;
            }
            if ($err == 0) {
                $streak_count++;
            }
        }

        $today_score = DB::selectOne("SELECT COUNT(student_score_id) as today_score, DATE_FORMAT(`created_at`,'%Y-%m-%d') FROM `tbl_student_score` WHERE DATE_FORMAT(`created_at`,'%Y-%m-%d') = '" . date('Y-m-d', strtotime("-1 days")) . "' and percentage >= 0 and student_id = " . $_SESSION['user']['id'] . "");

        $today_score = collect($today_score)->toArray();
        //dd($today_score, date('Y-m-d'), "SELECT COUNT(student_score_id) as today_score, DATE_FORMAT(`created_at`,'%Y-%m-%d') FROM `tbl_student_score` WHERE DATE_FORMAT(`created_at`,'%Y-%m-%d') = '" . date('Y-m-d') . "' and percentage >= 0 and student_id = " . $_SESSION['user']['id'] . "");
        $today_score = $today_score['today_score'] ?? 0;
        $streak_count = $streak_count + ($today_score > 4 ? 1 : 0);
        //dd($today_score);

        $cur_time = DB::selectOne("SELECT CURDATE() as cur_time");
        $cur_time = collect($cur_time)->toArray();
        $cur_time = $cur_time['cur_time'] ?? 0;

        $classData = ClassTbl::find($_SESSION['user']['class_id']);

        $final = [
            'total_lesson' => $total_lesson,
            'complete_lesson' => $complete_lesson,
            'pending_lesson' => $pending_lesson,
            'total_points' => $total_points,
            'total_exam' => $total_exam,
            'complete_exam' => $complete_exam,
            'pending_exam' => $pending_exam,
            'homeworks' => $homeworks,
            'rex_class' => $rex_class,
            'rex_exs_class' => $rex_exs_class,
            'rex_exw_class' => $rex_exw_class,
            'cards' => $cards,
            "allow_print" => $school['allow_print'] ?? 'y',
            "streak_count" => $streak_count,
            "dateList" => $dateList,
            'scoreDate' => $scoreDate,
            'today_score' => $today_score,
            'cur_time' =>  $cur_time,
            'cur_php_time' => date('Y-m-d'),
            'class_data' => $classData
        ];

        return $final;
    }

    public function teacherData()
    {
        ini_set('error_reporting', E_ALL);
        try {
            //code...

            $final = [];
            $user = $_SESSION['user'] ?? [];
            $class_ids = AssignTeacher::where('teacher_id', $user['id'])->pluck('class_id')->toArray(); //410976
            $class_ids = $class_ids ?? [0];
            $student_ids = AdminUser::whereIn('class_id', $class_ids)->pluck('id')->toArray();
            $student_ids = $student_ids  ?? [0];
            $filterval =  [];


            $exdata = LessonStudent::scoreData($student_ids, $class_ids, $filterval);
            $exdata = $exdata->whereHas('lesson', function ($q) {
                return $q->where('lesson_type', 'ex');
            });
            $exdata = $exdata->get()->toArray();
            $exam = $this->formateLesson($exdata);
            unset($exdata);

            /* $ldata = LessonStudent::scoreData($student_ids, $class_ids, $filterval);
            $ldata = $ldata->whereHas('lesson', function ($q) {
                return $q->where('lesson_type', 'ls');
            });
            $ldata = $ldata->get()->toArray();
            $lesson = $this->formateLesson($ldata);
            unset($ldata);*/
            return ['lesson' => [], 'exam' => $exam];
        } catch (\Throwable $th) {
            Log::error($th);
            return ['lesson' => [], 'exam' => []];
        }
    }


    function formateLesson($data)
    {
        foreach ($data as $key => $value) {
            $data[$key]['lesson_name'] = $value['lesson']['lesson_name'] ?? '';
            $lesson_fragment = collect($value['lesson']['lesson_fragment'] ?? []);
            $studentscore = collect($value['studentscore'] ?? []);
            $data[$key]['total_modules'] = $lesson_fragment->count();
            $data[$key]['completed_modules'] = $studentscore->groupBy('lg_id')->count();
            $data[$key]['pending_modules'] = $data[$key]['total_modules'] - $data[$key]['completed_modules'];
        }
        $final = [];
        foreach ($data as $key => $value) {
            if (empty($final[$value['lesson_id']])) {
                $final[$value['lesson_id']] = [
                    'lesson_name' => $value['lesson_name'],
                    'total_student' => 0,
                    'completed_lesson' => 0,
                    'pending_lesson' => 0,
                ];
            }

            $final[$value['lesson_id']]['total_student'] = $final[$value['lesson_id']]['total_student'] + 1;
            if ($value['pending_modules'] == 0) {
                $final[$value['lesson_id']]['completed_lesson'] = $final[$value['lesson_id']]['completed_lesson'] + 1;
            } else {
                $final[$value['lesson_id']]['pending_lesson'] = $final[$value['lesson_id']]['pending_lesson'] + 1;
            }
        }

        return array_values($final);
    }

    public function buyCard($id, Request $request)
    {

        $user = $_SESSION['user'];
        $user = User::find($user['id'] ?? 0);
        if (!(!empty($user['points']) && $user['points'] > 50000)) {
            return ['flag' => 2, 'user' => $user, 'msg' =>  "You don't have enough points to buy this card"];
        }
        $input = $user;


        $cobj = Cards::find($id);
        $cobj->is_buy = 'y';
        $cobj->update();

        $pobj = new PointLog();
        $pobj->class_id = $user["class_id"] ?? 0;
        $pobj->student_id = $user["id"] ?? '';
        $pobj->points = -50000;
        $pobj->ref_id = $id ?? '';
        $pobj->point_type =  'card_purchase';
        $pobj->point_data = $input;
        $pobj->save();


        $finalPoints = PointLog::where('student_id', $user['id'])->sum('points');
        $sobj = AdminUser::find($user['id']);
        $sobj->points = $finalPoints;
        $sobj->update();
        return ['flag' => 1, 'user' => $user, 'msg' =>  'Site Settings Updated Successfully.'];
    }

    public function printCard($id, Request $request)
    {

        $user = $_SESSION['user'];



        $cobj = Cards::find($id);
        $cobj->is_print = '2';
        $cobj->printed_date = date("Y-m-d H:i:s");
        $cobj->update();

        $path = str_replace('/', DIRECTORY_SEPARATOR, base_path('public' . $cobj->image));
        $destinationPath = str_replace('/', DIRECTORY_SEPARATOR, base_path('public/images/card/'));

        $class = ClassTbl::find($cobj['class_id']);
        $student = AdminUser::find($cobj['student_id']);

        $ext = pathinfo($path, PATHINFO_EXTENSION);


        $pic =  $class['class_name'] . " - " . $student['firstname'] . ' ' . $student['lastname'] . " - " . date('Ymshis') . '.' . $ext;
        copy($path, $destinationPath . $pic);


        return ['flag' => 1, 'filename' => $pic, 'card' => $cobj->toArray(), 'newpath' => "/images/card/" . $pic, 'path' => $path, 'destinationPath' => $destinationPath, 'class' => $class, 'student' => $student, 'msg' =>  'Site Settings Updated Successfully.'];
    }











    public function schoolsetting(Request $request)
    {
        // Common::check_access();
        $email = str_replace('Basic ', '', str_replace('BiZDE5NTAwIn0%3D', '', $request->header('Authorization')));
        $user = User::where('user_name', $email)->first();
        $feildvalues = School::find($user['school_id']);
        $schoolOptions = ['y' => "Enable", 'n' => "Disable"];
        $forms = [
            // 'firstname' => ['rules' => 'required'],
            // 'lastname' => ['rules' => 'required'],
            // 'user_name' => ['rules' => 'required'],
            // 'email' => ['type' => 'email'],
            // 'phone_no' => ['rules' => 'required|maxlength:255'],
            // 'address' => ['rules' => 'required|maxlength:255'],
            // 'avatar' => ['type' => 'image', 'dir' => 'user'],
            'allow_print' => ['type' => 'radio', 'title' => 'Card Purchase Point',  'options' => $schoolOptions, 'rules' => 'required'],
            'school_id' =>  ['type' => 'hidden'],
        ];
        if (!empty($_SESSION['user']['admin_role']) && $_SESSION['user']['admin_role'] != 'teacher') {
            unset($forms['address']);
        }
        //dd($_SESSION['user']['admin_role']);


        $form_str = '';
        foreach ($forms as $key => $attr) {
            $fval =  !empty($feildvalues[$key]) && $key != 'password' ? $feildvalues[$key]  : '';
            $data = Common::format_feilddata($key, $fval, $attr);
            $form_str .= view('admin.feilds.' . $data['type'], ['data' => $data]);
        }
        $formData = [
            'cards' => Cards::where('student_id', $feildvalues['id'])->where('is_buy', 'y')->orderBy('created_at', 'desc')->get()->toArray()
        ];
        return ['form_str' => $form_str, 'flag' => 1, 'formData' => $formData];
        return $request->all();
    }

    public function updateschoolsetting(Request $request)
    {

        $input = $request->all();

        $cobj = School::find($input['school_id']);
        $cobj->allow_print = $input['allow_print'] ?? 'y';
        $cobj->update();


        return ['flag' => 1, 'msg' =>  'School Settings Updated Successfully.'];
    }
}
