<?php

namespace App\Http\Controllers\Admin;

use App\Models\OfflineHomework;
use App\Http\Controllers\Controller;
use App\Models\ClassTbl;
use App\Models\AdminUser;
use App\Models\LessonStudent;
use App\Models\AssignTeacher;
use App\Models\School;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use App\User;
use App\Models\Recordinglessonstudent;
use App\Models\AssigmentLesson;

class DashboardController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = LessonStudent::class;
        $this->user = $_SESSION['user'] ?? [];
        $this->slug = 'Speaches Lab';
        $this->gridCol = ['lesson_name' => 'Lesson Name', 'total_student' => 'Total Student', 'pending_lesson' => 'Pending', 'completed_lesson' => 'Completed'];
    }
    public function get_griddata($request, $gridCol, $mperm)
    {
        $final = [];
        $user = $_SESSION['user'] ?? [];
        $class_ids = AssignTeacher::where('teacher_id', $user['id'])->pluck('class_id')->toArray(); //410976
        $class_ids = $class_ids ?? [0];
        $student_ids = AdminUser::whereIn('class_id', $class_ids)->pluck('id')->toArray();
        $student_ids = $student_ids  ?? [0];
        $filterval =  [];
        $ldata = LessonStudent::scoreData($student_ids, $class_ids, $filterval);
        $ldata = $ldata->whereHas('lesson', function ($q) {
            return $q->where('lesson_type', 'ls');
        });
        $request['sortby'] = $request['sortby'] == 'id' ? 'display_order' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            //$data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $ldata->paginate($request['limit'])->toArray();

        $data['data'] = $this->formateLesson($data['data']);

        return $data;
    }

    function formateLesson($data)
    {
        foreach ($data as $key => $value) {
            $data[$key]['lesson_name'] = $value['lesson']['lesson_name'] ?? '';
            $lesson_fragment = collect($value['lesson']['lesson_fragment'] ?? []);
            $studentscore = collect($value['studentscore'] ?? []);
            $data[$key]['total_modules'] = $lesson_fragment->count();
            $data[$key]['completed_modules'] = $studentscore->groupBy('lg_id')->count();
            $data[$key]['pending_modules'] = $data[$key]['total_modules'] - $data[$key]['completed_modules'];
        }
        $final = [];

        foreach ($data as $key => $value) {
            if (empty($final[$value['lesson_id']])) {
                $final[$value['lesson_id']] = [
                    'lesson_name' => $value['lesson_name'],
                    'total_student' => 0,
                    'completed_lesson' => 0,
                    'pending_lesson' => 0,
                ];
            }

            $final[$value['lesson_id']]['total_student'] = $final[$value['lesson_id']]['total_student'] + 1;
            if ($value['pending_modules'] == 0) {
                $final[$value['lesson_id']]['completed_lesson'] = $final[$value['lesson_id']]['completed_lesson'] + 1;
            } else {
                $final[$value['lesson_id']]['pending_lesson'] = $final[$value['lesson_id']]['pending_lesson'] + 1;
            }
        }

        return array_values($final);
    }

    public function dashboard_data()
    {
        $this->user = $_SESSION['user'] ?? [];
        $student_id = $this->user['id'];
        $student_ids = [$this->user['id'] ?? 0] ?? [0];
        $class_ids = [$this->user['class_id'] ?? 0] ?? [0];
        $student_completed_home_work = $this->pending_home_works('ls', $student_ids, $class_ids);

        $student_data = User::where('id', $student_id)->first();
        $class_data = ClassTbl::where('class_id', $this->user['class_id'])->first();
        $school_data = School::where('school_id', $this->user['school_id'])->first();
        $data_xaxis = [
            $student_data->firstname . ' ' . $student_data->lastname,
            $class_data->class_name,
            $school_data->school_name,
        ];
        //get the class data 
        $class_students = User::whereIn('class_id', $class_ids)->where('id', '!=', $student_id)->get();
        $studen_for_class = [];
        foreach ($class_students as $classdata) {
            $studen_for_class[] = $classdata->id;
        }
        $class_completed_home_work = $this->pending_home_works('ls', $studen_for_class, $class_ids);

        $school_students = User::where('admin_role', 'student')->where('school_id', $this->user['school_id'])->where('id', '!=', $student_id)->get();
        $studen_for_school = [];
        $class_for_school = [];
        foreach ($school_students as $school) {
            $studen_for_school[] = $school->id;
            $class_for_school[] = $school->class_id;
        }
        $school_completed_home_work = $this->pending_home_works('ls', $studen_for_school, $class_for_school);
        $data_yaxis = [
            count($student_completed_home_work),
            count($class_completed_home_work),
            count($school_completed_home_work),
        ];

        //Get correct answer for the homework
        $streak_count_class = [];
        foreach ($class_students as $key => $class) {
            $streak = $this->getSteakCountData($class->id);

            $streak_count_class[] = [
                'streak' => $streak,
                'name' => $class->firstname . ' ' . $class->lastname,
            ];
        }
        $streak_count_school = [];
        foreach ($school_students as $key => $school) {
            $streak = $this->getSteakCountData($school->id);

            $streak_count_school[] = [
                'streak' => $streak,
                'name' => $school->firstname . ' ' . $school->lastname,
            ];
        }
        $this->array_sort_by_column($streak_count_class, 'streak', SORT_DESC);
        $this->array_sort_by_column($streak_count_school, 'streak', SORT_DESC);
        $streak_count_class = array_slice($streak_count_class, 0, 5, true);
        $streak_count_school = array_slice($streak_count_school, 0, 5, true);


        $correctanswer = $this->getHomeWorkCorrectAnswer();

        $data = [
            'streak_count_school' => $streak_count_school,
            'streak_count_class' => $streak_count_class,
            'xaxis' => $data_xaxis,
            'yaxis' => $data_yaxis,
            'correctanswer' => $correctanswer,
        ];
        return response()->json($data);
    }
    public function array_sort_by_column(&$array, $column, $direction = SORT_ASC)
    {
        $reference_array = array();

        foreach ($array as $key => $row) {
            $reference_array[$key] = $row[$column];
        }

        array_multisort($reference_array, $direction, $array);
    }
    public function getSteakCountData($user_id)
    {
        $scoreDate = \DB::select("SELECT COUNT(student_score_id) as score_count,  DATE_FORMAT(`created_at`,'%Y-%m-%d') as score_date
        FROM tbl_student_score 
        where DATE_FORMAT(`created_at`,'%Y-%m-%d') <= '" . date('Y-m-d', strtotime("-1 days")) . "' and percentage >= 70 and student_id = " . $user_id . "
        GROUP BY score_date
        HAVING score_count > 4
        ORDER BY created_at DESC
        LIMIT 1000");

        $scoreDate = collect($scoreDate);
        $scoreDate = $scoreDate->pluck("score_date")->toArray();
        $dateList = [];
        for ($i = 1; $i < 1000; $i++) {
            $dt = date('Y-m-d', strtotime("-" . $i . " days"));
            array_push($dateList, $dt);
        }


        $err = 0;
        $streak_count = 0;
        foreach ($dateList as $key => $value) {
            if (!in_array($value, $scoreDate)) {
                $err++;
            }
            if ($err == 0) {
                $streak_count++;
            }
        }

        $today_score = \DB::selectOne("SELECT COUNT(student_score_id) as today_score, DATE_FORMAT(`created_at`,'%Y-%m-%d') FROM `tbl_student_score` WHERE DATE_FORMAT(`created_at`,'%Y-%m-%d') = '" . date('Y-m-d', strtotime("-1 days")) . "' and percentage >= 0 and student_id = " . $user_id . "");

        $today_score = collect($today_score)->toArray();

        $today_score = $today_score['today_score'] ?? 0;
        $streak_count = $streak_count + ($today_score > 4 ? 1 : 0);
        return $streak_count;
    }

    public function getHomeWorkCorrectAnswer()
    {

        $student_ids = [$this->user['id'] ?? 0] ?? [0];
        $class_ids = [$this->user['class_id'] ?? 0] ?? [0];

        $filterval =  [];
        $type = "ls";
        $data = LessonStudent::scoreData($student_ids, $class_ids, $filterval);
        $data = $data->whereHas('lesson', function ($q) use ($type) {
            return $q->where('lesson_type', $type);
        });


        $data->orderBy('lesson_id', 'desc');
        $data = $data->get()->toArray();

        $correct_ans = 0;
        foreach ($data as $key => $value) {
            $data[$key]['lesson_name'] = $value['lesson']['lesson_name'] ?? '';
            //$lesson_fragment = collect($value['lesson']['lesson_fragment'] ?? []);
            $lesson_fragment = $value['lesson']['lesson_fragment'] ?? [];
            foreach ($lesson_fragment as $tmkey => $tmvalue) {
                if (empty($tmvalue['game'])) {
                    unset($lesson_fragment[$tmkey]);
                }
            }
            $lesson_fragment = collect($lesson_fragment);
            $lg_ids = $lesson_fragment->pluck('lg_id')->toArray();
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
            //$data[$key]['lesson_new'] = $lesson_fragment;


            foreach ($lesson_fragment as $key2 => $value2) {
                $link = $value2['game']['game_URL'] . "?student_id=" . $this->user['id'] . "&lesson_id=" . $value['lesson']['lesson_id'] . "&game_id=" . $value2['game']['game_id'] . "&lg_id=" . $value2['lg_id'];

                if ($type == 'ex') {

                    if (empty($value2['student_score'])) {
                        $mdata .= '<tr>';
                        $mdata .= '<td>' . ($value2['game']['game_name'] ?? '') . '</td>';
                        $mdata .= '<td><a href="' . $link . '"><span class="badge  badge-danger" style="margin-right:5px">Start ' . __('title.game') . '</span></a></td>';
                        $mdata .= '<td>-</td>';
                        $mdata .= '</tr>';
                    } else {
                        foreach ($value2['student_score'] as $key3 => $value3) {
                            $mdata .= '<tr data-tr="' . $key3 . '" >';
                            $mdata .= '<td>' . ($value2['game']['game_name'] ?? '') . '</td>';
                            $mdata .= '<td><span class="badge  badge-success" style="margin-right:5px">Completed</span></td>';
                            $mdata .= '<td>' . date('Y-m-d H:i:s', strtotime($value3['created_at'])) . '</td>';
                            $mdata .= '</tr>';
                        }
                    }
                } else {
                    if (empty($value2['student_score'])) {
                        $mdata .= '<tr>';
                        $mdata .= '<td>' . ($value2['game']['game_name'] ?? '') . '</td>';
                        $mdata .= '<td><a href="' . $link . '"><span class="badge  badge-danger" style="margin-right:5px">Start ' . __('title.game') . '</span></a></td>';
                        $mdata .= '<td>-</td>';
                        $mdata .= '<td>-</td>';
                        $mdata .= '<td>-</td>';
                        $mdata .= '<td>-</td>';
                        $mdata .= '<td>-</td>';
                        $mdata .= '<td>-</td>';
                        $mdata .= '</tr>';
                    } else {
                        foreach ($value2['student_score'] as $key3 => $value3) {
                            $mdata .= '<tr data-tr="' . $key3 . '" >';
                            if ($key3 == 0) {
                                $mdata .= '<td rowspan="' . count($value2['student_score']) . '">' . ($value2['game']['game_name'] ?? '') . '</td>';
                                $mdata .= '<td rowspan="' . count($value2['student_score']) . '"><a href="' . $link . '"><span class="badge  badge-danger" style="margin-right:5px">Start ' . __('title.game') . '</span></a></td>';
                            }
                            $correct_ans = $correct_ans + $value3['correct_ans'];
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

        return $correct_ans;
    }

    public function pending_home_works($type, $student_ids, $class_ids)
    {
        $filterval =  [];
        $from_date = date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
        $to_date = date('Y-m-d');

        $data = LessonStudent::scoreData($student_ids, $class_ids, $filterval);
        $data = $data->whereHas('lesson', function ($q) use ($type) {
            return $q->where('lesson_type', $type);
        });
        $data->whereDate('created_at', '>', $from_date);
        $data->whereDate('created_at', '<=', $to_date);
        $data = $data->paginate(100000)->toArray();

        $pending_status = [];

        foreach ($data['data'] as $home_data) {
            $lesson_fragment = $home_data['lesson']['lesson_fragment'] ?? [];
            $studentscore = collect($home_data['studentscore'] ?? []);
            $lesson_fragment = collect($lesson_fragment);
            $lg_ids = $lesson_fragment->pluck('lg_id')->toArray();
            $total_modules = $lesson_fragment->count();
            $completed_modules = $studentscore->whereIn('lg_id', $lg_ids)->groupBy('lg_id')->count();
            $pending_modules = $total_modules - $completed_modules;

            if ($pending_modules == 0) {
                $pending_status[] = ['1'];
            }
        }
        return $pending_status;
    }
}
