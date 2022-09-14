<?php

namespace App\Http\Controllers\Admin;

use App\Models\ClassTbl;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\School;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use \App\Common;
use App\Models\LessonStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentLessonScoreController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->type = isset($_GET['type'])  ? $_GET['type'] : 'ls';
        $this->model = Lesson::class;
        $this->model = LessonStudent::class;
        $this->mTitle = 'Class';
        $this->slug = 'studentlessonscore';
        $this->pk = 'class_id';
        $this->gridCol = ['lesson_id' => 'Id',  'lesson_name' => 'Lesson Name', 'total_modules' => 'Total Modules', "pending_modules" => 'Pending Modules', 'completed_modules' => 'Completed Modules', 'status'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->schoolOptions = School::pluck('school_name', 'school_id')->toArray();
        $this->forms = [
            'class_name' => ['rules' => 'required|maxlength:255'],
        ];

        $statusOptions = ['c' => 'Completed', 'p' => 'Pending'];
        $this->filters = [
            'lesson_name' => ["width" => 8, 'title' => 'Lesson Name', 'placeholder' => 'Seac'],
            'status' => ['type' => 'select', "width" => 4, 'title' => 'Status', 'options' => $statusOptions],
        ];
        $this->user = $_SESSION['user'] ?? [];
        $this->school_id = $this->user['school_id'] ?? null;
        $this->ignoresort = ['lesson_id', 'lesson_name', 'total_modules', 'pending_modules', 'completed_modules', 'status'];
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
		    $data[$key]['lesson_name'] = $value['lesson']['lesson_name'] ?? '';
            $target_percentage = $value['lesson']['target_percentage'];
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
            $mdataArray = [];
            $lesson_fragment = $lesson_fragment->toArray();
            foreach ($lesson_fragment as $lkey => $lvalue) {
				
                $lesson_fragment[$lkey]['student_score'] = array_values($studentscore->where('lg_id', $lvalue['lg_id'])->toArray());
            }
            //$data[$key]['lesson_new'] = $lesson_fragment;
			
            foreach ($lesson_fragment as $key2 => $value2) {
                $link = $value2['game']['game_URL'] . "?student_id=" . $this->user['id'] . "&lesson_id=" . $value['lesson']['lesson_id'] . "&game_id=" . $value2['game']['game_id'] . "&lg_id=" . $value2['lg_id'];
								
                if ($this->type == 'challenges') {
					if (empty($value2['student_score'])) {
						$mdataArray [] = [
							'game_name'=>$value2['game']['game_name'] ?? '',
							'link'=>$link,
							'status'=>'pending',
						];
					} else {
						
						$is_completed = "0";
						foreach ($value2['student_score'] as $key3 => $value3) {
							if($value3['percentage']>=$target_percentage) {
								$is_completed = "1";
							}
					    }
						if($is_completed==='0') {
							$mdataArray [] = [
								'game_name'=>$value2['game']['game_name'] ?? '',
								'link'=>$link,
								'status'=>'pending',
							];
						} else {
							$mdataArray [] = [
								'game_name'=>$value2['game']['game_name'] ?? '',
								'link'=>'#',
								'status'=>'compeleted',
							];
						}
						
					}
				}
				else if ($this->type == 'ex') {

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
            $data[$key]['mdataArray'] = $mdataArray;
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
        $this->user = $_SESSION['user'] ?? [];


        $student_ids = [$this->user['id'] ?? 0] ?? [0];
        $class_ids = [$this->user['class_id'] ?? 0] ?? [0];

        $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];
        $data = LessonStudent::scoreData($student_ids, $class_ids, $filterval);
        $data = $data->whereHas('lesson', function ($q) {
            return $q->where('lesson_type', $this->type)->where('status', 'a');
        });

        $request['sortby'] = $request['sortby'] == 'id' ? 'lesson_student_id' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            //$data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data->orderBy('lesson_id', 'desc');
        $data = $data->paginate($request['limit'])->toArray();
		
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }


    public function griddata(Request $request)
    {
        $mperm = Common::user_roles();
        $gridCol = $this->gridCol($request);
        // if (!$this->can_show($mperm)) {
        //     return ['flag' => 2];
        // }

        $scoreDate = DB::select("SELECT COUNT(student_score_id) as score_count, substring(created_at,1,10) as score_date
        FROM tbl_student_score 
        where created_at < CURDATE() and percentage >= 70 and student_id = " . $_SESSION['user']['id'] . "
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

        $today_score = DB::selectOne("SELECT COUNT(student_score_id) as today_score FROM `tbl_student_score` WHERE DATE(`created_at`) = CURDATE() and percentage >= 70 and student_id = " . $_SESSION['user']['id'] . "");
        $today_score = collect($today_score)->toArray();
        $today_score = $today_score['today_score'] ?? 0;
        $streak_count = $streak_count + ($today_score > 4 ? 1 : 0);
        //dd($today_score);


        return [
            'flag' => 1,
            'gridCol' => $gridCol,
            'griddata' => $this->get_griddata($request, $gridCol, $mperm),
            'bulkactions' => $this->bulkactions(),
            'filtersinputs' => $this->filtersinputs($request),
            'can_add' => $this->can_add($mperm),
            'can_bulk' => $this->can_bulk($mperm),
            'can_export' => $this->can_export($mperm),
            'admin_role' => session('admin_role'),
            "streak_count" => $streak_count,
            "dateList" => $dateList,
            'scoreDate' => $scoreDate,
            'today_score' => $today_score
            //'get_where' => $this->get_where($request, $gridCol)
        ];
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
/*
SELECT * FROM `tbl_chapter` as chp
    left join tbl_chapter_englishsentence as englishsentence on chp.chapter_id = englishsentence.chapter_id
    left join tbl_chapter_englishword as englishword on chp.chapter_id = englishword.chapter_id
    left join tbl_chapter_extrawords as extrawords on chp.chapter_id = extrawords.chapter_id
    left join tbl_chapter_grammer as grammer on chp.chapter_id = grammer.chapter_id
    left join tbl_chapter_missing_word as missing_word on chp.chapter_id = missing_word.chapter_id
    left join tbl_chapter_multiple_choice as multiple_choice on chp.chapter_id = multiple_choice.chapter_id
    left join tbl_chapter_qa as qa on chp.chapter_id = qa.chapter_id
    left join tbl_chapter_role_play as role_play on chp.chapter_id = role_play.chapter_id
    left join tbl_chapter_vocabulary as vocabulary on chp.chapter_id = vocabulary.chapter_id
    WHERE chp.created_at >= '2020-04-01 00:00:00' */