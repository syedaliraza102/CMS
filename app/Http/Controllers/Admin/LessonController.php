<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lesson;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Fragment;
use App\Models\Game;
use App\Models\AdminUser;
use App\Models\ClassTbl;
use App\Models\Level;
use App\Models\Package;
use App\Models\LessonFragment;
use App\Models\LessonStudent;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use Illuminate\Http\Request;
use \App\Common;
use App\Models\AssignTeacher;

class LessonController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = Lesson::class;

        $this->type = isset($_GET['type']) ? $_GET['type'] : 'ls';
        
        $this->mTitle = $this->type == 'ls' ? __('title.lesson') : 'Exam';
        $this->mTitle = $this->type == 'challenges' ? 'Challenges' : $this->mTitle;
        $this->slug = 'lesson';
        $this->pk = 'lesson_id';

        $this->user = $_SESSION['user'] ?? [];
        $this->school_id = $this->user['school_id'] ?? null;
        $this->assignClasses = AssignTeacher::where('teacher_id', $_SESSION['user']['id'] ?? 0)->pluck('class_id')->toArray();
        $this->assignClassesIds = $this->assignClasses ?? [0];

        $this->gridCol = ['order_action' => '#', 'display_order' => 'ID',  'lesson_name' => __('title.lesson') . ' Name', 'class_id' => 'Class', 'status' => 'Status', 'created_at' => 'Created At'];
        $this->viewCol = ['lesson_id', 'lesson_name', 'created_at' => 'Created At'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->typeoptions = ['t1' => 'Type 1', 't2' => 'Type 2'];
        //$this->schoolOptions = School::pluck('school_name', 'school_id')->toArray();
        $this->fragmentOptions = Fragment::where('school_id', $this->school_id)->pluck('fragment_name', 'fragment_id')->toArray();
        $this->gameOptions = Game::where('game_type', 'h')->pluck('game_name', 'game_id')->toArray();
        $this->studentOptions = AdminUser::where('school_id', $this->school_id)->where('admin_role', 'student')->pluck('user_name', 'id')->toArray();
        $this->forms = [
            'lesson_name' => ['title' => __('title.lesson') . ' Name', 'rules' => 'required|maxlength:255', 'titlewidth' => 12, 'feildwidth' => 12],
            // 'fragment_ids' => ['type' => 'multipleselect', 'title' => 'Fragment', 'options' => $this->fragmentOptions, 'rules' => 'required', 'titlewidth' => 12, 'feildwidth' => 12],
            // 'game_ids' => ['type' => 'multipleselect', 'title' => 'Game', 'options' => $this->gameOptions, 'rules' => 'required'],
            'student_ids' => ['type' => 'multipleselect', 'title' => 'Student', 'options' => $this->studentOptions, 'titlewidth' => 12, 'feildwidth' => 12],
            'student_ids' => ['type' => 'multipleselect', 'title' => 'Student', 'options' => $this->studentOptions, 'titlewidth' => 12, 'feildwidth' => 12],
        ];
        $this->filters = [
            'lesson_name' => ["width" => 12, 'title' => __('title.lesson') . ' Name'],
        ];
    }

    public function format_griddata($data, $mperm)
    {
        $cls = ClassTbl::pluck('class_name', 'class_id')->toArray();
        $up_icon = url('public/icons/up.png');
        $down_icon = url('public/icons/down.png');
        $copy_icon = url('public/icons/17.png');

        foreach ($data as $key => $value) {
            $order_action = '';
            if ($this->type == 'ls') {
                $order_action = '<a class="btn custom_action_btn btn-xs text-white btn-success hide_btn" title="Move up" ng-click="addaction($event,' . "'move_up_lesson'" . ',' . $value['display_order'] . ')"> <i class="fa fa-arrow-circle-up  " aria-hidden="true"></i> </a>';
                $order_action .= '<a class="btn custom_action_btn btn-xs text-white btn-danger" title="Move Down" ng-click="addaction($event,' . "'move_down_lesson'" . ',' . $value['display_order'] . ')"> <i class="fa fa-arrow-circle-down  " aria-hidden="true"></i> </a>';
               
            }
            if ($this->type == 'challenges') {
                $order_action = '<a class="btn custom_action_btn btn-xs text-white btn-success hide_btn" title="Move up" ng-click="addaction($event,' . "'move_up_challenges'" . ',' . $value['display_order'] . ')"> <i class="fa fa-arrow-circle-up  " aria-hidden="true"></i> </a>';
                $order_action .= '<a class="btn custom_action_btn btn-xs text-white btn-danger" title="Move Down" ng-click="addaction($event,' . "'move_down_challenges'" . ',' . $value['display_order'] . ')"> <i class="fa fa-arrow-circle-down  " aria-hidden="true"></i> </a>';
               
            }
            if ($this->type == 'ex') {
                $order_action = '<a class="btn custom_action_btn btn-xs text-white btn-success hide_btn" title="Move up" ng-click="addaction($event,' . "'move_up_exam'" . ',' . $value['display_order'] . ')"> <i class="fa fa-arrow-circle-up  " aria-hidden="true"></i> </a>';
                $order_action .= '<a class="btn custom_action_btn btn-xs text-white btn-danger" title="Move Down" ng-click="addaction($event,' . "'move_down_exam'" . ',' . $value['display_order'] . ')"> <i class="fa fa-arrow-circle-down  " aria-hidden="true"></i> </a>';
               
            }
            $data[$key]['order_action'] = $order_action;


            $data[$key]['status'] = $this->gridswitch('status', 'a', 'd', $value);
            // $data[$key]['game_type'] = $this->typeoptions[$value['game_type']] ?? '-';
            // $data[$key]['school_id'] = $this->gridMultiLabel($data[$key]['school_id'] ?? [], $this->schoolOptions);
            $data[$key]['class_id'] = ($cls[$value['class_id']] ?? '-') . ' - ' . ($value['is_all'] == 'y' ? 'All' : 'Selection');

            $data[$key]['status'] = $this->gridswitch('status', 'a', 'd', $value);
            //$data[$key]['actions'] = $this->action_formate($value, $mperm);

                       $actions = '<a class="btn btn-xs text-white btn-danger hide_btn" title="Move up" ng-click="hideCalled($event,' . $value['id'] . ')"> <i class="fa fa-ban" aria-hidden="true"></i> </a>' . '<a class="btn btn-xs text-white btn-info" title="Move Down" ng-click="copyData(' . "'frg'" . ',' . $value[$this->getpk()] . ')"> <i class="fa fa-files-o" aria-hidden="true"></i> </a>';

            $actions .= $this->action_formate($value, $mperm);
            $data[$key]['actions'] = $actions;
        }
        return $data;
    }

    public function prepare_insert($data)
    {
        unset($data['_token']);
        unset($data['id']);
        unset($data['type']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['lesson_type'] = $this->type;
        return $data;
    }

    public function prepare_update($data)
    {
        $data['lesson_id'] = $data['id'];
        unset($data['_token']);
        unset($data['id']);
        unset($data['type']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['lesson_type'] = $this->type;
        return $data;
    }


    public function get_griddata($request, $gridCol, $mperm)
    {
        $data = $this->model::withTrashed()->whereRaw($this->get_where($request, $gridCol));
        $data = $data->withCount('score')->where('lesson_type', $this->type)->where('is_removed', 'n')->where('is_hide', 'n');
        if (!empty($request['filters'])) {
            $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];
            if (!empty($filterval['filter_school_id']) && $filterval['filter_school_id'] != 'all') {
                $data = $data->where('school_id', 'like', '%"' . $filterval['filter_school_id'] . '"%');
            }
        }
        
        if(isset($request->class_id) && $request->class_id >0) {
            $data = $data->where('class_id', $request->class_id);
        }

        $request['sortby'] = $request['sortby'] == 'id' ? 'display_order' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        //dd($data->toSql());
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }

    public function getfromdata(Request $request)
    {
        $form = [];
        return [
            'fragment' => Fragment::all()->toArray(),
            'student' => AdminUser::where('admin_role', 'student')->get()->toArray(),
            'class' => ClassTbl::whereIn('class_id', $this->assignClassesIds)->get()->toArray(),
            //'package' => Package::where('package_id', '!=', 1)->get()->toArray(),
            'package' => Package::get()->toArray(),
            'games' => Game::where('game_type', 'h')->get()->toArray(),
            'level' => Level::all()->toArray(),
            'form' => $form,
            'flag' => 1
        ];
    }

    public function update($id, Request $request)
    {
        ////Common::check_access('admin.' . $this->slug . '.edit');
        $obj = $this->model::withTrashed()->find($id);
        if (empty($obj)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.', 'data' => []];
        }

        $data = $request->all();
        $rec = Lesson::where('lesson_id', '!=', $id)->where('lesson_name', $data['lesson_name'])->count();
        if ($rec > 0) {
            //return ['flag' => 2, 'msg' => 'This Homework Name already taken', 'data' => []];
        }

        $fragments = $data['fragment'];
        $student_ids = $data['student_ids'];
        unset($data['fragment']);
        unset($data['student_ids']);

        $data = $this->prepare_update($data);

        foreach ($data as $key => $value) {
            $obj->$key = $value;
        }

        if ($obj->update()) {
            $obj = $obj->refresh();
            $this->saveData($obj->lesson_id, $fragments, $student_ids);
            
            return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully.', 'data' => $obj->toArray()];
        } else {
            return ['flag' => 2, 'msg' => ' Oops! Something went worng.', 'data' => []];
        }
    }

    public function save(Request $request)
    {
        ////Common::check_access('admin.' . $this->slug . '.add');
        $data = $request->all();

        $rec = Lesson::where('lesson_name', $data['lesson_name'])->count();
        if ($rec > 0) {
            //return ['flag' => 2, 'msg' => 'This Homework Name already taken', 'data' => []];
        }


        $fragments = $data['fragment'];
        $student_ids = $data['student_ids'];
        unset($data['fragment']);
        unset($data['student_ids']);

        $data = $this->prepare_insert($data);

        $obj = new $this->model;
        foreach ($data as $key => $value) {
            $obj->$key = $value;
        }
        $obj->status = 'd';
        if ($obj->save()) {
            $obj = $obj->refresh();
            $this->saveData($obj->lesson_id, $fragments, $student_ids);
            return ['flag' => 1, 'msg' => $this->mTitle . ' inserted Successfully.', 'data' => $obj->toArray()];
        } else {
            return ['flag' => 2, 'msg' => ' Oops! Something went worng.', 'data' => []];
        }
        return $data;
    }

    public function saveData($id, $fragments, $student_ids)
    {
        if (!empty($fragments)) {
            $lesson_fragment_ids = collect($fragments)->pluck('lesson_fragment_id')->toArray();
            $lesson_fragment_ids = !empty($lesson_fragment_ids) ? $lesson_fragment_ids : [0];
            LessonFragment::where('lesson_id', $id)->whereNotIn('lesson_fragment_id', $lesson_fragment_ids)->delete();
        }
        foreach ($fragments as $key => $value) {
            if (!empty($value['lesson_fragment_id'])) {
                $obj =  LessonFragment::find($value['lesson_fragment_id']);
            } else {
                $obj = new LessonFragment();
            }
            //$obj = new LessonFragment();
            $obj->lesson_id = $id;
            $obj->package_id = $value['package_id'] ?? null;
            $obj->game_id = $value['game_id'] ?? null;
            $obj->is_random = $value['is_random'] ?? 'n';
            $obj->level_id = $value['level_id'] ?? null;
            $obj->fragment_ids = $value['fragment_ids'] ?? null;
            if (!empty($value['lesson_fragment_id'])) {
                $obj->update();
            } else {
                $obj->save();
            }
        }
        $lgList = LessonFragment::where('lesson_id', $id)->get()->toArray();
        foreach ($lgList as $key => $value) {
            $obj = LessonFragment::find($value['lesson_fragment_id']);
            $obj->lg_id = $obj->lesson_fragment_id . '_' . $obj->lesson_id . '_' . $obj->game_id;
            //$obj->lg_id = $obj->lesson_id . '_' . $obj->game_id;
            $obj->update();
        }

        LessonStudent::where('lesson_id', $id)->delete();
      
        foreach ($student_ids as $key => $value) {
            $obj = new LessonStudent();
            $obj->lesson_id = $id;
            $obj->student_id = $value;
            $obj->ls_id = $id . '_' . $value;
            $obj->save();
        }
    }


    public function add(Request $request)
    {

        $cls = AssignTeacher::where('teacher_id', $_SESSION['user']['id'] ?? 0)->count();
        if ($cls == 0) {
            return ['flag' => 4, 'msg' => 'You are not assign to any class'];
        }
        

        $data = $this->get_forms($request);
        $data['formData'] = [
            'fragment' => Fragment::where('data_type', 'frg')->where('school_id', $this->school_id)->get()->toArray(),
            'student' => AdminUser::where('school_id', $this->school_id)->where('admin_role', 'student')->get()->toArray(),
            'class' => ClassTbl::whereIn('class_id', $this->assignClassesIds)->get()->toArray(),
            //'package' =>  Package::where('package_id', '!=', 1)->get()->toArray(),
            'package' =>  Package::get()->toArray(),
            'games' => Game::where('game_type', 'h')->get()->toArray(),
            'level' => Level::where('school_id', $this->school_id)->get()->toArray(),
            'form' => [],
            'pk' => null
        ];
        return $data;
    }

    public function edit($id, Request $request)
    {
        ////Common::check_access();
        $data = $form =  $this->model::withTrashed()->find($id);
        if (empty($data)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.'];
        }

        $cls = AssignTeacher::where('teacher_id', $_SESSION['user']['id'] ?? 0)->count();
        if ($cls == 0) {
            return ['flag' => 4, 'msg' => 'You are not assign to any class'];
        }
        $data = $this->get_forms($request, $data);
        $data['formData'] = [
            'fragment' => Fragment::where('data_type', 'frg')->where('school_id', $this->school_id)->get()->toArray(),
            'student' => AdminUser::where('school_id', $this->school_id)->where('admin_role', 'student')->get()->toArray(),
            'class' => ClassTbl::whereIn('class_id', $this->assignClassesIds)->get()->toArray(),
            //'package' =>  Package::where('package_id', '!=', 1)->get()->toArray(),
            'package' =>  Package::get()->toArray(),
            'games' => Game::where('game_type', 'h')->get()->toArray(),
            'level' => Level::where('school_id', $this->school_id)->get()->toArray(),
            'form' => $form->toArray(),
            'pk' => $id,
        ];
        $data['formData']['form']['fragments'] = LessonFragment::where('lesson_id', $id)->orderBy('lesson_fragment_id', 'asc')->get()->toArray();
        $data['formData']['form']['student_ids'] = LessonStudent::where('lesson_id', $id)->pluck('student_id')->toArray();

        return $data;
    }


    public function bulkaction_updatecol($request)
    {
        $obj = $this->model::withTrashed()->find($request['id']);
        //dd('called');
        $obj->{$request['col']} = $request['val'];
        if ($obj->update()) {
            if ($request['col'] == 'status' && $request['val'] == 'a') {
                $obj->restore();
            } else {
                $obj->delete();
            }
            return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
        } else {
            return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
        }
    }


    public function delete($id, Request $request)
    {
        $obj = $this->model::withTrashed()->find($id);
        if (empty($obj)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.', 'data' => []];
        }
        $obj->is_removed = 'y';
        $obj->update();
        $this->remove_file($obj);
        if ($obj->delete()) {
            return ['flag' => 1, 'msg' => $this->mTitle . ' Deleted Successfully.'];
        } else {
            return ['flag' => 2, 'msg' => ' Oops! Something went worng.', 'data' => []];
        }
    }


    public function can_delete($value, $mperm)
    {
        if ($mperm['role'] != 'admin' && !in_array('admin.' . $this->slug . '.delete', $mperm['actions'])) {
            return false;
        }
        if (isset($value['score_count']) && $value['score_count'] > 0) {
            return false;
        }
        return true;
    }

    public function bulkaction_($slug, $request)
    {
        //return $slug;
        if ($slug == 'move_up_lesson') {
            
            $obj = $this->model::withTrashed()->where('display_order', $request['id'])->where('lesson_type', 'ls')->first();
            $next = $this->model::withTrashed()->where('display_order', '>', $request['id'])->where('is_removed', 'n')->where('is_hide', 'n')->where('lesson_type', 'ls')->orderBy('display_order', 'ASC')->first();
            if (empty($next)) {
                return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
            }

            $next_display_order= $next->display_order;
            $next->display_order = $obj->display_order;
            $next->save();

            /*$this->model::where('lesson_id',  $next['lesson_id'])->update(['display_order' => $obj->display_order]);
            $obj->display_order = $next['display_order'];
            */

            $obj->display_order = $next_display_order;
            $obj->save();

            if ($obj->update()) {
                return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }

        } else if ($slug == 'move_down_lesson') {
            $obj = $this->model::withTrashed()->where('display_order', $request['id'])->where('lesson_type', 'ls')->first();
            $prev = $this->model::withTrashed()->where('display_order', '<', $request['id'])->where('is_removed', 'n')->where('is_hide', 'n')->where('lesson_type', 'ls')->orderBy('display_order', 'DESC')->first();
            if (empty($prev)) {
                return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
            }

            /*$this->model::withTrashed()->where('lesson_id', '=',  $prev['lesson_id'])->update(['display_order' => $obj->display_order]);
            $obj->display_order = $prev['display_order'];*/
            $prev_display_order= $prev->display_order;
            $prev->display_order = $obj->display_order;
            

            $prev->save();

            $obj->display_order = $prev_display_order;
            $obj->save();
            return ['flag' => 1, 'msg' =>  'User Activated Successfully'];

            if ($obj->update()) {
                return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        } else if ($slug == 'move_up_exam') {
            $obj = $this->model::withTrashed()->where('display_order', $request['id'])->where('lesson_type', 'ex')->first();

            $next = $this->model::withTrashed()->where('display_order', '>', $request['id'])->where('is_removed', 'n')->where('is_hide', 'n')->where('lesson_type', 'ex')->orderBy('display_order', 'asc')->first();

            if (empty($next)) {
                return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
            }

            $next_display_order= $next->display_order;
            $next->display_order = $obj->display_order;
            $next->save();
            
            $obj->display_order = $next_display_order;
            $obj->save();
            return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
            

            $this->model::withTrashed()->where('lesson_id',  $next['lesson_id'])->update(['display_order' => $obj->display_order]);
            $obj->display_order = $next['display_order'];
            if ($obj->update()) {
                return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        } else if ($slug == 'move_down_exam') {
            $obj = $this->model::withTrashed()->where('display_order', $request['id'])->where('lesson_type', 
                'ex')->first();
            $prev = $this->model::withTrashed()->where('display_order', '<', $request['id'])->where('is_removed', 'n')->where('is_hide', 'n')->where('lesson_type', 'ex')->orderBy('display_order', 'DESC')->first();

            if (empty($prev)) {
                return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
            }
            $prev_display_order= $prev->display_order;
            $prev->display_order = $obj->display_order;
            

            $prev->save();

            $obj->display_order = $prev_display_order;
            $obj->save();
            return ['flag' => 1, 'msg' =>  'User Activated Successfully'];

            $this->model::withTrashed()->where('lesson_id', '=',  $prev['lesson_id'])->update(['display_order' => $obj->display_order]);
            $obj->display_order = $prev['display_order'];
            if ($obj->update()) {
                return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        }else if ($slug == 'move_up_challenges') {
            $obj = $this->model::withTrashed()->where('display_order', $request['id'])->where('lesson_type', 'challenges')->first();
            $next = $this->model::withTrashed()->where('display_order', '>', $request['id'])->where('is_removed', 'n')->where('is_hide', 'n')->where('lesson_type', 'challenges')->orderBy('display_order', 'asc')->first();

            if (empty($next)) {
                return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
            }

            $next_display_order= $next->display_order;
            $next->display_order = $obj->display_order;
            $next->save();
            $obj->display_order = $next_display_order;
            $obj->save();
            return ['flag' => 1, 'msg' =>  'Updated Successfully'];
            
        }else if ($slug == 'move_down_challenges') {
            $obj = $this->model::withTrashed()->where('display_order', $request['id'])->where('lesson_type', 
                'challenges')->first();
            $prev = $this->model::withTrashed()->where('display_order', '<', $request['id'])->where('is_removed', 'n')->where('is_hide', 'n')->where('lesson_type', 'challenges')->orderBy('display_order', 'DESC')->first();

            if (empty($prev)) {
                return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
            }
            $prev_display_order= $prev->display_order;
            $prev->display_order = $obj->display_order;
            

            $prev->save();

            $obj->display_order = $prev_display_order;
            $obj->save();
            return ['flag' => 1, 'msg' =>  'Updated Successfully'];
        }
        return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
    }
}
