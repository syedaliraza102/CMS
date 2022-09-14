<?php

namespace App\Http\Controllers\Admin;

use App\Models\Level;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Traits\AdminCrud;
use \App\Traits\Base;

class LevelController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = Level::class;
        $this->mTitle = 'Level';
        $this->slug = 'level';
        $this->pk = 'level_id';

        $this->gridCol = ['level_id' => 'id',  'level_name' => 'Level Name',   'created_at' => 'Created At'];
        $this->viewCol = ['level_id', 'level_name', 'principal_name',   'created_at' => 'Created At'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->schoolOptions = School::pluck('school_name', 'school_id')->toArray();
        $this->forms = [
            'level_name' => ['rules' => 'required|maxlength:255'],
        ];
        $this->filters = [
            'level_name' => ["width" => 12, 'title' => 'Level Name', 'placeholder' => 'Search Level'],
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
        $data['school_id'] = $this->school_id;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function prepare_update($data)
    {
        $data['level_id'] = $data['id'];
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
        $request['sortby'] = $request['sortby'] == 'id' ? 'level_id' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }
}
