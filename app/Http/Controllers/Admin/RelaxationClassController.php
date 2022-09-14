<?php

namespace App\Http\Controllers\Admin;

use App\Models\RelaxationClass;
use App\Http\Controllers\Controller;
use App\Models\ClassTbl;
use App\Models\AdminUser;
use App\Models\Game;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use App\User;
use Illuminate\Http\Request;
use \App\Common;
use App\Models\AssignTeacher;
use App\Models\Fragment;
use App\Models\School;
use Illuminate\Support\Facades\Route;
class RelaxationClassController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {

        $this->model = RelaxationClass::class;
        $this->mTitle = 'Arcade';
        $this->slug = 'relaxationclass';
        $this->pk = 'relaxation_class_id';

        $this->gridCol = ['order_action' => '#', 'display_order' => 'Id',  "class_id" => 'Class', "game_id" => 'Game', "fragment_id" => 'Lesson Block',  'activated_by' => 'Activated By', 'deactivated_by' => 'Deactivated By', 'status', 'created_at' => 'Created At'];
        $this->viewCol = ['relaxation_class_id' => 'Id', "class_id" => 'Class', "game_id" => 'Game',  'created_at' => 'Created At', 'created_at' => 'Created At', 'created_at' => 'Created At'];


        $this->user = $_SESSION['user'] ?? [];
        $this->school_id = $this->user['school_id'] ?? null;
        //dd($this->school_id);

        $this->gameOptions = ['all' => 'All'];
        $tempgameOptions = Game::whereIn('game_type', ['r', 'exw', 'exs'])->pluck('game_name', 'game_id')->toArray();
        foreach ($tempgameOptions as $key => $value) {
            $this->gameOptions[$key] = $value;
        }

        $this->assignClasses = AssignTeacher::where('teacher_id', $_SESSION['user']['id'] ?? 0)->pluck('class_id')->toArray();
        $this->assignClassesIds = $this->assignClasses ?? [0];

        $this->classOptions = ClassTbl::whereIn('class_id', $this->assignClassesIds)->pluck('class_name', 'class_id')->toArray();
        $this->forms = [
            'class_id' => ['type' => 'select', 'title' => 'Class',  'options' => $this->classOptions, 'rules' => 'required'],
            //'game_ids' => ['type' => 'select', 'title' => 'Game',  'options' => $this->gameOptions, 'rules' => 'required'],
            //'game_ids' => ['type' => 'checkbox', 'title' => 'Games',  'options' => $this->gameOptions, 'rules' => 'required'],
        ];
        $this->filters = [
            //'class_id' => ['type' => 'select', 'cond' => 'c', 'title' => 'Class', "width" => 6, 'options' => //$this->classOptions, 'operator' => '='],
            'game_id' => ['type' => 'select', 'cond' => 'c', 'title' => 'Game', "width" => 6, 'options' => $this->gameOptions, 'operator' => '='],
        ];
      
    }

    public function format_griddata($data, $mperm)
    {
        $up_icon = url('public/icons/up.png');
        $down_icon = url('public/icons/down.png');
        $copy_icon = url('public/icons/17.png');
        foreach ($data as $key => $value) {
            
            $data[$key]['checkbox'] = '<input type="checkbox" class="selected_ids" name=selected_ids[] value="'.$value['relaxation_class_id'].'" style="margin-left:0px !important">';
            $data[$key]['class_id'] = $this->classOptions[$value['class_id'] ?? ''] ?? '-';
            $data[$key]['game_id'] = $this->gameOptions[$value['game_id'] ?? ''] ?? '-';
            $data[$key]['activated_by'] = '-';
            $data[$key]['deactivated_by'] = '-';
            $order_action = '<a class="btn custom_action_btn btn-xs text-white btn-success hide_btn" title="Move up" ng-click="addaction($event,' . "'move_up'" . ',' . $value['display_order'] . ')"> <i class="fa fa-arrow-circle-up  " aria-hidden="true"></i> </a>';
            $order_action .= '<a class="btn custom_action_btn btn-xs text-white btn-danger" title="Move Down" ng-click="addaction($event,' . "'move_down'" . ',' . $value['display_order'] . ')"> <i class="fa fa-arrow-circle-down  " aria-hidden="true"></i> </a>';
           
            $data[$key]['order_action'] = $order_action;

            if ($value['status'] == 'a') {
                $data[$key]['activated_by'] = $value['activated_user']['user_name'] ?? '-';
            } else {
                $data[$key]['deactivated_by'] = $value['deactivated_user']['user_name'] ?? '-';
            }
            $data[$key]['fragment_id'] = $value['fragment']['fragment_name'] ?? '-';

            $data[$key]['actions'] = $this->action_formate($value, $mperm);
            $data[$key]['status'] = $this->gridswitch('status', 'a', 'd', $value);
        }
        return $data;
    }

    public function get_griddata($request, $gridCol, $mperm)
    {
        $data = $this->model::whereRaw($this->get_where($request, $gridCol))->with(['activated_user', 'deactivated_user', 'fragment']);
        $data = $data->where('school_id', $this->user['school_id'] ?? null);
        if(isset($request->class_id) && $request->class_id >0) {
            $data = $data->where('class_id', $request->class_id);
        }
        // $classIds = array_keys($this->classOptions);
        // $gameIds = array_keys($this->gameOptions);

        // $data = $data->whereIn('game_id', $gameIds ?? [0]);

        $request['sortby'] = $request['sortby'] == 'id' ? 'display_order' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            if($request['sortby']!='checkbox') {
                $data->orderBy($request['sortby'], $request['sortdir']);
            }
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
        $data['activated_by'] = $this->user['id'] ?? 0;
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['school_id'] = $this->school_id;
        return $data;
    }

    public function prepare_update($data)
    {
        $data['relaxation_class_id'] = $data['id'];
        unset($data['_token']);
        unset($data['id']);
        $data['activated_by'] = $this->user['id'];
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['school_id'] = $this->school_id;
        return $data;
    }

    public function can_edit($value, $mperm)
    {
        if (!empty($value['type']) && $value['type'] == 'r') {
            return false;
        }
        return true;
    }

    public function can_delete($value, $mperm)
    {
        return false;
    }

    public function can_view($value, $mperm)
    {
        return false;
    }

    public function can_add($mperm)
    {
        return true;
    }



    public function getstudentlist(Request $request)
    {

        $input = $request->all();

        $games = RelaxationClass::where('class_id', $input['class_id'])->pluck('game_id')->toArray();
        $g_ids = $games ?? [0];



        $form_str = '';
        $types = ['r' => 'Extra Training - No Data', 'exw' => 'Extra Training - Word', 'exs' => 'Extra Training - Sentence'];
        foreach ($types as $tkey => $tvalue) {
            $tempgameOptions = Game::where('game_type', $tkey)->whereNotIn('game_id', $g_ids)->pluck('game_name', 'game_id')->toArray();
            //dd($tempgameOptions);
            if (!empty($tempgameOptions)) {
                $gList = ['all' => 'All'];
                $gList = [];
                foreach ($tempgameOptions as $key => $value) {
                    $gList[$key] = $value;
                }
                //$attr = ['type' => 'checkbox', 'title' =>  $tvalue,  'options' => $gList, 'rules' => 'required'];
                $attr = ['title' =>  $tvalue, 'class' => $tkey . '_val ex_radio', 'type' => 'radio', 'options' => $gList];
                $fval =  [];
                $data = Common::format_feilddata('game_ids[]', $fval, $attr);
                $form_str .= view('admin.feilds.' . $data['type'], ['data' => $data])->render();
            } else {
                $form_str .= '<div class="form-group row ng-scope">
                <label for="game_ids_radio_id" class="col-sm-2 control-label text-right">' . $tvalue . '</label>
                <div class="col-sm-8">
                            -
                </div>
            </div>';
            }
        }
        // if ($form_str == '') {
        //     return [
        //         'msg' => 'No Games available for this class',
        //         'flag' => 2
        //     ];
        // }
        return [
            'form_str' => $form_str,
            'form' => $input,
            'flag' => 1
        ];

        // $form = [];

        // $games = RelaxationClass::where('game_id', $input['game_id'])->whereNotNull('student_ids')->pluck('student_ids')->toArray();
        // $st_ids = [0];
        // foreach ($games as $key => $value) {
        //     foreach ($value as $key2 => $value2) {
        //         array_push($st_ids, $value2);
        //     }
        // }
        // //return $st_ids;

        // $tempschoolOptions = AdminUser::whereNotIn('id', $st_ids)->where('class_id', $input['class_id'])->where('admin_role', 'student')->pluck('user_name', 'id')->toArray();
        // foreach ($tempschoolOptions as $key => $value) {
        //     $stList[$key] = $value;
        // }

        // if (count($stList) == 1) {
        //     return [
        //         'msg' => 'No Students available for this game and class',
        //         'flag' => 2
        //     ];
        // }
        // $attr = ['type' => 'checkbox', 'title' => 'Students',  'options' => $stList, 'rules' => 'required'];

        // $fval =  [];
        // $data = Common::format_feilddata('student_ids', $fval, $attr);
        // $form_str = view('admin.feilds.' . $data['type'], ['data' => $data])->render();
        // return [
        //     'form_str' => $form_str,
        //     'form' => $input,
        //     'flag' => 1
        // ];
    }

    public function get_forms($request, $feildvalues = [])
    {
        $this->forms = $this->forms ?? [];
        $form_arr = [];
        $form_render_arr = [];
        $form_str = '';
        foreach ($this->forms as $key => $attr) {
            $fval =  $feildvalues[$key] ?? '';
            $data = Common::format_feilddata($key, $fval, $attr);
            array_push($form_arr, $data);
            if ($data['type'] == 'html') {
                $render =  $form_str .= view($data['view'], ['data' => $data]);
            } else {
                $render = $form_str .= view('admin.feilds.' . $data['type'], ['data' => $data]);
            }
            array_push($form_render_arr, $render);
        }
        if (!empty($feildvalues)) {
            //dd($feildvalues);



            // $games = RelaxationClass::where('class_id', $feildvalues['class_id'])->pluck('game_id')->toArray();
            // $g_ids = $games ?? [0];

            // $types = ['r' => 'Extra Training - No Data', 'exw' => 'Extra Training - Word', 'exs' => 'Extra Training - Sentence'];
            // foreach ($types as $tkey => $tvalue) {
            //     $tempgameOptions = Game::where('game_type', $tkey)->whereNotIn('game_id', $g_ids)->pluck('game_name', 'game_id')->toArray();
            //     //dd($tempgameOptions);
            //     if (!empty($tempgameOptions)) {
            //         $gList = ['all' => 'All'];
            //         $gList = [];
            //         foreach ($tempgameOptions as $key => $value) {
            //             $gList[$key] = $value;
            //         }
            //         //$attr = ['type' => 'checkbox', 'title' =>  $tvalue,  'options' => $gList, 'rules' => 'required'];
            //         $attr = ['title' =>  $tvalue, 'class' => $tkey . '_val ex_radio', 'type' => 'radio', 'options' => $gList];
            //         $fval =  [];
            //         $data = Common::format_feilddata('game_ids[]', $fval, $attr);
            //         $form_str .= view('admin.feilds.' . $data['type'], ['data' => $data])->render();
            //     } else {
            //         $form_str .= '<div class="form-group row ng-scope">
            //     <label for="game_ids_radio_id" class="col-sm-2 control-label text-right">' . $tvalue . '</label>
            //     <div class="col-sm-8">
            //                 -
            //     </div>
            // </div>';
            //     }
            // }


            $type = $feildvalues['type'];
            if ($type == 'exw') {
                $fragmentList = Fragment::where('school_id', $this->school_id)->where('data_type', 'frg')->where(function ($query) {
                    $query->where('package_ids', 'like', '%"5"%');
                })->pluck('fragment_name', 'fragment_id')->toArray();
                $attr = ['title' =>  'Fragment',  'type' => 'select', 'options' => $fragmentList];
                $fval =  $feildvalues['fragment_id'];
                $data = Common::format_feilddata('fragment_id', $fval, $attr);
                $form_str .= view('admin.feilds.' . $data['type'], ['data' => $data])->render();
                // return [
                //     'form_str' => $form_str,
                //     'form' => [],
                //     'flag' => 1
                // ];
            } else  if ($type == 'exs') {
                $fragmentList = Fragment::where('school_id', $this->school_id)->where('data_type', 'frg')->where(function ($query) {
                    $query->whereOr('package_ids', 'like', '%"2"%')
                        ->whereOr('package_ids', 'like', '%"10"%')
                        ->whereOr('package_ids', 'like', '%"9"%');
                })->pluck('fragment_name', 'fragment_id')->toArray();
                $attr = ['title' =>  'Fragment',  'type' => 'select', 'options' => $fragmentList];
                $fval =  $feildvalues['fragment_id'];
                $data = Common::format_feilddata('fragment_id', $fval, $attr);
                $form_str .= view('admin.feilds.' . $data['type'], ['data' => $data])->render();
                // return [
                //     'form_str' => $form_str,
                //     'form' => [],
                //     'flag' => 1
                // ];
            }
        }

        return ['form_arr' => $form_arr, 'form_str' => $form_str, 'form_render_arr' => $form_render_arr, 'flag' => 1];
    }

    public function getFragmentList(Request $request)
    {

        $type = $request->type;

        if ($type == 'exw') {

            $fragmentList = Fragment::where('school_id', $this->school_id)->where('data_type', 'frg')->where(function ($query) {
                $query->where('package_ids', 'like', '%"5"%');
            })->pluck('fragment_name', 'fragment_id')->toArray();
            $attr = ['title' =>  'Fragment',  'type' => 'select', 'options' => $fragmentList];
            $fval =  [];
            $data = Common::format_feilddata('fragment_id', $fval, $attr);
            $form_str = view('admin.feilds.' . $data['type'], ['data' => $data])->render();
            return [
                'form_str' => $form_str,
                'form' => [],
                'flag' => 1
            ];
        } else  if ($type == 'exs') {
            $fragmentList = Fragment::where('school_id', $this->school_id)->where('data_type', 'frg')->where(function ($query) {
                $query->whereOr('package_ids', 'like', '%"2"%')
                    ->whereOr('package_ids', 'like', '%"10"%')
                    ->whereOr('package_ids', 'like', '%"9"%');
            })->pluck('fragment_name', 'fragment_id')->toArray();
            $attr = ['title' =>  'Fragment',  'type' => 'select', 'options' => $fragmentList];
            $fval =  [];
            $data = Common::format_feilddata('fragment_id', $fval, $attr);
            $form_str = view('admin.feilds.' . $data['type'], ['data' => $data])->render();
            return [
                'form_str' => $form_str,
                'form' => [],
                'flag' => 1
            ];
        }

        return [
            'form_str' => '',
            'form' => [],
            'flag' => 2
        ];
    }

    public function bulkaction_updatecol($request)
    {
        $obj = $this->model::find($request['id']);
        $obj->{$request['col']} = $request['val'];
        if ($request['col'] == 'status' && $request['val'] == 'd') {
            $obj->deactivated_by = $this->user['id'] ?? 0;
        } else  if ($request['col'] == 'status' && $request['val'] == 'a') {
            $obj->activated_by = $this->user['id'] ?? 0;
        }
        if ($obj->update()) {
            return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
        } else {
            return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
        }
    }

    public function bulkaction_($slug, $request)
    {
        //return $slug;
        if ($slug == 'move_up') {
            $obj = $this->model::where('display_order', $request['id'])->first();
            $next = $this->model::where('display_order', '>', $request['id'])->orderBy('display_order', 'asc')->first();
            if (empty($next)) {
                return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
            }
            $next_display_order= $next->display_order;
            $next->display_order = $obj->display_order;
            

            $next->save();

            $obj->display_order = $next_display_order;
            $obj->save();
            return ['flag' => 1, 'msg' =>  'User Activated Successfully'];

            $this->model::where('relaxation_class_id',  $next['relaxation_class_id'])->update(['display_order' => $obj->display_order]);
            $obj->display_order = $next['display_order'];
            if ($obj->update()) {
                return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        } else if ($slug == 'move_down') {
            $obj = $this->model::where('display_order', $request['id'])->first();
            $prev = $this->model::where('display_order', '<', $request['id'])->orderBy('display_order', 'DESC')->first();
            if (empty($prev)) {
                return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
            }
            
            $prev_display_order= $prev->display_order;
            $prev->display_order = $obj->display_order;
            $prev->save();

            $obj->display_order = $prev_display_order;
            $obj->save();
            return ['flag' => 1, 'msg' =>  'User Activated Successfully'];


            $this->model::where('relaxation_class_id', '=',  $prev['relaxation_class_id'])->update(['display_order' => $obj->display_order]);
            $obj->display_order = $prev['display_order'];
            if ($obj->update()) {
                return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        }
        return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
    }



    public function save(Request $request)
    {
        //Common::check_access('admin.' . $this->slug . '.add');
        $data = $this->prepare_insert($request->all());

        $err = $this->checkCrud($data);
        if (!empty($err)) {
            return ['flag' => 2, 'msg' => $err, 'data' => $err];
        }

        foreach ($data['game_ids'] as $key => $value) {
            if ($value != 'all') {
                $obj = new $this->model;
                $obj->class_id = $data['class_id'] ?? 0;
                $obj->game_id = $value;
                $obj->fragment_id = $data['fragment_id'] ?? 0;
                $obj->type = $data['type'] ?? 'r';
                $obj->created_at = date('Y-m-d H:i:s');
                $obj->updated_at = date('Y-m-d H:i:s');
                $obj->activated_by = $this->user['id'] ?? 0;
                $obj->school_id = $this->user['school_id'] ?? 0;
                $obj->save();
            }
        }
        return ['flag' => 1, 'msg' => $this->mTitle . ' inserted Successfully.', 'data' => []];
    }

    public function gridCol($request)
    {
        $isort = $this->get_ignoresort();
        $gridCol = [];
        //array_push($gridCol, ['slug' => 'ckbox', 'title' => '<div class="checkbox"><label><input ng-model="chk_all" class="chk_all" ng-change="chkbox_change()" type="checkbox"></label></div>', 'sclass' => '']);
        array_push($gridCol, ['slug' => 'checkbox', 'title' => '<input type="checkbox" id="checkAll" name="select_all" style="margin-left:0px !important">', 'sclass' => '']);
        foreach ($this->getgridCol() as $key => $value) {
            $slug = is_numeric($key) ? $value : $key;
            $title = is_numeric($key) ? Common::format_colname($value) : $value;
            $scalss = !in_array($slug, $isort) ? 'is_sort' : '';
            $scalss .= $scalss == 'is_sort' && !empty($request['sortby']) && !empty($request['sortdir']) && $request['sortby'] == $slug ? '  ' . $request['sortdir'] . '_sort' : '';
            array_push($gridCol, ['slug' => $slug, 'title' =>  $title, 'sclass' => $scalss]);
        }
        array_push($gridCol, ['slug' => 'actions', 'title' => 'Actions', 'sclass' => '']);
        return $gridCol;
    }

    public function relaclassactdect (Request $request) 
    {
        $selected_ids = explode(',', $request->selected_ids);
        $selectedData = RelaxationClass::whereIn('relaxation_class_id', $selected_ids)->get();
        foreach ($selectedData as $data) 
        {
            if($request->type==="1") {
                $data->status = 'a';   
            }
            if($request->type==="2") {
                $data->status = 'd';   
            }
            $data->save();
        }
        return response()->json(['status'=>true, 'message'=>'Record has been saved.']);
    }
}
