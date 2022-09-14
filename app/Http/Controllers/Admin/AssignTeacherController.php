<?php

namespace App\Http\Controllers\Admin;

use App\Models\AssignTeacher;
use App\Http\Controllers\Controller;
use App\Models\ClassTbl;
use App\Models\AdminUser;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use App\User;

class AssignTeacherController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = AssignTeacher::class;
        $this->mTitle = 'Assign Teacher';
        $this->slug = 'assignteacher';
        $this->pk = 'teacher_assign_id';

        $this->gridCol = ['teacher_assign_id' => 'Id',  "class_id" => 'Class', "teacher_id" => 'Teacher',   'created_at' => 'Created At'];
        $this->viewCol = ['teacher_assign_id' => 'Id', "class_id" => 'Class', "teacher_id" => 'Teacher',   'created_at' => 'Created At'];


        $this->user = $_SESSION['user'] ?? [];
        $this->school_id = $this->user['school_id'] ?? null;

        $this->classOptions = ClassTbl::where('school_id', $this->school_id)->pluck('class_name', 'class_id')->toArray();
        $this->teacherOptions = AdminUser::where('school_id', $this->school_id)->where('admin_role', 'teachers')->pluck('user_name', 'id')->toArray();
        $this->forms = [
            'class_id' => ['type' => 'select', 'title' => 'Class',  'options' => $this->classOptions, 'rules' => 'required'],
            'teacher_id' => ['type' => 'select', 'title' => 'Teacher',  'options' => $this->teacherOptions, 'rules' => 'required'],

        ];
        $this->filters = [
            'class_id' => ['type' => 'select', 'cond' => 'c', 'title' => 'Class', "width" => 4, 'options' => $this->classOptions, 'operator' => '='],
            'teacher_id' => ['type' => 'select', 'cond' => 'c', 'title' => 'Teacher', "width" => 4, 'options' => $this->teacherOptions, 'operator' => '='],
        ];
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            $data[$key]['class_id'] = $this->classOptions[$value['class_id'] ?? ''] ?? '-';
            $data[$key]['teacher_id'] = $this->teacherOptions[$value['teacher_id'] ?? ''] ?? '-';
            $data[$key]['actions'] = $this->action_formate($value, $mperm);
        }
        return $data;
    }

    public function get_griddata($request, $gridCol, $mperm)
    {
        $data = $this->model::whereRaw($this->get_where($request, $gridCol));
        //$data = $this->model::whereRaw('1 = 1');

        $classIds = array_keys($this->classOptions);
        $teacherIds = array_keys($this->teacherOptions);

        $data = $data->whereIn('class_id', $classIds ?? [0]);
        $data = $data->whereIn('teacher_id', $teacherIds ?? [0]);

        $request['sortby'] = $request['sortby'] == 'id' ? 'teacher_assign_id' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }


    public function prepare_insert($data)
    {
        unset($data['_token']);
        unset($data['id']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function prepare_update($data)
    {
        $data['teacher_assign_id'] = $data['id'];
        unset($data['_token']);
        unset($data['id']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }
}
