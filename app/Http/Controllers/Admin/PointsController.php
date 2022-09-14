<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Http\Controllers\Controller;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use \App\Common;
use App\Models\AdminUser;
use App\Models\AssignTeacher;
use App\Models\ClassTbl;
use App\Models\PointLog;

class PointsController extends Controller
{

    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = AdminUser::class;
        $this->mTitle = 'Points';
        $this->slug = 'points';
        $this->pk = 'id';

        $this->gridCol = ['id', 'class_name' => 'Class',  'user_name' => 'Student',  'points' => 'Points',   'created_at' => 'Created At'];
        $this->viewCol = [];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->forms = [];


        $this->user = $_SESSION['user'] ?? [];
        $this->class_ids = AssignTeacher::where('teacher_id', $this->user['id'])->pluck('class_id')->toArray(); //410976
        $this->class_ids = $this->class_ids ?? [0];
        $this->classOptions = ClassTbl::whereIn('class_id', $this->class_ids)->pluck('class_name', 'class_id')->toArray();
        $this->studentOptions = AdminUser::whereIn('class_id', $this->class_ids)->pluck('user_name', 'id')->toArray();
        $this->filters = [
            'class_id' => ['type' => 'select', "width" => 4, 'title' => 'Class', 'options' => $this->classOptions],
            'student_id' => ['type' => 'select', "width" => 8, 'title' => 'Student', 'options' => $this->studentOptions],
        ];
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            $data[$key]['class_name'] = $value['classtbl']['class_name'] ?? '-';
            $actions = '<a class="btn btn-xs text-white btn-success" title="Change Points" ng-click="changePoints(' . $value[$this->getpk()] . ')"> <i class="fa fa-clone" aria-hidden="true"></i> </a>';
            $data[$key]['actions'] = $actions;
        }
        return $data;
    }

    public function prepare_insert($data)
    {
        dd($data);
        unset($data['_token']);
        unset($data['id']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function prepare_update($data)
    {

        $data['id'] = $data['id'];
        unset($data['_token']);
        unset($data['id']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }


    public function get_griddata($request, $gridCol, $mperm)
    {
        $student_ids =  array_keys($this->studentOptions);
        $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];
        if (!empty($filterval['filter_student_id']) && $filterval['filter_student_id'] != 'all') {
            $student_ids =  [$filterval['filter_student_id']];
        }

        $data = $this->model::with(['classtbl']);
        if (!empty($filterval['filter_class_id']) && $filterval['filter_class_id'] != 'all') {
            $data = $data->where('class_id', $filterval['filter_class_id']);
        }
        $data = $data->where('admin_role', 'student')->whereIn('id', $student_ids);
        
        if(isset($request->class_id) && $request->class_id >0) {
            $class_id =  $request->class_id;
            $data = $data->whereHas('classtbl', function ($q) use($class_id){
                return $q->where('class_id', $class_id);
            });
        }

        $request['sortby'] = $request['sortby'] == 'id' ? 'id' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }




    public function can_delete($value, $mperm)
    {
        return false;
    }

    public function can_view($value, $mperm)
    {
        return false;
    }

    public function gridCol($request)
    {
        $isort = $this->get_ignoresort();
        $gridCol = [];
        foreach ($this->getgridCol() as $key => $value) {
            $slug = is_numeric($key) ? $value : $key;
            $title = is_numeric($key) ? Common::format_colname($value) : $value;
            $scalss = !in_array($slug, $isort) ? 'is_sort' : '';
            $scalss .= $scalss == 'is_sort' && !empty($request['sortby']) && !empty($request['sortdir']) && $request['sortby'] == $slug ? '  ' . $request['sortdir'] . '_sort' : '';
            array_push($gridCol, ['slug' => $slug, 'title' =>  $title, 'sclass' => $scalss]);
        }
        return $gridCol;
    }


    public function pointsUpdate(Request $request)
    {
        $input = $request->all();
        $obj = $this->model::find($input['id']);
        if (empty($obj)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.', 'data' => []];
        }


        $pobj = new PointLog();
        $pobj->class_id = $input['class_id'] ?? '';
        $pobj->student_id = $input['id'] ?? '';
        $pobj->points = $input['cPoints'] ?? 0;
        $pobj->point_type =  'bp';
        $pobj->point_data = $input;
        $pobj->created_at = date('Y-m-d H:i:s');
        $pobj->updated_at = date('Y-m-d H:i:s');
        $pobj->save();

        $finalPoints = PointLog::where('student_id', $input['id'])->sum('points');
        $obj->points = $finalPoints;

        if ($obj->update()) {
            return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully.', 'data' => $obj->toArray()];
        } else {
            return ['flag' => 2, 'msg' => ' Oops! Something went worng.', 'data' => []];
        }
        return;
    }
}
