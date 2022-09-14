<?php

namespace App\Http\Controllers\Admin;

use App\Models\Fragment;
use App\Http\Controllers\Controller;
use App\Models\ChapterEnglishSentence;
use App\Models\ChapterQA;
use App\Models\ChapterRolePlay;
use App\Models\ChapterVocabulary;
use App\Models\ChapterMultipleChoice;
use App\Models\ChapterExtrawords;
use App\Models\ChapterEnglishWord;
use App\Models\ChapterMissingWord;
use App\Models\ClassTbl;
use App\Models\Package;
use App\Models\Level;
use App\Models\School;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use Illuminate\Http\Request;
use \App\Common;
use App\Models\ChapterGrammer;
use DB;

class FragmentController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = Fragment::class;
        $this->mTitle = 'Fragments';
        $this->slug = 'fragment';
        $this->pk = 'fragment_id';


        $this->user = $_SESSION['user'] ?? [];
        $this->school_id = $this->user['school_id'] ?? null;
        $this->type = !empty($_GET['type']) ? $_GET['type'] : 'frg';
        //dd($_GET['type']);

        // $this->gridCol = ['order_action' => '#', 'display_order' => 'ID','fragment_id' => 'Id',  'fragment_name' => 'fragment Name', "fragment_type" => 'Fragment Type', "level_id" => 'Level',   'created_at' => 'Created At'];
        $this->gridCol = ['order_action' => '#', 'display_order' => 'ID',   'fragment_name' => 'fragment Name',  "level_id" => 'Level',   'created_at' => 'Created At'];
        $this->viewCol = ['fragment_id', 'fragment_name', 'principal_name',   'created_at' => 'Created At'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->typeoptions = ['1' => 'Lesson', '2' => 'Exam'];
        $this->classOptions = ClassTbl::pluck('class_name', 'class_id')->toArray();

        $this->levelOptions = Level::where('school_id', $this->school_id)->pluck('level_name', 'level_id')->toArray();

        if ($this->type == 'allin') {
            $this->packageOptions = Package::whereIn('slug', ['qa', 'vocabulary', 'english_sentences'])->pluck('package_name', 'package_id')->toArray();
        } else {
            $this->packageOptions = Package::whereNotIn('slug', ['role_play', 'english_words'])->pluck('package_name', 'package_id')->toArray();
        }
        $this->forms = [
            'fragment_name' => ['rules' => 'required|maxlength:255', 'titlewidth' => 12, 'feildwidth' => 12],
            'level_id' => ['type' => 'select', 'title' => 'Level',  'options' => $this->levelOptions, 'rules' => 'required', 'titlewidth' => 12, 'feildwidth' => 12],
        ];


        $this->categoriesOptions = [];
        $this->categoriesOptions = $this->model::where('data_type', 'lib')->pluck('categories', 'categories')->toArray();

        if ($this->type == 'lib' || $this->type == 'allin') {
            // $this->gridCol = ['fragment_id' => 'Id',  'fragment_name' => 'fragment Name', "categories" => 'Categories',   'created_at' => 'Created At'];
            $this->gridCol = ['order_action' => '#', 'display_order' => 'ID',  'fragment_name' => 'fragment Name', "categories" => 'Categories',   'created_at' => 'Created At'];
            $this->forms = [
                'fragment_name' => ['title' => 'fragment name', 'rules' => 'required|maxlength:255', 'titlewidth' => 12, 'feildwidth' => 12],
                'categories' => ['type' => 'stags', 'title' => 'Categories',  'options' => $this->categoriesOptions, 'rules' => 'required', 'titlewidth' => 12, 'feildwidth' => 12],
                'package_ids' => ['type' => 'checkbox', 'title' => 'Packages',  'options' => $this->packageOptions, 'rules' => 'required', 'titlewidth' => 12, 'feildwidth' => 12]
            ];

            // $this->forms['categories'] = ['type' => 'stags', 'title' => 'Categories',  'options' => $this->categoriesOptions, 'rules' => 'required', 'titlewidth' => 12, 'feildwidth' => 12];
            // $this->forms['package_ids'] = ['type' => 'checkbox', 'title' => 'Packages',  'options' => $this->packageOptions, 'rules' => 'required', 'titlewidth' => 12, 'feildwidth' => 12];
        } else {
            $this->forms = [
                'fragment_name' => ['rules' => 'required|maxlength:255', 'titlewidth' => 12, 'feildwidth' => 12],
                //'fragment_type' => ['type' => 'select', 'rules' => 'required', 'options' => $this->typeoptions,  'default' => 'a', 'titlewidth' => 12, 'feildwidth' => 12],
                'level_id' => ['type' => 'select', 'title' => 'Level',  'options' => $this->levelOptions, 'rules' => 'required', 'titlewidth' => 12, 'feildwidth' => 12],
                //'categories' => ['type' => 'stags', 'title' => 'Categories',  'options' => $this->categoriesOptions, 'rules' => 'required', 'titlewidth' => 12, 'feildwidth' => 12],
                'package_ids' => ['type' => 'checkbox', 'title' => 'Packages',  'options' => $this->packageOptions, 'rules' => 'required', 'titlewidth' => 12, 'feildwidth' => 12]
            ];
            // $this->forms['package_ids'] = ['type' => 'checkbox', 'title' => 'Packages',  'options' => $this->packageOptions, 'rules' => 'required', 'titlewidth' => 12, 'feildwidth' => 12];
        }

        $this->filters = [
            'fragment_name' => ["width" => 8, 'title' => 'Game Name'],
            'categories' => ['type' => 'select', 'title' => 'Category', "width" => 4, 'options' => $this->categoriesOptions, 'operator' => '='],
        ];
    }


    public function get_ignoresort()
    {
        return [
            'order_action',
            // 'display_order',
            // 'fragment_name',
            // "fragment_type",
            "level_id",
            //'created_at',
        ];
    }


    public function format_griddata($data, $mperm)
    {
        $up_icon = url('public/icons/up.png');
        $down_icon = url('public/icons/down.png');
        $copy_icon = url('public/icons/17.png');
        foreach ($data as $key => $value) {

            $order_action = '';

            //if ($this->type == 'ls') {
            $order_action = '<a class="btn custom_action_btn btn-xs text-white btn-success hide_btn" title="Move up" ng-click="addaction($event,' . "'move_up_fragment'" . ',' . $value['display_order'] . ')"><i class="fa fa-arrow-circle-up  " aria-hidden="true"></i></a>';
            $order_action .= '<a class="btn custom_action_btn btn-xs text-white btn-danger" title="Move Down" ng-click="addaction($event,' . "'move_down_fragment'" . ',' . $value['display_order'] . ')">  <i class="fa fa-arrow-circle-down  " aria-hidden="true"></i> </a>';
            //}
            // if ($this->type == 'ex') {
            //     $order_action = '<a class="btn custom_action_btn btn-xs text-white btn-success hide_btn" title="Move up" ng-click="addaction($event,' . "'move_up_exam'" . ',' . $value[$this->getpk()] . ')"> <i class="fa fa-arrow-circle-up  " aria-hidden="true"></i> </a>';
            //     $order_action .= '<a class="btn custom_action_btn btn-xs text-white btn-danger" title="Move Down" ng-click="addaction($event,' . "'move_down_exam'" . ',' . $value[$this->getpk()] . ')"> <i class="fa fa-arrow-circle-down  " aria-hidden="true"></i> </a>';
            // }
            $data[$key]['order_action'] = $order_action;

            $data[$key]['status'] = $this->gridswitch('status', 'a', 'd', $value);
            $data[$key]['fragment_type'] = $this->typeoptions[$value['fragment_type']] ?? '-';
            $data[$key]['class_id'] = $this->classOptions[$value['class_id']] ?? '-';
            $data[$key]['level_id'] = $this->levelOptions[$value['level_id']] ?? '-';

            $actions = '';
            if ($this->type != 'lib') {
                $actions = '<a class="btn btn-xs text-white btn-info" title="Make Fragment Copy" ng-click="copyData(' . "'frg'" . ',' . $value[$this->getpk()] . ')"> <i class="fa fa-files-o" aria-hidden="true"></i> </a>';
            } else {
                $actions .= '<a class="btn btn-xs text-white btn-success" title="Make Data Library Copy" ng-click="copyData(' . "'lib'" . ',' . $value[$this->getpk()] . ')"> <i class="fa fa-clone" aria-hidden="true"></i> </a>';
            }
            $actions .= $this->action_formate($value, $mperm);
            $data[$key]['actions'] = $actions;
        }
        return $data;
    }

    public function can_view($value, $mperm)
    {

        return false;
    }

    public function get_griddata($request, $gridCol, $mperm)
    {
        $this->user = $_SESSION['user'] ?? [];
        $this->school_id = $this->user['school_id'] ?? null;

        $data = $this->model::whereRaw($this->get_where($request, $gridCol));

        if (!empty($request['filters'])) {
            $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];
            if (!empty($filterval['filter_class_id']) && $filterval['filter_class_id'] != 'all') {
                $data = $data->where('class_id', 'like', '%"' . $filterval['filter_class_id'] . '"%');
            }
        }
        if (isset($request->level_id) && $request->level_id > 0) {
            $data = $data->where('level_id', $request->level_id);
        }
        //dd($this->type ?? 'frg');
        $data = $data->where('school_id', $this->school_id)->where('data_type', $this->type ?? 'frg');
        $request['sortby'] = $request['sortby'] == 'id' ? 'display_order' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }

    public function prepare_insert($data)
    {
        unset($data['_token']);
        unset($data['id']);
        $data['school_id'] = $this->school_id;
        $data['data_type'] = $this->type;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function prepare_update($data)
    {
        $data['fragment_id'] = $data['id'];
        $data['school_id'] = $this->school_id;
        //$data['data_type'] = $this->type;
        unset($data['_token']);
        unset($data['id']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function save(Request $request)
    {

        //return $request;
        //Common::check_access('admin.' . $this->slug . '.add');
        $data = $this->prepare_insert($request->all());
        $obj = new $this->model;
        $obj->fragment_name = $data['fragment_name'] ?? '';
        $obj->fragment_type = $data['fragment_type'] ?? '';
        $obj->data_type = $this->type ?? 'frg';
        $obj->school_id = $this->school_id ?? null;
        $obj->class_id = $data['class_id'] ?? '';
        $obj->level_id = $data['level_id'] ?? '';
        $obj->package_ids = $data['package_ids'] ?? [];
        $obj->categories = $data['categories'] ?? '';

        if ($obj->save()) {
            $this->modifyFragmentData($obj->fragment_id, $data);
            return ['flag' => 1, 'msg' => $this->mTitle . ' inserted Successfully.', 'data' => $obj->toArray()];
        } else {
            return ['flag' => 2, 'msg' => ' Oops! Something went worng.', 'data' => []];
        }
        return $data;
    }

    public function add(Request $request)
    {
        ini_set('max_execution_time',  -1);
        $formData = $this->get_forms($request);
        $formData['catList'] = $this->GetCatData();
        return $formData;
    }

    public function edit($id, Request $request)
    {
        ini_set('max_execution_time',  -1);
        //Common::check_access();
        $data = $this->model::find($id);
        if (empty($data)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.'];
        }
        $formData = $this->get_forms($request, $data);
        $fData = $data->load(['vocabulary', 'qa', 'role_play', 'missing_words', 'mc', 'sentence', 'english_words', 'english_sentences', 'grammer'])->toArray();
        //$fData = $data->toArray();

        if (!empty($fData['grammer'])) {
            $grammer = collect($fData['grammer']);
            $fData['grammer_var1'] = $grammer->whereNotIn('grammer_var1', ['', null])->pluck('grammer_var1')->first();
            $fData['grammer_var2'] = $grammer->whereNotIn('grammer_var2', ['', null])->pluck('grammer_var2')->first();
            $fData['grammer_var3'] = $grammer->whereNotIn('grammer_var3', ['', null])->pluck('grammer_var3')->first();
            $fData['grammer_var4'] = $grammer->whereNotIn('grammer_var4', ['', null])->pluck('grammer_var4')->first();
            $fData['grammer_var5'] = $grammer->whereNotIn('grammer_var5', ['', null])->pluck('grammer_var5')->first();
            $fData['grammer_var6'] = $grammer->whereNotIn('grammer_var6', ['', null])->pluck('grammer_var6')->first();
            $fData['grammer_var7'] = $grammer->whereNotIn('grammer_var7', ['', null])->pluck('grammer_var7')->first();
            $fData['grammer_var8'] = $grammer->whereNotIn('grammer_var8', ['', null])->pluck('grammer_var8')->first();
            $fData['grammer_var9'] = $grammer->whereNotIn('grammer_var9', ['', null])->pluck('grammer_var9')->first();
            $fData['grammer_var10'] = $grammer->whereNotIn('grammer_var10', ['', null])->pluck('grammer_var10')->first();
            $fData['grammer_var11'] = $grammer->whereNotIn('grammer_var11', ['', null])->pluck('grammer_var11')->first();
            $fData['grammer_var12'] = $grammer->whereNotIn('grammer_var12', ['', null])->pluck('grammer_var12')->first();
            $fData['grammer_var13'] = $grammer->whereNotIn('grammer_var13', ['', null])->pluck('grammer_var13')->first();
            $fData['grammer_var14'] = $grammer->whereNotIn('grammer_var14', ['', null])->pluck('grammer_var14')->first();
            $fData['grammer_var15'] = $grammer->whereNotIn('grammer_var15', ['', null])->pluck('grammer_var15')->first();
            $fData['grammer_var16'] = $grammer->whereNotIn('grammer_var16', ['', null])->pluck('grammer_var16')->first();
        }


        if (!empty($fData['missing_words'])) {
            foreach ($fData['missing_words'] as $key => $value) {
                $temps = [];
                $fData['missing_words'][$key]['missing_word_sentences_plain'] = $value['missing_word_sentences'];
                $value['missing_word_sentences'] = explode(' ', $value['missing_word_sentences']);
                foreach ($value['missing_word_sentences'] as $key2 => $value2) {
                    if (strpos($value2, '^') !== false) {
                        array_push($temps, ['type' => 1, 'value' => str_replace('^', '', $value2)]);
                    } else {
                        array_push($temps, ['type' => 2, 'value' => str_replace('^', '', $value2)]);
                    }
                }
                $fData['missing_words'][$key]['missing_word_sentences'] = $temps;
            }
        }

        $feilds = ['a', 'b', 'c', 'd', 'e', 'f'];
        if (!empty($fData['mc'])) {
            foreach ($fData['mc'] as $key => $value) {
                foreach ($feilds as $key2 => $value2) {
                    $tval = $value['choice_' . $value2] ?? '';
                    $fData['mc'][$key]['choice_' . $value2] = ['type' => strpos($tval, '^') !== false ? true : false, 'value' => str_replace('^', '', $tval)];
                }
            }
        }



        $formData['catList'] = $this->GetCatData();
        $formData['formData'] = $fData;
        //$formData['formData'] = [];
        return $formData;
    }

    public function update($id, Request $request)
    {
        //return $request->all();
        //Common::check_access('admin.' . $this->slug . '.edit');
        $obj = $this->model::find($id);
        if (empty($obj)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.', 'data' => []];
        }

        $data = $this->prepare_update($request->all());
        $obj->fragment_name = $data['fragment_name'] ?? '';
        $obj->fragment_type = $data['fragment_type'] ?? '';
        $obj->data_type = $this->type ?? 'frg';
        $obj->school_id = $this->school_id ?? null;
        $obj->class_id = $data['class_id'] ?? '';
        $obj->level_id = $data['level_id'] ?? '';
        $obj->package_ids = $data['package_ids'] ?? [];
        $obj->categories = $data['categories'] ?? '';

        if ($obj->update()) {
            $this->modifyFragmentData($obj->fragment_id, $data);
            return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully.', 'data' => $obj->toArray()];
        } else {
            return ['flag' => 2, 'msg' => ' Oops! Something went worng.', 'data' => []];
        }
    }


    public function modifyFragmentData($fragment_id, $data)
    {

        ChapterVocabulary::where('fragment_id', $fragment_id)->delete();
        ChapterQA::where('fragment_id', $fragment_id)->delete();
        ChapterRolePlay::where('fragment_id', $fragment_id)->delete();
        ChapterMissingWord::where('fragment_id', $fragment_id)->delete();
        ChapterMultipleChoice::where('fragment_id', $fragment_id)->delete();
        ChapterExtrawords::where('fragment_id', $fragment_id)->delete();
        ChapterEnglishWord::where('fragment_id', $fragment_id)->delete();
        ChapterEnglishSentence::where('fragment_id', $fragment_id)->delete();
        ChapterGrammer::where('fragment_id', $fragment_id)->delete();


        $data['grammer_english'] = $data['grammer_english'] ?? [];
        $cols = ['grammer_english', 'grammer_korean'];
        foreach ($data['grammer_english'] as $key => $value) {
            $obj = new ChapterGrammer;
            foreach ($cols as $key2 => $col) {
                $obj->$col = trim($data[$col][$key] ?? '');
            }
            if ($key == 0) {
                $obj->grammer_var1 = $data['grammer_var1'] ?? [];
                $obj->grammer_var2 = $data['grammer_var2'] ?? [];
                $obj->grammer_var3 = $data['grammer_var3'] ?? [];
                $obj->grammer_var4 = $data['grammer_var4'] ?? [];
                $obj->grammer_var5 = $data['grammer_var5'] ?? [];
                $obj->grammer_var6 = $data['grammer_var6'] ?? [];
                $obj->grammer_var7 = $data['grammer_var7'] ?? [];
                $obj->grammer_var8 = $data['grammer_var8'] ?? [];
                $obj->grammer_var9 = $data['grammer_var9'] ?? [];
                $obj->grammer_var10 = $data['grammer_var10'] ?? [];
                $obj->grammer_var11 = $data['grammer_var11'] ?? [];
                $obj->grammer_var12 = $data['grammer_var12'] ?? [];
                $obj->grammer_var13 = $data['grammer_var13'] ?? [];
                $obj->grammer_var14 = $data['grammer_var14'] ?? [];
                $obj->grammer_var15 = $data['grammer_var15'] ?? [];
                $obj->grammer_var16 = $data['grammer_var16'] ?? [];
            }
            $obj->fragment_id = $fragment_id;
            $obj->save();
        }

        $data['qa_english_question'] = $data['qa_english_question'] ?? [];
        $cols = ['qa_english_question', 'qa_english_answer', 'qa_korean_question', 'qa_korean_answer', 'qa_image_urls'];
        foreach ($data['qa_english_question'] as $key => $value) {
            $obj = new ChapterQA;
            foreach ($cols as $key2 => $col) {
                $obj->$col = trim($data[$col][$key] ?? '');
            }
            $obj->fragment_id = $fragment_id;
            $obj->save();
        }


        $data['role_play_english_A'] = $data['role_play_english_A'] ?? [];
        $cols = ['role_play_english_A', 'role_play_english_B', 'role_play_korean_A', 'role_play_korean_B', 'role_play_image_urls'];
        foreach ($data['role_play_english_A'] as $key => $value) {
            $obj = new ChapterRolePlay();
            foreach ($cols as $key2 => $col) {
                $obj->$col = trim($data[$col][$key] ?? '');
            }
            $obj->fragment_id = $fragment_id;
            $obj->save();
        }


        $data['vocabulary_english'] = $data['vocabulary_english'] ?? [];
        $cols = ['vocabulary_english', 'vocabulary_korean', 'vocabulary_image_urls'];
        foreach ($data['vocabulary_english'] as $key => $value) {
            $obj = new ChapterVocabulary();
            foreach ($cols as $key2 => $col) {
                $obj->$col = trim($data[$col][$key] ?? '');
            }
            $obj->fragment_id = $fragment_id;
            $obj->save();
        }


        $data['choice_question'] = $data['choice_question'] ?? [];
        $cols = ['choice_question', 'choice_a', 'choice_b', 'choice_c', 'choice_d', 'choice_e', 'choice_f', 'option_type', 'choice_image_urls'];
        $feilds = ['a', 'b', 'c', 'd', 'e', 'f'];
        foreach ($data['choice_question'] as $key => $value) {
            $obj = new ChapterMultipleChoice();
            $obj->choice_question =  trim($data['choice_question'][$key] ?? '');
            $obj->choice_image_urls =  $data['choice_image_urls'][$key] ?? '';
            $obj->option_type =  $data['option_type'][$key] ?? '';

            foreach ($feilds as $key2 => $value2) {
                $tval = $data['choice_' . $value2][$key] ?? '';
                $tname = 'choice_' . $value2;
                $obj->$tname = trim((!empty($data['choice_' . $value2 . '_ck'][$key])  ? '^' : '') . ($tval));
            }

            $obj->fragment_id = $fragment_id;
            $obj->save();
        }

        $data['extrawords_sentence'] = $data['extrawords_sentence'] ?? [];
        $cols = ['extrawords_sentence', 'extrawords_word1', 'extrawords_word2', 'extrawords_word3', 'extrawords_word4', 'extrawords_word5', 'extrawords_word6', 'extrawords_word7', 'extrawords_word8', 'extrawords_image_urls'];

        foreach ($data['extrawords_sentence'] as $key => $value) {
            $obj = new ChapterExtrawords();
            foreach ($cols as $key2 => $col) {
                $obj->$col = trim($data[$col][$key] ?? '');
            }
            $obj->fragment_id = $fragment_id;
            $obj->save();
        }


        $data['englishword_word'] = $data['englishword_word'] ?? [];
        $cols = ['englishword_word', 'englishword_image_urls'];
        foreach ($data['englishword_word'] as $key => $value) {
            $obj = new ChapterEnglishWord();
            foreach ($cols as $key2 => $col) {
                $obj->$col = trim($data[$col][$key] ?? '');
            }
            $obj->fragment_id = $fragment_id;
            $obj->save();
        }


        $data['englishsentence_sentence'] = $data['englishsentence_sentence'] ?? [];
        $cols = ['englishsentence_sentence', 'englishsentence_image_urls', 'englishsentence_korean'];
        foreach ($data['englishsentence_sentence'] as $key => $value) {
            $obj = new ChapterEnglishSentence();
            foreach ($cols as $key2 => $col) {
                $obj->$col = trim($data[$col][$key] ?? '');
            }
            $obj->fragment_id = $fragment_id;
            $obj->save();
        }

        $data['missing_word_id'] = $data['missing_word_id'] ?? [];
        $cols = ['missing_word_id', 'missing_word_sentences'];
        foreach ($data['missing_word_id'] as $key => $value) {
            $obj = new ChapterMissingWord();
            $mws = '';
            if (!empty($data['missing_word_sentences'][$key])) {
                foreach ($data['missing_word_sentences'][$key] as $key2 => $value2) {
                    if ($value2["'type'"] == 1) {
                        $mws .= '^' . $value2["'value'"] ?? '';
                    } else {
                        $mws .= $value2["'value'"] ?? '';
                    }
                    $mws .= ' ';
                }
            }
            $obj->missing_word_sentences  = trim($mws);
            $obj->missing_word_image_urls = $data['missing_word_image_urls'][$key] ?? '';
            $obj->fragment_id = $fragment_id;
            $obj->save();
        }
    }


    function GetCatData($pack = '')
    {
        //dd($pack);
        if (empty($pack)) {
            return [];
        }
        $catData = [];
        $feilds = ['a', 'b', 'c', 'd', 'e', 'f'];
        $dataTypes = [$pack];
        $catList = $this->model::where('data_type', 'lib')->with([$pack])->get()->toArray();
        //return $catList;
        foreach ($catList as $ckey => $cvalue) {

            if (!empty($cvalue['missing_words'])) {
                foreach ($cvalue['missing_words'] as $key => $value) {
                    $temps = [];
                    $catList[$ckey]['missing_words'][$key]['missing_word_sentences_plain'] = $value['missing_word_sentences'];
                    $value['missing_word_sentences'] = explode(' ', $value['missing_word_sentences']);
                    foreach ($value['missing_word_sentences'] as $key2 => $value2) {
                        if (strpos($value2, '^') !== false) {
                            array_push($temps, ['type' => 1, 'value' => str_replace('^', '', $value2)]);
                        } else {
                            array_push($temps, ['type' => 2, 'value' => str_replace('^', '', $value2)]);
                        }
                    }
                    $catList[$ckey]['missing_words'][$key]['missing_word_sentences'] = $temps;
                }
            }

            if (!empty($cvalue['mc'])) {
                foreach ($cvalue['mc'] as $key => $value) {
                    foreach ($feilds as $key2 => $value2) {
                        $tval = $value['choice_' . $value2] ?? '';
                        $catList[$ckey]['mc'][$key]['choice_' . $value2] = ['type' => strpos($tval, '^') !== false ? true : false, 'value' => str_replace('^', '', $tval)];
                    }
                }
            }
        }

        foreach ($catList as $key => $value) {
            if (!empty($value['categories'])) {
                foreach ($dataTypes as $key3 => $value3) {
                    if (!empty($value[$value3])) {
                        $catData[$value3][$value['categories']] = $catData[$value3][$value['categories']] ?? [];
                        foreach ($value[$value3] as $key4 => $value4) {
                            array_push($catData[$value3][$value['categories']], $value4);
                        }
                    }
                }
            }
        }

        unset($catList);

        return $catData;
    }

    function GetCatDataRequest(Request $request)
    {
        $input = $request->all();
        $tabs = [
            '2' => 'qa',
            '3' => 'missing_words',
            '4' => 'role_play',
            '5' => 'vocabulary',
            '6' => 'mc',
            '7' => 'sentence',
            '8' => 'english_words',
            '9' => 'english_sentences',
            '10' => 'grammer'
        ];

        if (!empty($input['tab']) && !empty($tabs[$input['tab']])) {
            $data = $this->GetCatData($tabs[$input['tab']]);
            return ['flag' => 1, 'data' => $data];
        } else {
            return ['flag' => 2, 'data' => []];
        }
    }


    public function bulkaction_($slug, $request)
    {
        if ($slug == 'move_up_fragment') {
            $obj = $this->model::withTrashed()->where('display_order', $request['id'])->first();
            if (empty($obj)) {
                return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
            }
            $next = $this->model::where('display_order', '>', $request['id'])->where('data_type', $obj['data_type'])->orderBy('display_order', 'asc')->first();

            if (empty($next)) {
                return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
            }

            $next_display_order = $next->display_order;
            $next->display_order = $obj->display_order;


            $next->save();

            $obj->display_order = $next_display_order;
            $obj->save();
            return ['flag' => 1, 'msg' =>  'User Activated Successfully'];



            /*$this->model::where('fragment_id',  $next['fragment_id'])->update(['display_order' => $obj->display_order]);
            $obj->display_order = $next['display_order'];
*/
            if ($obj->update()) {
                return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        } else if ($slug == 'move_down_fragment') {
            $obj = $this->model::withTrashed()->where('display_order', $request['id'])->first();
            if (empty($obj)) {
                return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
            }
            $prev = $this->model::withTrashed()->where('display_order', '<', $request['id'])->where('data_type', $obj['data_type'])->orderBy('display_order', 'DESC')->first();
            if (empty($prev)) {
                return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
            }

            $prev_display_order = $prev->display_order;
            $prev->display_order = $obj->display_order;


            $prev->save();

            $obj->display_order = $prev_display_order;
            $obj->save();
            return ['flag' => 1, 'msg' =>  'User Activated Successfully'];

            $this->model::withTrashed()->where('fragment_id', '=',  $prev['fragment_id'])->update(['display_order' => $obj->display_order]);
            $obj->display_order = $prev['display_order'];
            if ($obj->update()) {
                return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        }
        //return $slug;
        // if ($slug == 'move_up_lesson') {
        //     $obj = $this->model::withTrashed()->find($request['id']);
        //     $next = $this->model::withTrashed()->where('display_order', '>', $obj->display_order)->where('is_removed', 'n')->where('is_hide', 'n')->where('lesson_type', 'ls')->orderBy('display_order', 'asc')->first();
        //     if (empty($next)) {
        //         return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
        //     }
        //     $this->model::where('lesson_id',  $next['lesson_id'])->update(['display_order' => $obj->display_order]);
        //     $obj->display_order = $next['display_order'];
        //     if ($obj->update()) {
        //         return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
        //     } else {
        //         return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
        //     }
        // } else if ($slug == 'move_down_lesson') {
        //     $obj = $this->model::withTrashed()->find($request['id']);
        //     $prev = $this->model::withTrashed()->where('display_order', '<', $obj->display_order)->where('is_removed', 'n')->where('is_hide', 'n')->where('lesson_type', 'ls')->orderBy('display_order', 'DESC')->first();
        //     if (empty($prev)) {
        //         return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
        //     }
        //     $this->model::withTrashed()->where('lesson_id', '=',  $prev['lesson_id'])->update(['display_order' => $obj->display_order]);
        //     $obj->display_order = $prev['display_order'];
        //     if ($obj->update()) {
        //         return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
        //     } else {
        //         return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
        //     }
        // } else if ($slug == 'move_up_exam') {
        //     $obj = $this->model::withTrashed()->find($request['id']);
        //     $next = $this->model::withTrashed()->where('display_order', '>', $obj->display_order)->where('is_removed', 'n')->where('is_hide', 'n')->where('lesson_type', 'ex')->orderBy('display_order', 'asc')->first();
        //     if (empty($next)) {
        //         return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
        //     }
        //     $this->model::withTrashed()->where('lesson_id',  $next['lesson_id'])->update(['display_order' => $obj->display_order]);
        //     $obj->display_order = $next['display_order'];
        //     if ($obj->update()) {
        //         return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
        //     } else {
        //         return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
        //     }
        // } else if ($slug == 'move_down_exam') {
        //     $obj = $this->model::withTrashed()->find($request['id']);
        //     $prev = $this->model::withTrashed()->where('display_order', '<', $obj->display_order)->where('is_removed', 'n')->where('is_hide', 'n')->where('lesson_type', 'ex')->orderBy('display_order', 'DESC')->first();
        //     if (empty($prev)) {
        //         return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
        //     }
        //     $this->model::withTrashed()->where('lesson_id', '=',  $prev['lesson_id'])->update(['display_order' => $obj->display_order]);
        //     $obj->display_order = $prev['display_order'];
        //     if ($obj->update()) {
        //         return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
        //     } else {
        //         return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
        //     }
        // }
        return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
    }
}
