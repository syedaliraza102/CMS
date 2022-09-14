<?php

namespace App\Http\Controllers\Admin;

use App\Models\OfflineHomework;
use App\Http\Controllers\Controller;
use App\Models\ClassTbl;
use App\Models\AdminUser;
use App\Models\AssignTeacher;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use App\User;

class OfflineHomeworkController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = OfflineHomework::class;
        $this->mTitle = 'Offline Homework';
        $this->slug = 'offlinehomework';
        $this->pk = 'homework_id';


        $this->gridCol = ['homework_id' => 'Id', 'homework_image' => 'Image',   "class_id" => 'Class',    'created_at' => 'Created At'];
        $this->viewCol = ['homework_id' => 'Id', 'homework_image' => 'Image',  "class_id" => 'Class',    'created_at' => 'Created At'];


        $this->user = $_SESSION['user'] ?? [];
        $this->school_id = $this->user['school_id'] ?? null;

        $this->assignClasses = AssignTeacher::where('teacher_id', $_SESSION['user']['id'] ?? 0)->pluck('class_id')->toArray();
        $this->assignClassesIds = $this->assignClasses ?? [0];

        $this->classOptions = ClassTbl::whereIn('class_id', $this->assignClassesIds)->pluck('class_name', 'class_id')->toArray();
        //dd($this->assignClasses, $this->assignClassesIds, $this->classOptions);
        $this->teacherOptions = AdminUser::where('school_id', $this->school_id)->where('admin_role', 'teachers')->pluck('user_name', 'id')->toArray();
        $this->forms = [
            'class_id' => ['type' => 'select', 'title' => 'Class',  'options' => $this->classOptions, 'rules' => 'required'],
            'homework_image' => ['title' => __('title.game_sort') . ' Image', 'type' => 'image', 'dir' => 'user'],
        ];
        $this->filters = [
            'class_id' => ['type' => 'select', 'cond' => 'c', 'title' => 'Class', "width" => 12, 'options' => $this->classOptions, 'operator' => '='],
        ];
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            $data[$key]['class_id'] = $this->classOptions[$value['class_id'] ?? ''] ?? '-';
            //$data[$key]['teacher_id'] = $this->teacherOptions[$value['teacher_id'] ?? ''] ?? '-';
            $data[$key]['homework_image'] = $this->gridimage($value['homework_image']);
            $data[$key]['actions'] = $this->action_formate($value, $mperm);
        }
        return $data;
    }

    public function get_griddata($request, $gridCol, $mperm)
    {
        $data = $this->model::whereRaw($this->get_where($request, $gridCol));
        //$data = $this->model::whereRaw('1 = 1');

        $classIds = array_keys($this->classOptions);
        if(isset($request->class_id) && $request->class_id >0) {
           $data = $data->where('class_id', $request->class_id);
        } else {
           $data = $data->whereIn('class_id', $classIds ?? [0]);
        }

        $data = $data->where('teacher_id', $_SESSION['user']['id'] ?? 0);

        $request['sortby'] = $request['sortby'] == 'id' ? 'homework_id' : $request['sortby'];
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
        $data['teacher_id'] = $_SESSION['user']['id'] ?? 0;
        return $data;
    }

    public function prepare_update($data)
    {
        $data['homework_id'] = $data['id'];
        $data['teacher_id'] = $_SESSION['user']['id'] ?? 0;
        unset($data['_token']);
        unset($data['id']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }
}
