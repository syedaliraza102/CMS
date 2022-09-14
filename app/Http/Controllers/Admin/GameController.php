<?php

namespace App\Http\Controllers\Admin;

use App\Models\Game;
use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\School;
use App\Traits\AdminCrud;
use \App\Traits\Base;

class GameController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = Game::class;
        $this->mTitle = __('title.game');
        $this->slug = 'game';
        $this->pk = 'game_id';

        $this->gridCol = ['game_id' => __('title.game_sort') . ' ID',  'game_name' => __('title.game_sort') . ' Name',  "school_id" => 'School', 'package_id' => 'Package', 'game_type' => 'Type', 'created_at' => 'Created At'];
        $this->viewCol = ['game_id', 'game_name', 'principal_name',   'created_at' => 'Created At'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->typeoptions = ['h' => 'Homework Activity', 'r' => 'Extra Training - No Data', 'exw' => 'Extra Training - Word', 'exs' => 'Extra Training - Sentence'];
        $this->packageOptions = Package::pluck('package_name', 'package_id')->toArray();
        $this->schoolOptions = ['all' => 'All'];
        $tempschoolOptions = School::pluck('school_name', 'school_id')->toArray();
        foreach ($tempschoolOptions as $key => $value) {
            $this->schoolOptions[$key] = $value;
        }
        $this->forms = [
            'game_name' => ['title' => __('title.game_sort') . ' Name', 'rules' => 'required|maxlength:255'],
            'game_URL' => ['title' => __('title.game_sort') . ' Url', 'rules' => 'required|maxlength:255'],
            'game_image' => ['title' => __('title.game_sort') . ' Preview', 'type' => 'image', 'dir' => 'user'],
            'game_type' => ['title' => __('title.game_sort') . ' Type', 'type' => 'select', 'options' => $this->typeoptions,  'default' => 'a'],
            'game_icon' => ['title' => __('title.game_sort') . ' Icon', 'type' => 'image', 'dir' => 'user'],
            'package_id' => ['type' => 'select', 'title' => 'Package',  'options' => $this->packageOptions, 'rules' => 'required'],
            'school_id' => ['type' => 'checkbox', 'title' => 'School',  'options' => $this->schoolOptions, 'rules' => 'required'],
        ];
        $this->filters = [
            'game_name' => ["width" => 8, 'title' => __('title.game') . ' Name'],
            'school_id' => ['type' => 'select', 'cond' => 'c', 'title' => 'School', "width" => 4, 'options' => $this->schoolOptions, 'operator' => '='],
            //'game_type' => ['type' => 'select', 'cond' => 'c', 'title' => 'Type', "width" => 4, 'options' => $this->typeoptions, 'operator' => '='],
        ];
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            $data[$key]['status'] = $this->gridswitch('status', 'a', 'd', $value);
            $data[$key]['game_type'] = $this->typeoptions[$value['game_type']] ?? '-';
            $data[$key]['school_id'] = array_diff($data[$key]['school_id'], array("all"));
            $data[$key]['school_id'] = $this->gridMultiLabel($data[$key]['school_id'] ?? [], $this->schoolOptions);
            $data[$key]['package_id'] = $this->packageOptions[$data[$key]['package_id'] ?? '-'] ?? '-';
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
        $data['game_id'] = $data['id'];
        unset($data['_token']);
        unset($data['id']);
        $data['updated_at'] = date('Y-m-d H:i:s');


        return $data;
    }


    public function get_griddata($request, $gridCol, $mperm)
    {
        $data = $this->model::whereRaw($this->get_where($request, $gridCol));

        if (!empty($request['filters'])) {
            $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];
            if (!empty($filterval['filter_school_id']) && $filterval['filter_school_id'] != 'all') {
                $data = $data->where('school_id', 'like', '%"' . $filterval['filter_school_id'] . '"%');
            }
        }
        $request['sortby'] = $request['sortby'] == 'id' ? 'game_id' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }
}
