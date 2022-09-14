<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Models\AssignTeacher;
use App\Models\Cards;
use App\Models\ChapterEnglishSentence;
use App\Models\ChapterEnglishWord;
use App\Models\ChapterExtrawords;
use App\Models\ChapterGrammer;
use App\Models\ChapterMissingWord;
use App\Models\ChapterMultipleChoice;
use App\Models\ChapterQA;
use App\Models\ChapterRolePlay;
use App\Models\ChapterVocabulary;
use App\Models\ClassTbl;
use App\Models\Fragment;
use App\Models\Lesson;
use App\Models\LessonFragment;
use App\Models\MultiplayerRoom;
use App\Models\MultiplayerRoomStudent;
use App\Models\PointLog;
use App\Models\Purchase;
use App\Models\RelaxationClass;
use App\Models\RelaxationData;
use App\Models\StudentScore;
use App\Models\UnusedItem;
use App\Models\Users;
use Illuminate\Http\Request;
use Hash;

class ScoreController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }


    public function savescore(Request $request)
    {
        $input = $request->all();

        $student = AdminUser::find($input["student_id"]);

        $lg_id = $input["lesson_id"] . '_' . $input["game_id"];
        if (!empty($input['lg_id'])) {
            $lg_id = $input['lg_id'];
        }


        $final = [
            "lesson_id" => $input["lesson_id"],
            "game_id" => $input["game_id"],
            "student_id" => $input["student_id"],
            //"fragment_id" => $input["fragment_id"],
            "total_question" => $input["total_question"],
            "correct_ans" => $input["correct_ans"],
            "wrong_ans" => $input["wrong_ans"],
            "points" => $input["points"],
            "percentage" => $input["percentage"],
            "school_id" => $student["school_id"],
            "class_id" => $student["class_id"],
            "ls_id" => $input["lesson_id"] . '_' . $input["student_id"],
            "lg_id" => $lg_id,
            "point_type" => 'sc',
            //"lesson_fragment_id" => $input["lesson_fragment_id"],
            "score_data" => $input,
        ];

        $obj = new StudentScore();
        foreach ($final as $key => $value) {
            $obj->$key = $value;
        }

        if ($obj->save()) {

            $pobj = new PointLog();
            $pobj->class_id = $student["class_id"] ?? '';
            $pobj->student_id = $input["student_id"] ?? '';
            $pobj->points = $input["points"] ?? 0;
            $pobj->point_type =  'sc';
            $pobj->point_data = $input;
            $pobj->created_at = date('Y-m-d H:i:s');
            $pobj->updated_at = date('Y-m-d H:i:s');
            $pobj->save();


            $finalPoints = PointLog::where('student_id', $input["student_id"])->sum('points');
            $sobj = AdminUser::find($input["student_id"]);
            $sobj->points = $finalPoints;
            $sobj->update();

            return ['status' => 2];
        } else {
            return ['flag' => 1,];
        }
    }


    public function getlessondata(Request $request)
    {
        $input = $request->all();

        $ls = LessonFragment::whereHas('lesson.lesson_student', function ($q) use ($input) {
            return $q->where('lesson_id', $input['lesson_id'])->where('student_id', $input['student_id']);
        })->with(['game'])->where('lesson_id', $input['lesson_id'])->where('game_id', $input['game_id']);
        if (!empty($input['lg_id'])) {
            $ls = $ls->where('lg_id', $input['lg_id']);
        }
        $ls = $ls->first();
        //return $input;

        if (!empty($ls)) {
            $lesson = Lesson::where('lesson_id', $input['lesson_id'])->first()->toArray();
            if ($ls['package_id'] == '5') {
                $frg  = ChapterVocabulary::class;
                $cols = [
                    'vocabulary_english',
                    "vocabulary_korean",
                    "vocabulary_image_urls",
                ];
            } else if ($ls['package_id'] == '2') {
                $frg  = ChapterQA::class;
                $cols = [
                    'qa_english_question',
                    "qa_english_answer",
                    "qa_korean_question",
                    "qa_korean_answer",
                    "qa_image_urls",
                ];
            } else if ($ls['package_id'] == '3') {
                $frg  = ChapterMissingWord::class;
                $cols = [
                    'missing_word_sentences',
                    "missing_word_image_urls",
                ];
            } else if ($ls['package_id'] == '4') {
                $frg  = ChapterRolePlay::class;
                $cols = [
                    'role_play_english_A',
                    'role_play_english_B',
                    'role_play_korean_A',
                    'role_play_korean_B',
                    "role_play_image_urls",
                ];
            } else if ($ls['package_id'] == '6') {
                $frg  = ChapterMultipleChoice::class;
                $cols = [
                    'choice_question',
                    'option_type',
                    'choice_a',
                    'choice_b',
                    'choice_c',
                    'choice_d',
                    'choice_e',
                    'choice_f',
                ];
            } else if ($ls['package_id'] == '7') {
                $frg  = ChapterExtrawords::class;
                $cols = [
                    'extrawords_sentence',
                    'extrawords_word1',
                    'extrawords_word2',
                    'extrawords_word3',
                    'extrawords_word4',
                    'extrawords_word5',
                    'extrawords_word6',
                    'extrawords_word7',
                    'extrawords_word8',
                    'extrawords_image_urls',
                ];
            } else if ($ls['package_id'] == '8') {
                $frg  = ChapterEnglishWord::class;
                $cols = [
                    'englishword_word',
                    "englishword_image_urls",
                ];
            } else if ($ls['package_id'] == '9') {
                $frg  = ChapterEnglishSentence::class;
                $cols = [
                    'englishsentence_sentence',
                    'englishsentence_korean',
                    "englishsentence_image_urls",
                ];
            } else if ($ls['package_id'] == '10') {
                $frg  = ChapterGrammer::class;
                $cols = [
                    'grammer_english',
                    'grammer_korean',
                    'grammer_var1',
                    'grammer_var2',
                    'grammer_var3',
                    'grammer_var4',
                    'grammer_var5',
                    'grammer_var6', 'grammer_var7', 'grammer_var8', 'grammer_var9', 'grammer_var10', 'grammer_var11', 'grammer_var12', 'grammer_var13', 'grammer_var14', 'grammer_var15', 'grammer_var16'
                ];
            }

            if (!empty($frg)) {
                $frg = $frg::whereIn('fragment_id', $ls['fragment_ids'])->get()->toArray();
                //return [$lesson, $ls];
                $final = [
                    "lesson_name" => $lesson['lesson_name'],
                    "lesson_type" => $lesson['lesson_type'],
                    "lesson_points" => $lesson['lesson_points'],
                    "lesson_id" => $lesson['lesson_id'],
                    "game_id" => $ls['game_id'],
                    "lg_id" => $ls['lg_id'],
                    "game_name" => $ls['game']['game_name'] ?? '',
                    //"teacher_id" => $lesson['teacher_id'],
                    "student_id" => $input['student_id'],
                    "status" => '2',
                    "data_count" => count($frg),
                    "is_random" => $ls['is_random'],
                ];
                foreach ($cols as $key => $value) {
                    $temp = array_column($frg, $value);
                    foreach ($temp as $tkey => $tval) {
                        $temp[$tkey] = !empty($tval) ? $tval : '';
                        if (!empty($temp[$tkey]) && in_array($value, ['vocabulary_image_urls', 'qa_image_urls', 'missing_word_image_urls', 'role_play_image_urls', 'extrawords_image_urls', 'englishword_image_urls', 'englishsentence_image_urls'])) {
                            $temp[$tkey] = '/public/' . $temp[$tkey];
                        }

                        if (!empty($temp[$tkey]) && in_array($value, [
                            'grammer_var1', 'grammer_var2', 'grammer_var3', 'grammer_var4', 'grammer_var5', 'grammer_var6', 'grammer_var7', 'grammer_var8', 'grammer_var9', 'grammer_var10', 'grammer_var11', 'grammer_var12', 'grammer_var13', 'grammer_var14', 'grammer_var15', 'grammer_var16'
                        ])) {
                            $temp[$tkey] = implode('~', $temp[$tkey]);
                        }

                        if (!empty($final['option_type'][$tkey]) && $final['option_type'][$tkey] == 3 && in_array($value, ['choice_d', 'choice_e', 'choice_f'])) {
                            $temp[$tkey] = "";
                            //                            dd('cal;ed',$temp,$final['option_type'][$tkey]);
                        }
                    }
                    $final[$value] = $temp;
                }
            } else {
                $final = [
                    "lesson_name" => $lesson['lesson_name'],
                    "lesson_type" => $lesson['lesson_type'],
                    "lesson_points" => $lesson['lesson_points'],
                    "lesson_id" => $lesson['lesson_id'],
                    "game_id" => $ls['game_id'],
                    "is_random" => $ls['is_random'],
                    "lg_id" => $ls['lg_id'],
                    "game_name" => $ls['game']['game_name'] ?? '',
                    //"teacher_id" => $lesson['teacher_id'],
                    "student_id" => $input['student_id'],
                    "status" => '2',
                    "data_count" => 0,
                ];
            }

            if (!empty($final['is_random']) && $final['is_random'] == 'y') {
                $final = $this->shuffleData($ls['package_id'], $final);
            }

            return $final;
        } else {
            return ['status' => 1];
        }
    }


    public function shuffleData($package_id, $arr)
    {
        $final = [];
        // if ($package_id == '5') {
        //     $cols = [
        //         'vocabulary_english',
        //         "vocabulary_korean",
        //         "vocabulary_image_urls",
        //     ];
        //     $mainCol = 'vocabulary_english';
        // }
        if ($package_id == '5') {

            $cols = [
                'vocabulary_english',
                "vocabulary_korean",
                "vocabulary_image_urls",
            ];
            $mainCol = 'vocabulary_english';
        } else if ($package_id == '2') {
            $cols = [
                'qa_english_question',
                "qa_english_answer",
                "qa_korean_question",
                "qa_korean_answer",
                "qa_image_urls",
            ];
            $mainCol = 'qa_english_question';
        } else if ($package_id == '3') {

            $cols = [
                'missing_word_sentences',
                "missing_word_image_urls",
            ];
            $mainCol = 'missing_word_sentences';
        } else if ($package_id == '4') {

            $cols = [
                'role_play_english_A',
                'role_play_english_B',
                'role_play_korean_A',
                'role_play_korean_B',
                "role_play_image_urls",
            ];
            $mainCol = 'role_play_english_A';
        } else if ($package_id == '6') {

            $cols = [
                'choice_question',
                'option_type',
                'choice_a',
                'choice_b',
                'choice_c',
                'choice_d',
                'choice_e',
                'choice_f',
            ];
            $mainCol = 'choice_question';
        } else if ($package_id == '7') {

            $cols = [
                'extrawords_sentence',
                'extrawords_word1',
                'extrawords_word2',
                'extrawords_word3',
                'extrawords_word4',
                'extrawords_word5',
                'extrawords_word6',
                'extrawords_word7',
                'extrawords_word8',
                'extrawords_image_urls',
            ];
            $mainCol = 'extrawords_sentence';
        } else if ($package_id == '8') {

            $cols = [
                'englishword_word',
                "englishword_image_urls",
            ];
            $mainCol = 'englishword_word';
        } else if ($package_id == '9') {

            $cols = [
                'englishsentence_sentence',
                'englishsentence_korean',
                "englishsentence_image_urls",
            ];
            $mainCol = 'englishsentence_sentence';
        } else if ($package_id == '10') {

            $cols = [
                'grammer_english',
                'grammer_korean'
            ];
            $mainCol = 'grammer_english';
        }
        foreach ($arr[$mainCol] as $key => $value) {
            $temp = [];
            foreach ($cols as $ckey => $cval) {
                $temp[$cval] = $arr[$cval][$key] ?? '';
            }
            array_push($final, $temp);
        }
        $arr['temp_arr'] = $final;
        shuffle($final);
        foreach ($cols as $ckey => $cval) {
            $arr[$cval] = array_column($final, $cval);
        }

        return $arr;
    }

    public function redirectUser(Request $request)
    {
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
        $cls = ClassTbl::all()->toArray();
        AssignTeacher::where('teacher_assign_id', '!=', '5a')->delete();
        foreach ($cls as $key => $value) {
            $obj = new AssignTeacher;
            $obj->class_id = $value['class_id'];
            $obj->teacher_id = '1';
            $obj->created_at = date('Y-m-d H:i:s');
            $obj->updated_at = date('Y-m-d H:i:s');
            $obj->save();
        }
    }

    public function saveCard(Request $request)
    {
        $input =  $request->all();


        $student = AdminUser::find($input["student_id"]);
        $data = [
            "class_id" => $student['class_id'] ?? 0,
            "student_id" => $input['student_id'] ?? 0,
            "is_print" => 1,
            "image_state" => $input['image_state'] ?? null,
        ];


        if (isset($input['unused_item'])) {
            $item = UnusedItem::where('student_id', $input['student_id'])->first();
            if (!empty($item)) {
                $item = $item->toArray();
                $iObj = UnusedItem::find($item['unused_item_id']);
                $iObj->unused_item = $input['unused_item'];
                $iObj->update();
            } else {
                $iObj = new UnusedItem();
                $iObj->student_id = $input['student_id'];
                $iObj->unused_item = $input['unused_item'];
                $iObj->save();
            }
        }


        $image = $input['student_id'] . '_' . date('Y_m_d_H_i_s');
        $data['image'] = $this->base64_to_jpeg($input['image'], $image);
        $obj = new Cards();
        //$obj->status = 'd';
        $obj->is_avatar = 'd';
        foreach ($data as $key => $value) {
            $obj->$key = $value;
        }

        if ($obj->save()) {


            $obj = $obj->refresh();
            $input['image'] = $data['image'];

            // $pobj = new PointLog();
            // $pobj->class_id = $student["class_id"] ?? 0;
            // $pobj->student_id = $input["student_id"] ?? '';
            // $pobj->points = $input["points"] ?? 0;
            // $pobj->ref_id = $obj["card_id"] ?? '';
            // $pobj->point_type =  'mh';
            // $pobj->point_data = $input;
            // $pobj->save();


            // $finalPoints = PointLog::where('student_id', $input["student_id"])->sum('points');
            // $sobj = AdminUser::find($input["student_id"]);
            // $sobj->points = $finalPoints;
            // $sobj->update();


            return ['status' => 2];
        } else {
            return ['flag' => 1];
        }

        return $request->all();
    }


    function base64_to_jpeg($base64_string, $output_file)
    {
        // open the output file for writing
        $ext = 'png';
        $dir = 'card';
        if (strpos($base64_string, 'image/jpeg') !== false) {
            $ext = 'jpg';
        } else if (strpos($base64_string, 'image/png') !== false) {
            $ext = 'png';
        }
        $output_file = $output_file . '.' . $ext;

        $path = '/images/card/' . $output_file;
        $destinationPath = str_replace('/', DIRECTORY_SEPARATOR, base_path('public/images/card/'));
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 777);
        }
        $destinationPath = $destinationPath . '' . $output_file;
        //dd($destinationPath);
        $ifp = fopen($destinationPath, 'wb');



        // $upload_dir =  'images/card';
        // $upload_path = str_replace('/', DIRECTORY_SEPARATOR, base_path('public/' . $upload_dir));
        // $result_path = url($upload_dir) . '/';




        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode(',', $base64_string);

        // we could add validation here with ensuring count( $data ) > 1
        fwrite($ifp, base64_decode($data[1]));

        // clean up the file resource
        fclose($ifp);


        $filename = $destinationPath;

        // Load the image
        $source = imagecreatefrompng($filename);

        // Rotate
        $rotate = imagerotate($source, -90, 0);

        //and save it on your server...
        imagepng($rotate, $destinationPath);

        return $path;
    }

    public function getPonits(Request $request)
    {

        // $id = getUrlParm('id');
        // $id = $id ?? 0;
        // $data = selectOne('SELECT st.id, SUM(pt.points) as total_points FROM `admins` st LEFT JOIN tbl_point_log pt ON st.id = pt.student_id AND st.class_id = pt.class_id WHERE st.id = ? GROUP BY st.id', [$id]);


        // $uitem = selectOne("SELECT * FROM `tbl_unused_item` WHERE student_id = ?", [$id]);
        // if (!isset($uitem['unused_item_id'])) {
        //     $uitem['unused_item'] = "energy^5^speed^5^magic^5^attack^5^brains^5^shield^5^";
        // }

        // if (is_null($uitem['unused_item'])) {
        //     $uitem['unused_item'] = "";
        // }

        $input =  $request->all();


        $student = AdminUser::where('id', $input["student_id"])->with(['unusedItem'])->first();

        if (empty($student)) {
            return ['status' => 1];
        }

        $student = $student->toArray();


        $unusedItem = !empty($student['unused_item']['unused_item']) ? $student['unused_item']['unused_item'] : "energy^5^speed^5^magic^5^attack^5^brains^5^shield^5^";
        //return $student['unused_item']['unused_item'];



        return ['status' => 2, 'point' => (($student['points'] ?? 0) * 1), "unused_item" => $unusedItem,  'msg' => 'Points retrive successfully.'];



        // $id = getUrlParm('id');
        // $id = $id ?? 0;
        // $data = selectOne('SELECT st.id, SUM(pt.points) as total_points FROM `admins` st LEFT JOIN tbl_point_log pt ON st.id = pt.student_id AND st.class_id = pt.class_id WHERE st.id = ? GROUP BY st.id', [$id]);
        // header('Content-Type: application/json');

        // $uitem = selectOne("SELECT * FROM `tbl_unused_item` WHERE student_id = ?", [$id]);
        // if (!isset($uitem['unused_item_id'])) {
        //     $uitem['unused_item'] = "energy^5^speed^5^magic^5^attack^5^brains^5^shield^5^";
        // }

        // if (is_null($uitem['unused_item'])) {
        //     $uitem['unused_item'] = "";
        // }

        // echo json_encode(['status' => true, 'point' => (($data['total_points'] ?? 0) * 1), "unused_item" => $uitem['unused_item'],  'msg' => $this->modeltitle . ' record retrive successfully.']);
    }

    public function saveUnusedItem(Request $request)
    {
        $input = $request->all();

        if (isset($input['unused_item'])) {
            $item = UnusedItem::where('student_id', $input['student_id'])->first();
            if (!empty($item)) {
                $item = $item->toArray();
                $iObj = UnusedItem::find($item['unused_item_id']);
                $iObj->unused_item = $input['unused_item'];
                $iObj->update();
            } else {
                $iObj = new UnusedItem();
                $iObj->student_id = $input['student_id'];
                $iObj->unused_item = $input['unused_item'];
                $iObj->save();
            }
        }

        return ['status' => 2];
    }

    public function purchaseItem(Request $request)
    {
        $input = $request->all();

        $probj = new Purchase();

        $probj->class_id =   $input['class_id'] ?? 0;
        $probj->student_id =   $input['student_id'] ?? 0;
        $probj->price =   ($input['points'] ?? 0) * -1;
        $probj->item_category =   $input['item_category'] ?? '';
        $probj->item_name =   $input['item_name'] ?? '';

        //$id = lastInsertId('tbl_purchase', $data);
        if ($probj->save()) {

            $probj = $probj->refresh();

            $pobj = new PointLog();
            $pobj->class_id = $student["class_id"] ?? 0;
            $pobj->student_id = $input["student_id"] ?? '';
            $pobj->points = $input["points"] ?? 0;
            $pobj->ref_id = $probj["purchase_id"] ?? '';
            $pobj->point_type =  'mhp';
            $pobj->point_data = $input;
            $pobj->save();

            PointLog::updatePoints($input["student_id"]);

            return ['status' => true, 'msg' =>  'Item Purchase successfully.'];
        } else {
            return ['status' => false, 'msg' => 'Item Purchase failed.'];
        }
    }

    public function getfragmentdata(Request $request)
    {
        $input = $request->all();
        $frg = Fragment::with(['qa', 'vocabulary', 'english_sentences'])->where('fragment_id', $input['fragment_id'])->first();

        if (empty($frg)) {
            return ['status' => 1];
        }
        $frg = $frg = $frg->toArray();
        $final = ['fragment_id' => $input['fragment_id']];

        $cols = [
            'qa_english_question',
            "qa_english_answer",
            "qa_korean_question",
            "qa_korean_answer",
            "qa_image_urls",
        ];
        $final = $this->fillData($frg['qa'] ?? [], $cols, $final);
        $cols = [
            'vocabulary_english',
            "vocabulary_korean",
            "vocabulary_image_urls",
        ];
        $final = $this->fillData($frg['vocabulary'] ?? [], $cols, $final);
        $cols = [
            'englishsentence_sentence',
            'englishsentence_korean',
            "englishsentence_image_urls",
        ];
        $final = $this->fillData($frg['english_sentences'] ?? [], $cols, $final);

        return $final;
    }


    public function getExtraTrainingData(Request $request)
    {
        $input = $request->all();

        $final = RelaxationClass::where('game_id', $input['game_id'])->where('class_id', $input['class_id'])->first();
        if (empty($final)) {
            return ['status' => 1];
        }

        if (!empty($final['fragment_id'])) {

            $frg = Fragment::with(['qa', 'vocabulary', 'english_sentences', 'grammer'])->where('fragment_id', $final['fragment_id'])->first();

            if (empty($frg)) {
                return ['status' => 1];
            }
            $frg = $frg->toArray();
            //$final = ['fragment_id' => $input['fragment_id']];
            $final["qa_count"] = count($frg['qa'] ?? []);
            if ($final["qa_count"] > 0) {
                $cols = [
                    'qa_english_question',
                    "qa_english_answer",
                    "qa_korean_question",
                    "qa_korean_answer",
                    "qa_image_urls",
                ];
                $final = $this->fillData($frg['qa'] ?? [], $cols, $final);
            }
            $final["vocabulary_count"] = count($frg['vocabulary'] ?? []);
            if ($final["vocabulary_count"] > 0) {
                $cols = [
                    'vocabulary_english',
                    "vocabulary_korean",
                    "vocabulary_image_urls",
                ];
                $final = $this->fillData($frg['vocabulary'] ?? [], $cols, $final);
            }
            $final["englishsentence_count"] = count($frg['english_sentences'] ?? []);
            if ($final["englishsentence_count"] > 0) {
                $cols = [
                    'englishsentence_sentence',
                    'englishsentence_korean',
                    "englishsentence_image_urls",
                ];
                $final = $this->fillData($frg['english_sentences'] ?? [], $cols, $final);
            }
            $final["grammer_count"] = count($frg['grammer'] ?? []);
            if ($final["grammer_count"] > 0) {
                $cols = [
                    'grammer_english',
                    'grammer_korean',
                    'grammer_var1',
                    'grammer_var2',
                    'grammer_var3',
                    'grammer_var4',
                    'grammer_var5',
                    'grammer_var6', 'grammer_var7', 'grammer_var8', 'grammer_var9', 'grammer_var10', 'grammer_var11', 'grammer_var12', 'grammer_var13', 'grammer_var14', 'grammer_var15', 'grammer_var16'
                ];
                $final = $this->fillData($frg['grammer'] ?? [], $cols, $final);
            }
        } else {
            $final["englishsentence_count"] = count($frg['english_sentences'] ?? []);
            $final["vocabulary_count"] = count($frg['vocabulary'] ?? []);
            $final["qa_count"] = count($frg['qa'] ?? []);
            $final["grammer_count"] = count($frg['grammer'] ?? []);
        }

        return $final;
    }


    function fillData($input, $cols, $final)
    {
        foreach ($cols as $key => $value) {
            $temp = array_column($input, $value);
            foreach ($temp as $tkey => $tval) {
                $temp[$tkey] = !empty($tval) ? $tval : '';
                if (!empty($temp[$tkey]) && in_array($value, ['vocabulary_image_urls', 'qa_image_urls', 'missing_word_image_urls', 'role_play_image_urls', 'extrawords_image_urls', 'englishword_image_urls', 'englishsentence_image_urls'])) {
                    $temp[$tkey] = '/public/' . $temp[$tkey];
                }

                if (!empty($temp[$tkey]) && in_array($value, [
                    'grammer_var1', 'grammer_var2', 'grammer_var3', 'grammer_var4', 'grammer_var5', 'grammer_var6', 'grammer_var7', 'grammer_var8', 'grammer_var9', 'grammer_var10', 'grammer_var11', 'grammer_var12', 'grammer_var13', 'grammer_var14', 'grammer_var15', 'grammer_var16'
                ])) {
                    $temp[$tkey] = implode('~', $temp[$tkey]);
                }

                if (!empty($final['option_type'][$tkey]) && $final['option_type'][$tkey] == 3 && in_array($value, ['choice_d', 'choice_e', 'choice_f'])) {
                    $temp[$tkey] = "";
                }
            }
            $final[$value] = $temp;
        }
        return $final;
    }

    public function deactiveroom(Request $request)
    {
        $input = $request->all();
        $room = MultiplayerRoom::find($input['room_id']);

        if (empty($room)) {
            return ['status' => 'fail'];
        }

        $room->status  = 'd';
        $room->update();
        return ["status" => "success"];
    }

    public function saveroomdata(Request $request)
    {
        $input = $request->all();
        $obj = new MultiplayerRoomStudent();
        $obj->fragment_id = $input['fragment_id'] ?? '';
        $obj->class_id = $input['class_id'] ?? '';
        $obj->room_id = $input['room_id'] ?? '';
        $obj->student_id = $input['student_id'] ?? '';
        $obj->student_data = $input['student_data'] ?? '';
        $obj->save();
        return ["status" => "success"];
    }

    public function multiroomPoints(Request $request)
    {
        $input = $request->all();


        $pobj = new PointLog();
        $pobj->class_id = $student["class_id"] ?? 0;
        $pobj->student_id = $input["student_id"] ?? '';
        $pobj->points = $input["points"] ?? 0;
        $pobj->ref_id = '';
        $pobj->point_type =  'mrp';
        $pobj->point_data = $input;
        $pobj->save();
        PointLog::updatePoints($input["student_id"]);

        return ['status' => true, 'msg' =>  'Item Purchase successfully.'];
    }

    public function saveRelaxationData(Request $request)
    {
        $input = $request->all();

        if (isset($input['score']) && $input['score'] != -1) {
            $robj = new RelaxationData();
            $robj->class_id = $input["class_id"] ?? 0;
            $robj->student_id = $input["student_id"] ?? 0;
            $robj->student_id = $input["student_id"] ?? 0;
            $robj->game_id = $input["game_id"] ?? 0;
            $robj->relaxation_class_id = $input["relaxation_class_id"] ?? 0;
            $robj->score = $input["score"] ?? 0;
            $robj->points = $input["points"] ?? 0;
            $robj->save();
        }

        if (isset($input['points']) && $input['points'] > 0) {
            $pobj = new PointLog();
            $pobj->class_id = $input["class_id"] ?? 0;
            $pobj->student_id = $input["student_id"] ?? '';
            $pobj->points = $input["points"] ?? 0;
            $pobj->ref_id = $input["relaxation_class_id"] ?? 0;
            $pobj->point_type =  'rxd';
            $pobj->point_data = $input;
            $pobj->save();
            PointLog::updatePoints($input["student_id"]);
        }

        return ['status' => true, 'msg' =>  'Relaxation Data saved successfully.'];
    }
}
