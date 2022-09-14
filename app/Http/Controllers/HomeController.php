<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
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
use App\Models\LessonFragment;
use App\Models\PointLog;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
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

    function decodeEmoticons($src)
    {
        $replaced = preg_replace("/\\\\u([0-9A-F]{1,4})/i", "&#x$1;", $src);
        $result = mb_convert_encoding($replaced, "UTF-16", "HTML-ENTITIES");
        //$result = mb_convert_encoding($result, 'utf-8', 'utf-16');
        return $result;
    }

    function convert_to($source, $target_encoding)
    {
        // detect the character encoding of the incoming file
        $encoding = mb_detect_encoding($source, "auto");

        // escape all of the question marks so we can remove artifacts from
        // the unicode conversion process
        $target = str_replace("?", "[question_mark]", $source);

        // convert the string to the target encoding
        $target = mb_convert_encoding($target, $target_encoding, $encoding);

        // remove any question marks that have been introduced because of illegal characters
        $target = str_replace("?", "", $target);

        // replace the token string "[question_mark]" with the symbol "?"
        $target = str_replace("[question_mark]", "?", $target);

        return $target;
    }

    function chr_utf8($n, $f = 'C*')
    {
        return $n < (1 << 7) ? chr($n) : ($n < 1 << 11 ? pack($f, 192 | $n >> 6, 1 << 7 | 191 & $n) : ($n < (1 << 16) ? pack($f, 224 | $n >> 12, 1 << 7 | 63 & $n >> 6, 1 << 7 | 63 & $n) : ($n < (1 << 20 | 1 << 16) ? pack($f, 240 | $n >> 18, 1 << 7 | 63 & $n >> 12, 1 << 7 | 63 & $n >> 6, 1 << 7 | 63 & $n) : '')));
    }



    public function savechapterdata()
    {


        ini_set('max_execution_time',  -1);
        $tbl_chapter =  json_decode(file_get_contents("http://devadmin.iadam.kr/public/results.json"), true);
        $final = [];

        foreach ($tbl_chapter as $key => $value) {

            //if (strpos($value['package_ids'], '3') !== false) {
            $cData = [
                'fragment_name' => $value['chapter_name'],
                'school_id' => $value['chapter_id'],
                'package_ids' => explode(',', $value['package_ids']),
                'data_type' => 'lib',
                'categories' => $value['chapter_name'],
            ];

            $categories = explode(' ', $value['chapter_name']);

            $obj = new Fragment();
            $obj->fragment_name = $value['chapter_name'];
            $obj->data_type = 'lib';
            $obj->school_id = '1';
            $obj->package_ids = explode(',', $value['package_ids']);
            $obj->categories = ($categories[0] ?? '') . ' ' . ($categories[1] ?? '');
            $obj->save();
            $obj = $obj->refresh();
            $fragment_id = $obj->fragment_id;
            $list = $this->colList($fragment_id, $value);

            //array_push($final, [$cData, $list]);
            //}
        }

        //return $final;
    }

    function colList($fragment_id, $data)
    {
        $cols = [
            'englishsentence_id' => ['class' => ChapterEnglishSentence::class,  'cols' => ['englishsentence_sentence', 'englishsentence_image_urls', 'englishsentence_korean']],
            'englishword_id' => ['class' => ChapterEnglishWord::class, 'cols' => ['englishword_word', 'englishword_image_urls']],
            'grammer_id' => ['class' => ChapterGrammer::class, 'cols' => ['grammer_english', 'grammer_korean']],
            'qa_id' => ['class' => ChapterQA::class, 'cols' => ['qa_english_question', 'qa_english_answer', 'qa_korean_question', 'qa_korean_answer', 'qa_image_urls']],
            'role_play_id' => ['class' => ChapterRolePlay::class, 'cols' => ['role_play_english_A', 'role_play_english_B', 'role_play_korean_A', 'role_play_korean_B', 'role_play_image_urls']],
            'vocabulary_id' => ['class' => ChapterVocabulary::class, 'cols' => ['vocabulary_english', 'vocabulary_korean', 'vocabulary_image_urls']],
            'multiple_choice_id' => ['class' => ChapterMultipleChoice::class, 'cols' => ['choice_question', 'choice_a', 'choice_b', 'choice_c', 'choice_d', 'choice_e', 'choice_f', 'option_type', 'choice_image_urls']],
            'extra_word_id' => ['class' => ChapterExtrawords::class, 'cols' => ['extrawords_sentence', 'extrawords_word1', 'extrawords_word2', 'extrawords_word3', 'extrawords_word4', 'extrawords_word5', 'extrawords_word6', 'extrawords_word7', 'extrawords_word8', 'extrawords_image_urls']],
            'missing_word_id' => ['class' => ChapterMissingWord::class, 'cols' => ['missing_word_sentences', 'missing_word_image_urls']],
        ];
        $final = [];

        foreach ($cols as $key => $value) {
            if (!empty($data[$key])) {
                $temp = [];
                foreach ($value['cols'] as $ckey => $cvalue) {
                    $temp[$cvalue] = !empty($data[$cvalue]) ? explode('~', $data[$cvalue]) : [];
                }
                $newtemp = [];
                foreach ($temp[$value['cols'][0]] as $ckey => $cvalue) {
                    foreach ($value['cols'] as $ckey2 => $cvalue2) {
                        $newtemp[$ckey][$cvalue2] = $temp[$cvalue2][$ckey] ?? '';
                    }
                }
                $final[$key] = $newtemp;
            }
        }

        foreach ($final as $key => $value) {
            if (!empty($value)) {
                foreach ($value as $ckey => $cval) {
                    $obj = new $cols[$key]['class']();
                    $obj->fragment_id = $fragment_id;
                    foreach ($cval as $ckey2 => $cval2) {
                        $obj->$ckey2 = $cval2;
                    }
                    $obj->save();
                }
            }
        }

        return $final;
    }

    public function saveStudentData()
    {
        ini_set('max_execution_time',  -1);
        $data =  json_decode(file_get_contents("http://devadmin.iadam.kr/public/studentsdata.json"), true);
        $final = [];
        //return $data;

        foreach ($data as $key => $value) {
            $obj = new AdminUser;
            $obj->id = $value['id'];
            $obj->firstname = $value['fname'];
            $obj->lastname = $value['lname'];
            $obj->user_name = $value['user_name'];
            $obj->avatar = '';
            $obj->email = $value['email'];
            $obj->class_id = $value['class_id'];
            $obj->user_type = 'a';
            $obj->admin_role = 'student';
            $obj->created_at = date('Y-m-d H:i:s');
            $obj->updated_at = date('Y-m-d H:i:s');
            $obj->school_id = '1';
            $obj->password = Hash::make('iadam2021');
            $obj->save();

            $cls = ClassTbl::where('class_id', $value['class_id'])->count();
            if ($cls == 0) {
                $cobj = new ClassTbl;
                $cobj->class_id = $value['class_id'];
                $cobj->class_name = $value['class_name'];
                $cobj->school_id = '1';
                $cobj->created_at = date('Y-m-d H:i:s');
                $cobj->updated_at = date('Y-m-d H:i:s');
                $cobj->save();
            }

            $pobj = new PointLog();
            $pobj->class_id = $value["class_id"] ?? '';
            $pobj->student_id = $value["id"] ?? '';
            $pobj->points = $value["achived_points"] ?? 0;
            $pobj->point_type =  'bp';
            $pobj->point_data = [];
            $pobj->created_at = date('Y-m-d H:i:s');
            $pobj->updated_at = date('Y-m-d H:i:s');
            $pobj->save();


            $finalPoints = PointLog::where('student_id', $value["id"])->sum('points');
            $sobj = AdminUser::find($value["id"]);
            $sobj->points = $finalPoints ?? 0;
            $sobj->update();
        }
    }


    public function syncLesson()
    {
        ini_set('max_execution_time',  -1);
        $list = StudentScore::get()->toArray();
        //return $list;
        foreach ($list as $key => $value) {
            $lg = LessonFragment::where('lesson_id', $value['lesson_id'])->where('game_id', $value['game_id'])->first();
            if (!empty($lg)) {
                $obj = StudentScore::find($value['student_score_id']);
                $obj->lg_id = $lg['lg_id'];
                $obj->update();
            }
        }
    }
}


/*

UPDATE `tbl_lesson_fragment` SET lg_id = concat(lesson_fragment_id,'_',lesson_id,'_',game_id)

TRUNCATE `tbl_assigment_lesson`;
TRUNCATE `tbl_assigment_student`;
TRUNCATE `tbl_chapter_englishsentence`;
TRUNCATE `tbl_chapter_englishword`;
TRUNCATE `tbl_chapter_extrawords`;
TRUNCATE `tbl_chapter_grammer`;
TRUNCATE `tbl_chapter_missing_word`;
TRUNCATE `tbl_chapter_multiple_choice`;
TRUNCATE `tbl_chapter_qa`;
TRUNCATE `tbl_chapter_role_play`;
TRUNCATE `tbl_chapter_vocabulary`;
TRUNCATE `tbl_fragment`;


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


   /* UPDATE yourtable SET url = REPLACE(url, '/admin-portal/public/asset/images/uploads/', 'images/package/')


UPDATE tbl_chapter_englishsentence SET englishsentence_image_urls = REPLACE(englishsentence_image_urls, '/admin-portal/public/asset/images/uploads/', 'images/package/');
UPDATE tbl_chapter_englishword SET englishword_image_urls = REPLACE(englishword_image_urls, '/admin-portal/public/asset/images/uploads/', 'images/package/');
UPDATE tbl_chapter_qa SET qa_image_urls = REPLACE(qa_image_urls, '/admin-portal/public/asset/images/uploads/', 'images/package/');
UPDATE tbl_chapter_role_play SET role_play_image_urls = REPLACE(role_play_image_urls, '/admin-portal/public/asset/images/uploads/', 'images/package/');
UPDATE tbl_chapter_vocabulary SET vocabulary_image_urls = REPLACE(vocabulary_image_urls, '/admin-portal/public/asset/images/uploads/', 'images/package/');
UPDATE tbl_chapter_multiple_choice SET choice_image_urls = REPLACE(choice_image_urls, '/admin-portal/public/asset/images/uploads/', 'images/package/');
UPDATE tbl_chapter_extrawords SET extrawords_image_urls = REPLACE(extrawords_image_urls, '/admin-portal/public/asset/images/uploads/', 'images/package/');



// return array_unique($final);
        // return $final;
        // $str = '{"test" : "\u00ec\u2022\u02c6\u00eb\u2026\u2022"}';
        // $str = ['test' => "ë™ì‚¬~í˜„ìž¬~ê³¼ê±°~ë¯¸ëž˜~ì´ë²ˆ~ì§€ë‚œ~ë‹¤ìŒ~*í• ê±°ì•¼~*í•˜ì§€ ì•Šì„ê±°ì•¼~ì¼~ì£¼~ë‹¬~ë…„ë„~í•œ ë²ˆ~ë‘ ë²ˆ~ë²ˆ~ì„¸ ë²ˆ~ë„¤ ë²ˆ~ë§¤ì¼ì˜~ì¼ê³¼~ìžì£¼~ì–¼ë§ˆë‚˜ ìžì£¼~ì„¸ìˆ˜í•˜ë‹¤~ë¨¸ë¦¬ê°ë‹¤~ë¹¨ëž˜í•˜ë‹¤~ì„¸ì°¨í•˜ë‹¤~ì–‘ì¹˜í•˜ë‹¤~ë¹—ì§ˆí•˜ë‹¤"];
        // header('Content-Type: application/json');
        // $final = ['data' => $str, 'status' => !empty($data) ? 'success' : 'fail'];
        // echo json_encode($final, JSON_UNESCAPED_UNICODE);

        //return json_decode($str,  true, 512, JSON_UNESCAPED_UNICODE);
        //echo json_decode('{"t":"\u00ed"}');

        //return $this->getpagedata('home', $request->all());


        function decodeEmoticons($src)
    {
        $replaced = preg_replace("/\\\\u([0-9A-F]{1,4})/i", "&#x$1;", $src);
        $result = mb_convert_encoding($replaced, "UTF-16", "HTML-ENTITIES");
        $result = mb_convert_encoding($result, 'utf-8', 'utf-16');
        return $result;
    }

*/
