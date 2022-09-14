<?php

namespace App\Http\Controllers\Admin;

use App\Models\School;
use App\Models\ClassTbl;
use App\Models\Game;
use App\Http\Controllers\Controller;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use Illuminate\Http\Request;

class SchoolController extends Controller
{

    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = School::class;
        $this->mTitle = 'School';
        $this->slug = 'school';
        $this->pk = 'school_id';

        $this->gridCol = ['school_id', 'school_name' => 'School Name', 'principal_name' => 'Principal Name',   'created_at' => 'Created At'];
        $this->viewCol = ['school_id', 'school_name', 'principal_name',   'created_at' => 'Created At'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        //$this->gameOptions = Game::pluck('game_name', 'game_id')->toArray();

        $this->gameOptions = ['all' => 'All'];
        $tempgameOptions = Game::pluck('game_name', 'game_id')->toArray();
        foreach ($tempgameOptions as $key => $value) {
            $this->gameOptions[$key] = $value;
        }
        $this->forms = [
            'school_name' => ['rules' => 'required|maxlength:255'],
            'principal_name' => ['rules' => 'required|maxlength:255'],
            'email' => ['rules' => 'required|maxlength:255'],
            'phone_no' => ['rules' => 'required|maxlength:255'],
            'address' => ['rules' => 'required|maxlength:255'],
            'game_ids' => ['type' => 'checkbox', 'title' => __('title.game'),  'options' => $this->gameOptions],
            //'game_ids' => ['type' => 'multipleselect', 'title' => 'Games',  'options' => $this->gameOptions],
            //'status' => ['type' => 'radio', 'options' => $this->statusoptions,  'default' => 'a'],
        ];
        $this->filters = [
            'school_name' => ["width" => 12, 'title' => 'School Name', 'placeholder' => 'Seac'],
            //'status' => ['type' => 'select', "width" => 4, 'options' => $this->statusoptions, 'operator' => '=']
        ];
        $this->user = $_SESSION['user'] ?? [];
        $this->school_id = $this->user['school_id'] ?? null;
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            $data[$key]['status'] = $this->gridswitch('status', 'a', 'd', $value);
            $data[$key]['actions'] = $this->action_formate($value, $mperm);
        }
        return $data;
    }

    public function prepare_insert($data)
    {
        unset($data['_token']);
        unset($data['id']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function prepare_update($data)
    {
        $data['school_id'] = $data['id'];
        unset($data['_token']);
        unset($data['id']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function getClassData () {
        $classList = ClassTbl::where('school_id', $this->school_id)->get();
        return response()->json($classList);
    }
    public function get_griddata($request, $gridCol, $mperm)
    {
        $data = $this->model::whereRaw($this->get_where($request, $gridCol));
        //$data = $this->model::whereRaw('1 = 1');
        $request['sortby'] = $request['sortby'] == 'id' ? 'school_id' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }

    public function update($id, Request $request)
    {
        ////Common::check_access('admin.' . $this->slug . '.edit');
        $obj = $this->model::find($id);
        if (empty($obj)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.', 'data' => []];
        }
        $data = $request->all();
        $game_ids = $data['game_ids'] ?? [];
        unset($data['fragment']);
        unset($data['game_ids']);

        $data = $this->prepare_update($data);

        foreach ($data as $key => $value) {
            $obj->$key = $value;
        }

        if ($obj->update()) {
            $obj = $obj->refresh();
            $this->saveGames($obj->school_id, $game_ids);
            return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully.', 'data' => $obj->toArray()];
        } else {
            return ['flag' => 2, 'msg' => ' Oops! Something went worng.', 'data' => []];
        }
    }

    public function save(Request $request)
    {

        $data = $request->all();
        $game_ids = $data['game_ids'] ?? [];
        unset($data['game_ids']);

        $data = $this->prepare_insert($data);

        $obj = new $this->model;
        foreach ($data as $key => $value) {
            $obj->$key = $value;
        }

        if ($obj->save()) {
            $obj = $obj->refresh();
            $this->saveGames($obj->school_id, $game_ids);
            return ['flag' => 1, 'msg' => $this->mTitle . ' inserted Successfully.', 'data' => $obj->toArray()];
        } else {
            return ['flag' => 2, 'msg' => ' Oops! Something went worng.', 'data' => []];
        }
        return $data;
    }


    public function edit($id, Request $request)
    {

        $games = Game::pluck('school_id', 'game_id')->toArray();
        //Common::check_access();
        $game_ids = [];
        $data = $this->model::find($id);
        //dd($games);
        foreach ($games as $key => $value) {
            if (in_array($id, $value)) {
                array_push($game_ids, $key);
            }
        }
        $data->game_ids = $game_ids;

        if (empty($data)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.'];
        }
        return $this->get_forms($request, $data);
    }

    public function saveGames($school_id, $game_ids)
    {
        foreach ($game_ids as $key => $value) {
            $obj = Game::find($value);
            if (!empty($obj)) {
                $school_ids = $obj->school_id;
                array_push($school_ids, '' . $school_id);
                $school_ids = array_unique($school_ids);
                $obj->school_id = $school_ids;
                $obj->update();
            }
        }
    }
}
