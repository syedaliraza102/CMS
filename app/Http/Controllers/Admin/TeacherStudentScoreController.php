<?php

namespace App\Http\Controllers\Admin;

use App\Models\ClassTbl;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Traits\AdminCrud;
use \App\Traits\Base;

class TeacherStudentScoreController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = ClassTbl::class;
        $this->mTitle = 'Class';
        $this->slug = 'teacherstudentscore';
        $this->pk = 'class_id';

        $this->gridCol = ['class_id' => 'Id',  'class_name' => 'Class Name', "school_id" => 'School',   'created_at' => 'Created At'];
        $this->viewCol = ['class_id', 'class_name', 'principal_name',   'created_at' => 'Created At'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->schoolOptions = School::pluck('school_name', 'school_id')->toArray();
        $this->forms = [
            'class_name' => ['rules' => 'required|maxlength:255'],
            //'school_id' => ['type' => 'select', 'options' => $this->schoolOptions, 'rules' => 'required'],
            //'status' => ['type' => 'radio', 'options' => $this->statusoptions,  'default' => 'a'],
        ];
        $this->filters = [
            'class_name' => ["width" => 4, 'title' => 'Class Name', 'placeholder' => 'Seac'],
            //'school_id' => ['type' => 'select', 'title' => 'School', "width" => 4, 'options' => $this->schoolOptions, 'operator' => '='],
            //'status' => ['type' => 'select', "width" => 4, 'options' => $this->statusoptions, 'operator' => '=']
        ];
        $this->user = $_SESSION['user'] ?? [];
        $this->school_id = $this->user['school_id'] ?? null;
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            $data[$key]['status'] = $this->gridswitch('status', 'a', 'd', $value);
            $data[$key]['school_id'] = $this->schoolOptions[$value['school_id']] ?? '-';
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
        $data = $this->model::whereRaw($this->get_where($request, $gridCol));
        //$data = $this->model::whereRaw('1 = 1');
        $data = $data->where('school_id', $this->school_id);
        $request['sortby'] = $request['sortby'] == 'id' ? 'class_id' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }
}
