<?php

namespace App\Http\Controllers\Admin;

use App\Models\MultiplayerRoom;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Traits\AdminCrud;
use \App\Traits\Base;

use Illuminate\Http\Request;
use \App\Common;
use App\Models\AdminUser;
use App\Models\ClassTbl;
use App\Models\Fragment;

class MultiplayerRoomController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = MultiplayerRoom::class;
        $this->mTitle = 'MultiplayerRoom';
        $this->slug = 'multiplayerroom';
        $this->pk = 'room_id';
        $this->role = '';
        $this->gridCol = ['room_id' => 'id',  'room_name' => 'Room Name', 'fragment_id' => 'Lesson Block', 'class_id' => 'Class',   'created_at' => 'Created At'];
        $this->viewCol = ['room_id', 'room_name', 'principal_name',   'created_at' => 'Created At'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->schoolOptions = School::pluck('school_name', 'school_id')->toArray();
        $this->forms = [
            'room_name' => ['rules' => 'required|maxlength:255'],
        ];
        $this->filters = [
            'room_name' => ["width" => 12, 'title' => 'Room Name', 'placeholder' => 'Search Level'],
            //'status' => ['type' => 'select', "width" => 4, 'options' => $this->statusoptions, 'operator' => '=']
        ];

        $this->user = $_SESSION['user'] ?? [];
        if(isset($_SESSION['user']['admin_role'])) {
            $this->role = $_SESSION['user']['admin_role'];
        } 
        $this->school_id = $this->user['school_id'] ?? null;
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            //$data[$key]['status'] = $this->gridswitch('status', 'a', 'd', $value);
            $data[$key]['actions'] = $this->preaction($value);
            $data[$key]['fragment_id'] = $value['fragment']['fragment_name'] ?? '-';
            $data[$key]['class_id'] = $value['class_tbl']['class_name'] ?? '-';
        }
        return $data;
    }

    public function preaction($value)
    {
        $up_icon = url('public/icons/up.png');
        $down_icon = url('public/icons/down.png');
        $copy_icon = url('public/icons/17.png');
        $edit_icon = url('public/icons/18.png');
        $delete_icon = url('public/icons/19.png');

        if ($this->role == 'student') {
            // return '<a href="/game/neuro-class/index.html?room=' . $value['room_name'] . '&room_id=' . $value['room_id'] . '&fragment_id=' . $value['fragment_id'] . '&user_id=' . $this->user['id'] . '&user_name=' . $this->user['user_name'] . '&role=student" class="btn btn-xs bg-primary text-white" title="Join Room"><i class="fa fa-external-link-square" aria-hidden="true"></i>
            // </a>';
            return '<a class="btn btn-xs btn-primary" title="Deactive Room" ng-click="addaction($event,' . "'check_disable'" . ',' . $value[$this->getpk()] . ')"> <i class="fa fa-external-link-square" aria-hidden="true"></i> </a>';
        } else {
               $action = '<a class="btn btn-xs text-white btn-success" title="Edit" ng-click="edit(' . $value[$this->getpk()] . ')"> <i class="fa fa-pencil" > </i> </a><a class="btn btn-xs text-white btn-danger" title="Delete" ng-click="delete(' . $value[$this->getpk()] . ')"> <i class="fa fa-trash" > </i> </a>';

            if ($value['status'] == 'a') {
                $link = '<a href="/game/neuro-class/index.html?room=' . $value['room_name'] . '&class_id=' . $value['class_id'] . '&room_id=' . $value['room_id'] . '&fragment_id=' . $value['fragment_id'] . '&user_id=' . $this->user['id'] . '&user_name=' . $this->user['user_name'] . '&role=teacher" class="btn btn-xs bg-primary text-white" title="Join Room"><i class="fa fa-external-link-square" aria-hidden="true"></i></a>';
                return  $link . '<a class="btn btn-xs btn-danger" style="width: auto !important;
                padding: 0px 10px !important;" title="Hide Room" ng-click="addaction($event,' . "'disable'" . ',' . $value[$this->getpk()] . ')"> Deactive Room </a>' . $action;
            } else {

                return '<a class="btn btn-xs btn-primary" style="width: auto !important;
                padding: 0px 10px !important;" title="Show Room" ng-click="addaction($event,' . "'enable'" . ',' . $value[$this->getpk()] . ')"> Active Room </a>' . $action;
            }
        }
        return '';
    }

    public function bulkaction_($slug, $request)
    {
        //return $slug;
        if ($slug == 'disable') {
            $obj = $this->model::find($request['id']);
            $obj->status = 'd';
            if ($obj->update()) {
                return ['flag' => 1, 'msg' =>  'Room Deactived Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        } else if ($slug == 'enable') {
            $obj = $this->model::find($request['id']);
            $obj->status = 'a';
            if ($obj->update()) {
                return ['flag' => 1, 'msg' =>  'Room Deactived Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        } else if ($slug == 'check_disable') {
            $obj = $this->model::find($request['id']);

            if ($obj->status == 'a') {
                $value = $obj->toArray();
                $link = '/game/neuro-class/index.html?room=' . $value['room_name'] . '&class_id=' . $value['class_id'] . '&room_id=' . $value['room_id'] . '&fragment_id=' . $value['fragment_id'] . '&total_points=' . ($this->user['points'] ?? 0) . '&user_id=' . $this->user['id'] . '&user_name=' . $this->user['user_name'] . '&role=student';

                return ['flag' => 1, 'data' => ['link' => $link], 'msg' =>  'Room verified Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss This Room already closed'];
            }
        }
    }

    public function prepare_insert($data)
    {
        unset($data['_token']);
        unset($data['id']);
        $data['school_id'] = $this->school_id;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function prepare_update($data)
    {
        $data['room_id'] = $data['id'];
        $data['school_id'] = $this->school_id;
        unset($data['_token']);
        unset($data['id']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }


    public function get_griddata($request, $gridCol, $mperm)
    {
        $this->user = $_SESSION['user'] ?? [];
        if(isset($_SESSION['user']['admin_role'])) {
            $this->role = $_SESSION['user']['admin_role'];
        } 
        $data = $this->model::whereRaw($this->get_where($request, $gridCol));
        if(isset($request->selected_class) && $request->selected_class!=0) {
            $data->where('class_id', $request->selected_class);
        } 
       
        if ($this->role == 'student') {
            $data = $data->whereRaw('FIND_IN_SET(' . $this->user['id'] . ',student_ids) ')->where('status', 'a');
        } else {
            $data = $data->where('teacher_id', $this->user['id']);
        }
        $data = $data->whereHas('fragment')->with(['fragment', 'classTbl'])->where('school_id', $this->school_id);
        $request['sortby'] = $request['sortby'] == 'id' ? 'room_id' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
       
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }


    public function griddata(Request $request)
    {
        $mperm = Common::user_roles();
        $gridCol = $this->gridCol($request);

        return [
            'flag' => 1,
            'gridCol' => $gridCol,
            'griddata' => $this->get_griddata($request, $gridCol, $mperm),
            'bulkactions' => $this->bulkactions(),
            'filtersinputs' => $this->filtersinputs($request),
            'can_add' => $this->can_add($mperm),
            'can_bulk' => $this->can_bulk($mperm),
            'can_export' => $this->can_export($mperm),
            'admin_role' => session('admin_role'),

        ];
    }

    public function formdata(Request $request)
    {

        $classList = ClassTbl::where('school_id', $this->school_id)->get()->toArray();
        $studentList = AdminUser::where('admin_role', 'student')->where('school_id', $this->school_id)->get()->toArray();
        $fragmentList = Fragment::where('school_id', $this->school_id)->where(function ($query) {
            $query->where('package_ids', 'like', '%"2"%')
                ->where('package_ids', 'like', '%"5"%')
                ->where('package_ids', 'like', '%"9"%');
        })->get()->toArray();
        // ['qa', 'vocabulary', 'english_sentences']

        return [
            'flag' => 1,
            'classList' => $classList,
            'fragmentList' => $fragmentList,
            'studentList' => $studentList,
            //'get_where' => $this->get_where($request, $gridCol)
        ];
    }



    public function saveroom(Request $request)
    {
        //Common::check_access('admin.' . $this->slug . '.add');
        $data = $this->prepare_insert($request->all());

        $err = $this->checkCrud($data);
        if (!empty($err)) {
            return ['flag' => 2, 'msg' => $err, 'data' => $err];
        }

        $data['teacher_id'] = $this->user['id'];

        $obj = new $this->model;
        foreach ($data as $key => $value) {
            $obj->$key = $value;
        }
        $obj->status = 'd';

        if ($obj->save()) {
            $value =  $obj->toArray();
            if ($this->role != 'student') {
                $value['link'] = '/game/neuro-class/index.html?room=' . $value['room_name'] . '&class_id=' . $value['class_id'] . '&room_id=' . $value['room_id'] . '&fragment_id=' . $value['fragment_id'] . '&user_id=' . $this->user['id'] . '&user_name=' . $this->user['user_name'] . '&role=teacher';
            }
            $value['role'] = $this->role;
            return ['flag' => 1, 'msg' => $this->mTitle . ' inserted Successfully.', 'data' => $value];
        } else {
            return ['flag' => 2, 'msg' => ' Oops! Something went worng.', 'data' => []];
        }
        return $data;
    }
    public function getData ($id) {
        $getData = $this->model::where('room_id', $id)->first();
        $classList = ClassTbl::where('school_id', $this->school_id)->get()->toArray();
        $studentList = AdminUser::where('admin_role', 'student')->where('school_id', $this->school_id)->get()->toArray();
        $fragmentList = Fragment::where('school_id', $this->school_id)->where(function ($query) {
            $query->where('package_ids', 'like', '%"2"%')
                ->where('package_ids', 'like', '%"5"%')
                ->where('package_ids', 'like', '%"9"%');
        })->get()->toArray();
        // ['qa', 'vocabulary', 'english_sentences']

    

        return response()->json([
            'flag' => 1,
            'classList' => $classList,
            'fragmentList' => $fragmentList,
            'studentList' => $studentList,
            'data' => $getData,
        ]);
    }
}
