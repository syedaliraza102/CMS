<?php

namespace App\Http\Controllers\Admin;

use App\Common;
use App\Models\ClassTbl;
use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\AssignTeacher;
use App\Models\Cards;
use App\Models\School;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use Illuminate\Http\Request;

class CardsController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = Cards::class;
        $this->mTitle = 'cards';
        $this->slug = 'cards';
        $this->pk = 'card_id';

        $this->gridCol = ['class_id',  'class_name' => 'Class Name', "school_id" => 'School',   'created_at' => 'Created At'];
        $this->viewCol = ['class_id', 'class_name', 'principal_name',   'created_at' => 'Created At'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->printoptions = ['1' => 'Pending', '2' => 'Printed'];
        $this->schoolOptions = School::pluck('school_name', 'school_id')->toArray();
        
        $this->user = $_SESSION['user'] ?? [];
        $this->school_id = $this->user['school_id'] ?? null;
        $this->schoolData = School::find($this->school_id);

        if(!isset($this->user['id'])) {
            $this->user['id'] = 0;
        }
        $this->class_ids = AssignTeacher::where('teacher_id', $this->user['id'])->pluck('class_id')->toArray(); //410976
        $this->class_ids = $this->class_ids ?? [0];
        $this->studentOptions = AdminUser::whereIn('class_id', $this->class_ids)->pluck('user_name', 'id')->toArray();
        $this->classOptions = ClassTbl::whereIn('class_id', $this->class_ids)->pluck('class_name', 'class_id')->toArray();
        $this->forms = [
            'class_name' => ['rules' => 'required|maxlength:255'],
            //'school_id' => ['type' => 'select', 'options' => $this->schoolOptions, 'rules' => 'required'],
            //'status' => ['type' => 'radio', 'options' => $this->statusoptions,  'default' => 'a'],
        ];
        $this->filters = [
            'class_id' => ['type' => 'select', "width" => 3, 'title' => 'Class', 'options' => $this->classOptions],
            'student_id' => ['type' => 'select', "width" => 3, 'title' => 'Student', 'options' => $this->studentOptions],
            'is_print' => ['type' => 'select', "width" => 3, 'title' => 'Print Status', 'options' => $this->printoptions],
            //'status' => ['type' => 'select', 'title' => 'Card Type', "width" => 4, 'options' => $this->statusoptions, 'operator' => '=']
        ];
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            $data[$key]['status'] = $this->gridswitch('status', 'a', 'd', $value);
            //$data[$key]['school_id'] = $this->schoolOptions[$value['school_id']] ?? '-';
            $data[$key]['actions'] = $this->action_formate($value, $mperm);
        }
        return $data;
    }

    public function prepare_insert($data)
    {
        unset($data['_token']);
        unset($data['id']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['school_id'] = $this->school_id;
        return $data;
    }

    public function prepare_update($data)
    {
        $data['class_id'] = $data['id'];
        $data['school_id'] = $this->school_id;
        unset($data['_token']);
        unset($data['id']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }


    public function get_griddata($request, $gridCol, $mperm)
    {
        $this->user = $_SESSION['user'] ?? [];
        $this->school_id = $this->user['school_id'] ?? null;
        $this->schoolData = School::find($this->school_id);


        //$data = $this->model::whereRaw($this->get_where($request, $gridCol));
        $data = $this->model::whereRaw('1 = 1');

        $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];

        $noData = 0;
        if ((!empty($filterval['filter_student_id']) && $filterval['filter_student_id'] != 'all')) {
            $data = $data->where('student_id', $filterval['filter_student_id']);
        } else {
            $noData++;
        }

        if ((!empty($filterval['filter_class_id']) && $filterval['filter_class_id'] != 'all')) {
            $data = $data->where('class_id', $filterval['filter_class_id']);
        } else {
            $noData++;
        }

        if ($noData == 2) {
            $data = $data->where('card_id', 0);
        }

        if ((!empty($filterval['filter_is_print']) && $filterval['filter_is_print'] != 'all')) {
            $data = $data->where('is_print', $filterval['filter_is_print']);
        }


        if (!empty($this->schoolData['allow_print']) && $this->schoolData['allow_print'] == 'y') {
            $data = $data->where('is_buy', 'y');
        }

        $request['sortby'] = $request['sortby'] == 'id' ? 'card_id' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], 'desc');
        }
        $data = $data->with(['classtbl']);
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }


    public function griddata(Request $request)
    {

        //return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
        $mperm = Common::user_roles();
        $gridCol = $this->gridCol($request);
        $err = 2;
        $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];

        if (!(!empty($filterval['filter_student_id']) && $filterval['filter_student_id'] != 'all') && !(!empty($filterval['filter_class_id']) && $filterval['filter_class_id'] != 'all')) {
            $err = 1;
        }

        // if (!(!empty($filterval['filter_class_id']) && $filterval['filter_class_id'] != 'all')) {
        //     $err = 1;
        // }

        $school = School::find($this->school_id ?? 0);

        $gdata = $this->get_griddata($request, $gridCol, $mperm);
        return [
            'flag' => 1,
            'gridCol' => $gridCol,
            'griddata' => $gdata,
            'bulkactions' => $this->bulkactions(),
            'filtersinputs' => $this->filtersinputs($request),
            'can_add' => $this->can_add($mperm),
            'can_bulk' => $this->can_bulk($mperm),
            'can_export' => $this->can_export($mperm),
            'admin_role' => session('admin_role'),
            "error" =>   $err,
            "allow_card" => $school['allow_print'] ?? 'y',
            'msg' => 'Please select Student and Class',
            'filterval' => $filterval
            //'get_where' => $this->get_where($request, $gridCol)
        ];
    }
}
