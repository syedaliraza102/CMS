<?php

namespace App\Http\Controllers\Admin;

use App\Models\AssigmentLesson;
use App\Http\Controllers\Controller;
use App\Models\AssigmentStudent;
use App\Models\School;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use App\Models\ClassTbl;
use Illuminate\Http\Request;

class AssigmentLessonController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = AssigmentLesson::class;
        $this->mTitle = 'Assignment';
        $this->slug = 'assigmentlesson';
        $this->pk = 'lesson_id';
        $this->user = $_SESSION['user'] ?? [];
        $this->user_id = $_SESSION['user']['id'] ?? '';
        $this->role = $_SESSION['user']['admin_role'] ?? '';
        $this->school_id = $_SESSION['user']['school_id'] ?? null;
        if ($this->role == 'student') {
            $this->gridCol = ['lesson_id' => 'Id', 'lesson_name' => 'Assignment Name', 'points', 'status',  'created_at'];
        } else {
            $this->gridCol = ['order_action' => '#', 'display_order' => 'Id', 'lesson_name' => 'Assignment Name', 'class_id' => 'Class', 'pending' => 'Not Attempted', 'submited',  'completed', 'created_at'];
        }
        // $this->gridCol = ['lesson_id' => 'id',  'lesson_name' => 'lesson Name', 'pending' => 'Not Attempted', 'submited', 'checked', 'completed', 'created_at' => 'Created At'];
        $this->viewCol = ['lesson_id', 'lesson_name', 'principal_name',   'created_at' => 'Created At'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->classOptions = ClassTbl::where('school_id', $this->school_id)->pluck('class_name', 'class_id')->toArray();
        $this->forms = [
            'lesson_name' => ['rules' => 'required|maxlength:255'],
            'lesson_topic' => ['rules' => 'required|maxlength:255'],
            'lesson_image' => ['title' =>  ' Image', 'type' => 'image', 'dir' => 'user'],
            'class_id' => ['type' => 'select', 'title' => 'Class',  'options' => $this->classOptions, 'rules' => 'required'],
            // 'class_id' => ['type' => 'ckeditor', 'title' => 'Class',  'options' => $this->classOptions, 'rules' => 'required'],
        ];
        $this->filters = [
            'lesson_name' => ["width" => 8, 'title' => 'Level Name', 'placeholder' => 'Search Level'],
            'status' => ['type' => 'select', "width" => 4, 'options' => $this->statusoptions, 'operator' => '=']
        ];


        //dd($this->user);
    }

    public function format_griddata($data, $mperm)
    {

        // if ($mperm['role'] == 'students' && !empty($data)) {

        // }
        $cls = ClassTbl::pluck('class_name', 'class_id')->toArray();
        $up_icon = url('public/icons/up.png');
        $down_icon = url('public/icons/down.png');
        $copy_icon = url('public/icons/17.png');
        foreach ($data as $key => $value) {
           
            if ($mperm['role'] == 'teachers') {
                $order_action = '<a class="btn custom_action_btn btn-xs text-white btn-success hide_btn" title="Move up" ng-click="addaction($event,' . "'move_up'" . ',' . $value['display_order'] . ')"> <i class="fa fa-arrow-circle-up  " aria-hidden="true"></i> </a>';
                $order_action .= '<a class="btn custom_action_btn btn-xs text-white btn-danger" title="Move Down" ng-click="addaction($event,' . "'move_down'" . ',' . $value['display_order'] . ')"> <i class="fa fa-arrow-circle-down  " aria-hidden="true"></i> </a>';
             
                $data[$key]['order_action'] = $order_action;

                $actions = '<a class="btn btn-xs text-white btn-info" title="Make Clone" ng-click="copyData(' . $value['display_order'] . ')"> <i class="fa fa-files-o" aria-hidden="true"></i> </a>';



                $pending = $this->StatusQuery($value['lesson_id'], 'pending');
                if (count($pending) > 0) {
                    $data[$key]['pending'] = '<a class="btn btn-xs text-white btn-danger view_assignment_status" style="font-weight:1000;font-size:18px;cursor:pointer" ng-click="showStudents(' . "rows.pending_data" . ',' . "true" . ')" >' . count($pending) . '</a>';
                    $data[$key]['pending_data'] = $pending;
                } else {
                    $data[$key]['pending'] = '<a class="btn btn-xs text-white btn-danger view_assignment_status" style="font-weight:1000;font-size:18px;cursor:pointer">' . count($pending) . '</a>';;
                }

                $submited = $this->StatusQuery($value['lesson_id'], 'submited');
                if (count($submited) > 0) {
                    $data[$key]['submited'] = '<a class="btn btn-xs text-white btn-primary view_assignment_status" ng-click="showStudents(' . "rows.submited_data" . ')" style="font-weight:1000;font-size:18px;cursor:pointer">' . count($submited) . '</a>';
                    $data[$key]['submited_data'] = $submited;
                } else {
                    $data[$key]['submited'] = '<a class="btn btn-xs text-white btn-primary view_assignment_status" style="font-weight:1000;font-size:18px;cursor:pointer">' . count($submited) . '</a>';
                }

                $completed = $this->StatusQuery($value['lesson_id'], 'completed');
                if (count($completed) > 0) {
                    $data[$key]['completed'] = '<a class="btn btn-xs text-white btn-success view_assignment_status" ng-click="showStudents(' . "rows.completed_data" . ')" style="font-weight:1000;font-size:18px;cursor:pointer">' . count($completed) . '</a>';
                    $data[$key]['completed_data'] = $completed;
                } else {
                    $data[$key]['completed'] = '<a class="btn btn-xs text-white btn-success view_assignment_status" style="font-weight:1000;font-size:18px;cursor:pointer">' . count($completed) . '</a>';
                }

                $checked = $this->StatusQuery($value['lesson_id'], 'checked');
                if (count($checked) > 0) {
                    $data[$key]['checked'] = '<a class="view_assignment_status" ng-click="showStudents(' . "rows.checked_data" . ')" style="display:block; cursor: pointer;">' . count($checked) . '</a>';
                    $data[$key]['checked_data'] = $checked;
                } else {
                    $data[$key]['checked'] = 0;
                }
                $data[$key]['class_id'] = $cls[$value['class_id']];
            } else {
                $actions = '<a class="btn btn-xs text-white btn-info" title="Submit Assignment" ng-click="showForm(' . $value['lesson_id'] . ',' . $this->user_id . ')"> <i class="fa fa-files-o" aria-hidden="true"></i> </a>';


                $temp = \DB::selectOne('SELECT  al.*, ast.*, al.lesson_id as lesson_id,al.created_at as created_at FROM `tbl_assigment_lesson` as al LEFT JOIN tbl_assigment_student as ast ON al.lesson_id = ast.lesson_id AND ast.student_id = ? WHERE al.lesson_id = ? group by al.lesson_id ', [$this->user_id, $value['lesson_id']]);
                $data[$key] = collect($temp)->toArray();
                //dd($data[$key]);
                $data[$key]['status'] = 'Pending';
                if (!empty($data[$key]['assigment_student_id'])) {
                    $data[$key]['status'] = 'Submited';
                }
                if (!empty($data[$key]['is_checked']) && $data[$key]['is_checked'] == 'y') {
                    $data[$key]['status'] = 'Checked';
                    if (!empty($data[$key]['is_student_checked']) && $data[$key]['is_student_checked'] == 'y') {
                        $data[$key]['status'] = 'Completed';
                    }
                }

                if ($data[$key]['status'] == 'Completed') {
                    $data[$key]['status'] = $this->gridlabel('Completed');
                } else {
                    $data[$key]['status'] = $this->gridlabel($data[$key]['status'], 'danger');
                }

                $data[$key]['points'] = $data[$key]['points'] ?? 0;
            }
            $actions .= $this->action_formate($value, $mperm);
            $data[$key]['actions'] = $actions;
        }
        return $data;
    }

    public function prepare_insert($data)
    {
        unset($data['_token']);
        unset($data['id']);
        $data['school_id'] = $this->school_id;
        $data['teacher_id'] = $this->user_id;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function prepare_update($data)
    {
        $data['lesson_id'] = $data['id'];
        $data['school_id'] = $this->school_id;
        unset($data['_token']);
        unset($data['id']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }


    public function get_griddata($request, $gridCol, $mperm)
    {

        $data = $this->model::whereRaw($this->get_where($request, $gridCol));
        $data = $data->where('school_id', $this->school_id);

        if ($this->role == 'teachers') {
            
            $data = $data->where('teacher_id', $this->user_id);
            if(isset($request->class_id) && $request->class_id >0) {
                $data = $data->where('class_id', $request->class_id);
            }

        } else {
            $data = $data->where('class_id', $_SESSION['user']['class_id']);
        }

        $request['sortby'] = $request['sortby'] == 'id' ? 'display_order' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }

    public function can_edit($value, $mperm)
    {
        if ($mperm['role'] == 'teachers') {
            return true;
        }
        return false;
    }

    public function can_delete($value, $mperm)
    {
        if ($mperm['role'] == 'teachers') {
            return true;
        }
        return false;
    }

    public function can_add($mperm)
    {
        if ($mperm['role'] == 'teachers') {
            return true;
        }
        return false;
    }

    public function get_ignoresort()
    {
        return ['pending', 'submited', 'checked', 'completed'];
    }

    public function StatusQuery($lesson_id, $status)
    {
        $WhereRaw = 'al.lesson_id  = ' . $lesson_id . ' and stu.admin_role = "student" ';
        if ($status == 'pending') {
            $WhereRaw .= ' AND ast.assigment_student_id IS null  ';
        } else if ($status == 'submited') {
            $WhereRaw .= ' AND ast.is_checked  = "n" and ast.is_student_checked  = "n" ';
        } else if ($status == 'checked') {
            $WhereRaw .= ' AND ast.is_checked  = "y" and ast.is_student_checked  = "n"  ';
        } else if ($status == 'completed') {
            $WhereRaw .= ' AND ast.is_checked  = "y" and ast.is_student_checked  = "y"  ';
        }

        $qry = 'SELECT stu.id, stu.class_id, stu.user_name, al.lesson_id as ls_id,  al.*, ast.* FROM `users` as stu
        LEFT JOIN tbl_assigment_lesson as al ON stu.class_id = al.class_id
        LEFT JOIN tbl_assigment_student as ast on stu.id = ast.student_id AND al.lesson_id = ast.lesson_id
        WHERE  ' . $WhereRaw . '
        GROUP BY stu.id order by  al.lesson_id desc';
        $data = \DB::select($qry);
        //dd($data);
        return $data;

        return [$qry];
    }

    public function getStudentLessonForm($lesson_id, $student_id, Request $request)
    {

        $data = \DB::selectOne('SELECT al.*, ast.* FROM `tbl_assigment_lesson` as al LEFT JOIN tbl_assigment_student as ast ON al.lesson_id = ast.lesson_id AND ast.student_id = ? WHERE al.lesson_id = ?', [$student_id, $lesson_id]);
        $this->forms = [
            'lesson_name' => ['rules' => 'required|maxlength:255'],
            'lesson_topic' => ['rules' => 'required|maxlength:255'],
            'lesson_image' => ['title' =>  ' Image', 'type' => 'image', 'dir' => 'user'],
            'points' =>  ['type' => 'number', 'rules' => 'required|maxlength:255'],
            'answer' => ['type' => 'textarea', 'title' => 'answer', 'feildwidth' => '10', 'rules' => 'required'],
        ];
        //dd($data);

        if ($this->role == "student") {
            $this->forms['lesson_image'] = ['type' => 'html', 'titlewidth' => 3, 'feildwidth' => 9, 'view' => 'admin.assign_image', 'htmldata' => $data];
        }



        $data =  collect($data ?? [])->toArray();
        //dd($data);
        $form =  $this->get_forms($request, $data);
        $form['is_complete'] = false;

        if (!empty($data['is_student_checked']) && $data['is_student_checked'] == 'y' && !empty($data['is_checked']) && $data['is_checked'] == 'y') {
            $form['is_complete'] = true;
        }
        return $form;
    }

    public function saveStudentLessonForm($lesson_id, $student_id, Request $request)
    {

        $input = $_POST;
        // $lesson_id = $input['lesson_id'];
        // $student_id = $input['student_id'];
        $qry = 'SELECT * FROM `tbl_assigment_student` WHERE student_id = ' . $student_id . ' and lesson_id  = ' . $lesson_id;
        $formData = \DB::selectOne($qry);
        $formData = collect($formData ?? [])->toArray();



        //$input['answer'] = str_replace('<p> </p>', '</br>', $input['answer']);


        if ($this->role == 'student') {

            if (empty($formData)) {

                $obj = new AssigmentStudent();
                $obj->lesson_id = $lesson_id;
                $obj->student_id = $student_id;
                $obj->answer = $input['answer'];
                $obj->created_at = date('Y-m-d H:i:s');
                $obj->updated_at = date('Y-m-d H:i:s');
                $obj->save();
                //$id = \DB::selectOne('tbl_assigment_student', $data);
            } else {
                if ($formData['is_checked'] == 'n') {
                    $obj = AssigmentStudent::find($formData['assigment_student_id']);
                    $obj->answer = $input['answer'];
                    $obj->update();
                } else if ($formData['is_checked'] == 'y' && $formData['is_student_checked'] == 'n') {
                    //dd($input['answer']);
                    $obj = AssigmentStudent::find($formData['assigment_student_id']);
                    $obj->is_student_checked = 'y';
                    $obj->answer = $input['answer'];
                    $obj->update();
                }
            }
        } else {

            if (!empty($formData) && $formData['is_checked'] == 'n') {
                $obj = AssigmentStudent::find($formData['assigment_student_id']);
                $obj->is_checked = 'y';
                $obj->is_student_checked = 'y';
                $obj->answer = $input['answer'];
                $obj->points = $input['points'];
                $obj->update();
            } else {
                $obj = AssigmentStudent::find($formData['assigment_student_id']);
                $obj->answer = $input['answer'];
                $obj->points = $input['points'];
                $obj->update();
            }
        }
        return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully.', 'data' => $input];
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


            $this->model::where('lesson_id',  $next['lesson_id'])->update(['display_order' => $obj->display_order]);
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

            $this->model::where('lesson_id', '=',  $prev['lesson_id'])->update(['display_order' => $obj->display_order]);
            $obj->display_order = $prev['display_order'];
            if ($obj->update()) {
                return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        }
        return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
    }
}
