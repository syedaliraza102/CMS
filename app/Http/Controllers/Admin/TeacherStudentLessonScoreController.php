<?php

namespace App\Http\Controllers\Admin;

use App\Models\ClassTbl;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\School;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use \App\Common;
use App\Models\AdminUser;
use App\Models\AssignTeacher;
use App\Models\LessonStudent;
use App\Models\Users;
use Illuminate\Support\Facades\DB;

class TeacherStudentLessonScoreController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {

        $this->type = !empty($_GET['type']) && $_GET['type'] == 'ex' ? 'ex' : 'ls';
        $this->model = Lesson::class;
        $this->model = LessonStudent::class;
        $this->mTitle = $this->type == 'ls' ? 'Lessons' : 'Exam';
        $this->slug = 'teacherstudentlessonscore';
        $this->pk = 'lesson_student_id';

        $this->gridCol = ['student_name' => 'Student',  'lesson_name' => 'Lesson Name', 'class_id' => "Class", 'total_modules' => 'Total Modules', "pending_modules" => 'Pending Modules', 'completed_modules' => 'Completed Modules', 'status'];


        $this->forms = [
            'class_name' => ['rules' => 'required|maxlength:255'],
        ];
        $this->user = $_SESSION['user'] ?? [];
        $this->class_ids = AssignTeacher::where('teacher_id', $this->user['id'])->pluck('class_id')->toArray(); //410976
        $this->class_ids = $this->class_ids ?? [0];
        $this->studentOptions = AdminUser::whereIn('class_id', $this->class_ids)->pluck('user_name', 'id')->toArray();
        $this->classOptions = ClassTbl::whereIn('class_id', $this->class_ids)->pluck('class_name', 'class_id')->toArray();


        $statusOptions = ['c' => 'Completed', 'p' => 'Pending'];
        $this->filters = [
            'lesson_name' => ["width" => 3, 'title' => 'Lesson Name', 'placeholder' => 'Seac'],
            'student_id' => ['type' => 'select', "width" => 3, 'title' => 'Student', 'options' => $this->studentOptions],
            'class_id' => ['type' => 'select', "width" => 3, 'title' => 'Class', 'options' => $this->classOptions],
            'status' => ['type' => 'select', "width" => 3, 'title' => 'Status', 'options' => $statusOptions],
        ];

        $this->school_id = $this->user['school_id'] ?? null;
        $this->ignoresort = ['student_name', 'lesson_id', 'lesson_name', 'total_modules', 'pending_modules', 'completed_modules', 'status', 'class_id'];
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            $data[$key]['lesson_name'] = $value['lesson']['lesson_name'] ?? '';
            $data[$key]['student_name'] = $value['student']['user_name'] ?? '';
            $data[$key]['class_id'] = $value['student']['classtbl']['class_name'] ?? '';
            //$lesson_fragment = collect($value['lesson']['lesson_fragment'] ?? []);
            $lesson_fragment = $value['lesson']['lesson_fragment'] ?? [];
            foreach ($lesson_fragment as $tmkey => $tmvalue) {
                if (empty($tmvalue['game'])) {
                    unset($lesson_fragment[$tmkey]);
                }
            }
            $lesson_fragment = collect($lesson_fragment);

            $lg_ids = $lesson_fragment->pluck('lg_id')->toArray();
            //dd($lg_ids);
            $studentscore = collect($value['studentscore'] ?? []);
            $data[$key]['total_modules'] = $lesson_fragment->count();
            $data[$key]['completed_modules'] = $studentscore->whereIn('lg_id', $lg_ids)->groupBy('lg_id')->count();
            $data[$key]['pending_modules'] = $data[$key]['total_modules'] - $data[$key]['completed_modules'];
            $data[$key]['status'] = $data[$key]['pending_modules'] > 0 ? 'Pending' : 'Completed';
            $mdata = '';
            $lesson_fragment = $lesson_fragment->toArray();
            foreach ($lesson_fragment as $lkey => $lvalue) {
                $lesson_fragment[$lkey]['student_score'] = array_values($studentscore->where('lg_id', $lvalue['lg_id'])->toArray());
            }

            foreach ($lesson_fragment as $key2 => $value2) {
                if (empty($value2['student_score'])) {
                    $mdata .= '<tr>';
                    $mdata .= '<td>' . ($value2['game']['game_name'] ?? '') . '</td>';
                    $mdata .= '<td><span class="badge  badge-danger" style="margin-right:5px">Pending</span></td>';
                    $mdata .= '<td>-</td>';
                    $mdata .= '<td>-</td>';
                    $mdata .= '<td>-</td>';
                    $mdata .= '<td>-</td>';
                    $mdata .= '<td>-</td>';
                    $mdata .= '<td>-</td>';
                    $mdata .= '</tr>';
                } else {
                    //dd($value2['student_score']);
                    foreach ($value2['student_score'] as $key3 => $value3) {
                        $mdata .= '<tr>';
                        if ($key3 == 0) {
                            $mdata .= '<td rowspan="' . count($value2['student_score']) . '">' . ($value2['game']['game_name'] ?? '') . '</td>';
                            $mdata .= '<td rowspan="' . count($value2['student_score']) . '"><span class="badge  badge-success" style="margin-right:5px">Completed</span></td>';
                            // $mdata .= '<td rowspan="' . count($value2['student_score']) . '><a href="/games/grammer-drop/index.html?module_name=' . ($value2['game']['game_name'] ?? '') . '&chapter_id=&amp;student_id=8&amp;lesson_module_id=2756"><span class="badge  badge-danger" style="margin-right:5px">Start Game</span></a></td>';
                        }
                        $mdata .= '<td>' . date('Y-m-d H:i:s', strtotime($value3['created_at'])) . '</td>';
                        $mdata .= '<td> ' . $value3['total_question'] ?? '0' . ' </td>';
                        $mdata .= '<td> ' . $value3['correct_ans'] ?? '0' . ' </td>';
                        $mdata .= '<td> ' . $value3['wrong_ans'] ?? '0' . ' </td>';
                        $mdata .= '<td> ' . $value3['points'] ?? '0' . ' </td>';
                        $mdata .= '<td> ' . $value3['percentage'] ?? '0' . ' </td>';
                        $mdata .= '</tr>';
                    }
                }
            }
            if ($data[$key]['status'] == 'Completed') {
                $data[$key]['status'] = $this->gridlabel('Completed');
            } else {
                $data[$key]['status'] = $this->gridlabel($data[$key]['status'], 'danger');
            }
            unset($data[$key]['lesson']);
            unset($data[$key]['studentscore']);
            $data[$key]['mdata'] = $mdata;
            $data[$key]['actions'] = '';
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
        $student_ids =  array_keys($this->studentOptions);
        $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];
        if (!empty($filterval['filter_student_id']) && $filterval['filter_student_id'] != 'all') {
            $student_ids =  [$filterval['filter_student_id']];
        }


        $class_ids = $this->class_ids;

        if (!empty($filterval['filter_class_id']) && $filterval['filter_class_id'] != 'all') {
            $class_ids =  [$filterval['filter_class_id']];
            //dd($class_ids);
        }

        if (!(!empty($filterval['filter_student_id']) && $filterval['filter_student_id'] != 'all') && !empty($filterval['filter_class_id']) && $filterval['filter_class_id'] != 'all') {
            //dd('called');
            $stIds = AdminUser::whereIn('class_id', [$filterval['filter_class_id']])->pluck('id')->toArray();
            $student_ids = $stIds ?? [];
        }

        $data = LessonStudent::scoreData($student_ids, $class_ids, $filterval);
        $data = $data->whereHas('lesson', function ($q) {
            return $q->where('lesson_type', $this->type)->where('status', 'a');
        });
        if(isset($request->class_id) && $request->class_id >0) {
            $class_id =  $request->class_id;
            $data = $data->whereHas('lesson', function ($q) use($class_id){
                return $q->where('class_id', $class_id);
            });
        }
        $request['sortby'] = $request['sortby'] == 'id' ? 'lesson_student_id' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
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
}
