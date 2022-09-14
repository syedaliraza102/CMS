<?php

namespace App\Http\Controllers;

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



    public function redirectUser(Request $request)
    {
        $input = $request->all();
        //$_COOKIE['admin_datatable_' . $this->slug] = [];
        $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];
        $filters = $this->filters ?? [];

        $arr = [
            "pageNo" => 1,
            "perpageItems" => 20,
            "sortby" => "id",
            "sortdir" => "desc",
            "table_search" => "",
            "filter_game_id" => ($input['game_id'] ?? '') .  "",
            "filter_limit_new" => "20",
            "filter_room_name" => "",
        ];
        setcookie('admin_datatable.' . $this->slug, json_encode($arr));
        header("Location: " . url('/') . "/#!/relaxationdata");
        die();
    }
}
