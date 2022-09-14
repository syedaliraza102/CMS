<?php

namespace App\Http\Controllers\Admin;

use App\Models\RelaxationData;
use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\AssignTeacher;
use App\Models\ClassTbl;
use App\Models\Game;
use App\Models\School;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use \App\Common;
use App\Models\RelaxationClass;
use Illuminate\Http\Request;

class RelaxationDataController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = RelaxationData::class;
        $this->mTitle = 'Relaxation Leader Board';
        $this->slug = 'relaxationdata';
        $this->pk = 'relaxation_data_id';

        $this->gridCol = ['relaxation_data_id' => 'Rank',  'student_id' => 'Student', 'score', 'created_at' => 'Created At'];
        $this->viewCol = ['relaxation_data_id', 'student_id', 'principal_name',   'created_at' => 'Created At'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->schoolOptions = School::pluck('school_name', 'school_id')->toArray();
        $this->forms = [
            'level_name' => ['rules' => 'required|maxlength:255'],
        ];
        $this->user = $_SESSION['user'] ?? [];

        //$this->studentOptions = AdminUser::whereIn('class_id', $this->class_ids)->pluck('user_name', 'id')->toArray();

        $whCond = ' 1 = 1 ';

        if ($this->user['admin_role'] == 'student') {
            //$whCond = ' student_id =  ';
        }

        $this->class_ids =  RelaxationData::pluck('class_id')->toArray(); //410976
        $this->class_ids = $this->class_ids ?? [0];
        $this->classOptions = ClassTbl::whereIn('class_id', $this->class_ids)->pluck('class_name', 'class_id')->toArray();


        // $this->game_ids = RelaxationData::pluck('game_id')->toArray(); //410976
        // $this->game_ids = $this->game_ids ?? [0];
        $this->gameOptions = Game::pluck('game_name', 'game_id')->toArray();


        $this->limitOptions = [
            10 => 10,
            20 => 20,
            50 => 50,
        ];
        $this->role = $_SESSION['user']['admin_role'];

        if ($this->role != 'student') {
            $this->assignClasses = AssignTeacher::where('teacher_id', $_SESSION['user']['id'] ?? 0)->pluck('class_id')->toArray();
            $this->assignClassesIds = $this->assignClasses ?? [0];
            $rex_class = RelaxationClass::where('status', 'a')->whereIn('type', ['r', 'exs', 'exw'])->whereIn('class_id', $this->assignClassesIds)->whereHas('game')->with(['game'])->orderBy('relaxation_class_id', 'desc')->pluck('game_id')->toArray();
            $rex_class = $rex_class ?? [0];
            $this->gameOptions = Game::whereIn('game_id', $rex_class)->pluck('game_name', 'game_id')->toArray();
            $this->filters = [
                'class_id' => ['type' => 'select', "width" => 4, 'title' => 'Class', 'options' => $this->classOptions],
                'game_id' => ['type' => 'select', "width" => 4, 'title' => 'Game', 'options' => $this->gameOptions],
                'limit_new' => ['type' => 'select', "width" => 4, 'title' => 'Top Records', 'options' => $this->limitOptions, 'default' => 20],
            ];
        } else {
            $rex_class = RelaxationClass::where('status', 'a')->whereIn('type', ['r', 'exs', 'exw'])->where('class_id', $_SESSION['user']['class_id'] ?? 0)->whereHas('game')->with(['game'])->orderBy('relaxation_class_id', 'desc')->pluck('game_id')->toArray();
            $rex_class = $rex_class ?? [0];
            $this->gameOptions = Game::whereIn('game_id', $rex_class)->pluck('game_name', 'game_id')->toArray();
            $this->filters = [
                //'class_id' => ['type' => 'select', "width" => 4, 'title' => 'Class', 'options' => $this->classOptions],
                'game_id' => ['type' => 'select', "width" => 4, 'title' => 'Game', 'options' => $this->gameOptions],
                'limit_new' => ['type' => 'select', "width" => 4, 'title' => 'Top Records', 'options' => $this->limitOptions, 'default' => 20],
            ];
        }

        $this->user = $_SESSION['user'] ?? [];

        $this->school_id = $this->user['school_id'] ?? null;
        $this->ignoresort = ['relaxation_data_id', 'score', 'student_id'];
    }

    public function format_griddata($data, $mperm)
    {
        $i = 1;
        foreach ($data as $key => $value) {
            $data[$key]['relaxation_data_id'] = $i++;
            $data[$key]['status'] = $this->gridswitch('status', 'a', 'd', $value);
            $data[$key]['actions'] = $this->action_formate($value, $mperm);
            $data[$key]['student_id'] = $value['student']['user_name'] ?? '-';
        }
        return $data;
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
        $data['relaxation_data_id'] = $data['id'];
        $data['school_id'] = $this->school_id;
        unset($data['_token']);
        unset($data['id']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }


    public function get_griddata($request, $gridCol, $mperm)
    {
        $data = $this->model::whereRaw($this->get_where($request, $gridCol))->with(['student']);
        //$data = $this->model::where("relaxation_data_id", '!=', 0)->with(['student']);
        // if (!(!empty($request['game_id']) && !empty($request['game_id']))) {
        //     $data = $this->model::where("relaxation_data_id", '==', 0)->with(['student']);
        // }
        //$data = $data->where('school_id', $this->school_id);
        // $request['sortby'] = $request['sortby'] == 'id' ? 'relaxation_data_id' : $request['sortby'];
        // if (!empty($request['sortby']) && !empty($request['sortdir'])) {
        //     $data->orderBy($request['sortby'], $request['sortdir']);
        // }
        $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];
        //dd($filterval);
        $request['limit'] = $filterval['filter_limit_new'] ?? 20;
        $data = $data->orderBy("score", 'desc');
        //dd($data);
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }





    public function gridCol($request)
    {
        $isort = $this->get_ignoresort();
        $gridCol = [];
        //array_push($gridCol, ['slug' => 'ckbox', 'title' => '<div class="checkbox"><label><input ng-model="chk_all" class="chk_all" ng-change="chkbox_change()" type="checkbox"></label></div>', 'sclass' => '']);
        foreach ($this->getgridCol() as $key => $value) {
            $slug = is_numeric($key) ? $value : $key;
            $title = is_numeric($key) ? Common::format_colname($value) : $value;
            $scalss = !in_array($slug, $isort) ? 'is_sort' : '';
            $scalss .= $scalss == 'is_sort' && !empty($request['sortby']) && !empty($request['sortdir']) && $request['sortby'] == $slug ? '  ' . $request['sortdir'] . '_sort' : '';
            array_push($gridCol, ['slug' => $slug, 'title' =>  $title, 'sclass' => $scalss]);
        }
        //array_push($gridCol, ['slug' => 'actions', 'title' => 'Actions', 'sclass' => '']);
        return $gridCol;
    }


    public function griddata(Request $request)
    {

        //return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
        $mperm = Common::user_roles();
        $gridCol = $this->gridCol($request);
        $err = 2;
        $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];

        if ($this->role != 'student') {
            if (!(!empty($filterval['filter_class_id']) && $filterval['filter_class_id'] != 'all')) {
                $err = 1;
            }
        }

        if (!(!empty($filterval['filter_game_id']) && $filterval['filter_game_id'] != 'all')) {
            $err = 1;
        }

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
            'msg' => ' Please select Game and Class'
            //'get_where' => $this->get_where($request, $gridCol)
        ];
    }


    public function get_where($request, $gridCol)
    {
        $where = '1 = 1';
        $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];
        $filters = $this->filters ?? [];

        if ($this->role != 'student') {
            if (!(!empty($filterval['filter_class_id']) && $filterval['filter_class_id'] != 'all')) {
                $where = ' relaxation_data_id = 0 ';
            }
        }
        if (!(!empty($filterval['filter_game_id']) && $filterval['filter_game_id'] != 'all')) {
            $where = ' relaxation_data_id = 0 ';
        }

        if ($where == '1 = 1') {
            if ($this->role != 'student') {
                $where = ' game_id = ' . $filterval['filter_game_id'] . ' and class_id = ' . $filterval['filter_class_id'] . ' ';
            } else {
                $where = ' game_id = ' . $filterval['filter_game_id'] . ' ';
            }
        }
        //dd($where, $filterval);
        // foreach ($filters as $key => $value) {
        //     $fval = isset($filterval["filter_" . $key]) ? trim($filterval["filter_" . $key]) : '';
        //     if ($fval != '' && $fval != 'all' && $fval !== 0 && $fval != '0') {
        //         if (empty($value['cond'])) {
        //             $operator = $value['operator'] ?? 'like';
        //             if ($operator != 'like') {
        //                 $where .= ' and  (' . $key . '  ' . $operator . ' "' . $fval . '"  )';
        //             } else {
        //                 $where .= ' and  (' . $key . ' like  "%' . $fval . '%"  )';
        //             }
        //         }
        //     }
        // }
        return $where;
    }


    public function filtersinputs($request)
    {
        $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];
        $filters = $this->filters ?? [];
        $filters_str = '';
        foreach ($filters as $key => $value) {
            $width = $value['width'] ?? 3;
            $value['titlewidth'] = $value['titlewidth'] ?? '12';
            $value['feildwidth'] = $value['feildwidth'] ?? '12';
            $value['class'] = 'filter_control input-sm';
            $value['title'] = $value['title'] ?? Common::format_colname($key) ?? '-';
            $value['placeholder'] = 'Search By ' . Common::format_colname($key);
            //$value['options'] = ['all' => 'All ' . $value['title']] + ($value['options'] ?? []);
            $value['options'] =  $value['options'] ?? [];
            $fval = $filterval['filter_' . $key] ?? '';
            $filters_str .= '<div class="col-md-' . $width . '">';
            $filters_str .= Common::render_field($key, $fval, $value);
            $filters_str .= '</div>';
        }
        return $filters_str;
    }

    public function redirectUser(Request $request)
    {
        $_COOKIE['admin_datatable_' . $this->slug];
        $input = $request->all();
        $st_id = $input['student_id'] ?? 0;
        $lesson_type = $input['lesson_type'] ?? 'ls';
        if ($lesson_type == 'dashboard') {
            header("Location: " . url('/') . "/#!/");
        } else if ($lesson_type == 'allin') {
            header("Location: " . url('/') . "/#!/multiplayerroom");
        } else {
            header("Location: " . url('/') . "/#!/studentlessonscore?type=" . $lesson_type);
        }
        die();
    }
}
