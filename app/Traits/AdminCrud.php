<?php

namespace App\Traits;

use Illuminate\Http\Request;
use \App\Common;
use Faker\Factory;

trait AdminCrud
{
    public function getpk()
    {
        return !empty($this->pk) ? $this->pk : 'id';
    }

    public function gridlabel($data, $type = 'primary')
    {
        if (empty($data)) {
            return '-';
        }
        if (is_array($data)) {
            $str = '';
            foreach ($data as $key => $value) {
                $str .= '<span class="badge badge-' . $type . ' btn-xs mr-1 badge-pill font-s">' . ucfirst($value) . '</span>';
            }
            return $str;
        } else {
            return '<span class="badge badge-' . $type . ' btn-xs mr-1 badge-pill font-s">' . ucfirst($data) . '</span>';
        }
    }

    public function gridMultiLabel($ids, $options, $type = 'primary')
    {
        $str = '';
        if (!(!empty($ids) && !empty($options))) {
            return '-';
        }

        foreach ($ids as $key => $value) {
            if (!empty($options[$value])) {
                $str .= $this->gridlabel($options[$value], $type);
            }
        }
        return $str ?? '-';
    }

    public function gridswitch($col, $trueval, $falseval, $value)
    {
        $val = $value[$col] ?? $falseval;
        $id = $value[$this->getpk()];
        return '<label class="switch gridswich"><input type="checkbox" ng-click="called($event)" data-trueval="' . $trueval . '" data-falseval="' . $falseval . '" data-col="' . $col . '" data-id="' . $id . '" ' . ($val == $trueval ? 'checked' : "") . '  ><span class="slider round"></span></label>';
    }

    public function gridimage($data, $height = 40, $width = 40)
    {

        $def = '<a class="grid_ilink" href="' . asset('public/' . 'images\default\no-image-available.png') . '" target="_blank" ><img src="' . asset('public/' . 'images\default\no-image-available.png') . '" width="' . $width . 'px" height="' . $height . 'px" ></a>';
        if (is_array($data)) {
            $str = '';
            foreach ($data as $key => $value) {
                if (Common::is_file($value)) {
                    $str .= '<a class="grid_ilink" href="' . asset('public/' . $value) . '" target="_blank" ><img src="' . asset('public/' . $value) . '" width="' . $width . 'px" height="' . $height . 'px" ></a>';
                }
            }
            return  $str != '' ? $str : $def;
        } else {
            return Common::is_file($data) ? '<a class="grid_ilink" href="' . asset('public/' . $data) . '" target="_blank" ><img src="' . asset('public/' . $data) . '" width="' . $width . 'px" height="' . $height . 'px" ></a>' : $def;
        }
    }

    public function can_show($mperm)
    {
        if ($mperm['role'] == 'admin' || in_array('admin.' . $this->slug . '.index', $mperm['actions'])) {
            return true;
        }
        return false;
    }

    public function griddata(Request $request)
    {
        $mperm = Common::user_roles();
        $gridCol = $this->gridCol($request);
        // if (!$this->can_show($mperm)) {
        //     return ['flag' => 2];
        // }
        return [
            'flag' => 1,
            'gridCol' => $gridCol,
            'griddata' => $this->get_griddata($request, $gridCol, $mperm),
            'bulkactions' => $this->bulkactions(),
            'filtersinputs' => $this->filtersinputs($request),
            'can_add' => $this->can_add($mperm),
            'can_bulk' => $this->can_bulk($mperm),
            'can_export' => $this->can_export($mperm),
            'admin_role' => session('admin_role')
            //'get_where' => $this->get_where($request, $gridCol)
        ];
    }

    public function filtersinputs($request)
    {
        $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];
        $filters = $this->filters ?? [];
        $filters_str = '';
        foreach ($filters as $key => $value) {
            $width = $value['width'] ?? 3;
            $value['titlewidth'] = $value['titlewidth'] ?? '12';
            $value['feildwidth'] = $value['feildwidth'] ?? '12';
            $value['class'] = 'filter_control input-sm';
            $value['title'] = $value['title'] ?? Common::format_colname($key) ?? '-';
            $value['placeholder'] = 'Search By ' . Common::format_colname($key);
            $value['options'] = ['all' => 'All ' . $value['title']] + ($value['options'] ?? []);
            $fval = $filterval['filter_' . $key] ?? '';
            $filters_str .= '<div class="col-md-' . $width . '">';
            $filters_str .= Common::render_field($key, $fval, $value);
            $filters_str .= '</div>';
        }
        
        return $filters_str;
    }

    public function get_griddata($request, $gridCol, $mperm)
    {
        $data = $this->model::whereRaw($this->get_where($request, $gridCol));
        //$data = $this->model::whereRaw('1 = 1');
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }

    public function get_where($request, $gridCol)
    {
        $where = '1 = 1';
        $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];
        $filters = $this->filters ?? [];
        foreach ($filters as $key => $value) {
            $fval = isset($filterval["filter_" . $key]) ? trim($filterval["filter_" . $key]) : '';
            if ($fval != '' && $fval != 'all' && $fval !== 0 && $fval != '0') {
                if (empty($value['cond'])) {
                    $operator = $value['operator'] ?? 'like';
                    if ($operator != 'like') {
                        $where .= ' and  (' . $key . '  ' . $operator . ' "' . $fval . '"  )';
                    } else {
                        $where .= ' and  (' . $key . ' like  "%' . $fval . '%"  )';
                    }
                }
            }
        }
        return $where;
    }

    public function bulkactions()
    {
        return [
            ['slug' => 'delete', 'title' => 'Delete']
        ];
    }

    public function get_filterform($request)
    {
        $this->filters = !empty($this->filters) ? $this->filters : [];
        return $this->filters;
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            //$data[$key]['ckbox'] = '<div class="checkbox"><label><input type="checkbox" class="chk_record" data-id="' . $value[$this->getpk()] . '" ></label></div>';
            $data[$key]['actions'] = $this->action_formate($value, $mperm);
        }
        return $data;
    }

    public function action_formate($value, $mperm)
    {
        $action = '';
        $edit = url('public/icons/18.png');
        $delete = url('public/icons/19.png');
        if ($this->can_view($value, $mperm)) {
            $action .= '<a class="btn btn-xs text-white btn-success" title="View" ng-click="view(' . $value[$this->getpk()] . ')"> <i class="fa fa-eye" > </i> </a>';

        }
        if ($this->can_edit($value, $mperm)) {
             $action .= '<a class="btn btn-xs text-white btn-primary" title="Edit" ng-click="edit(' . $value[$this->getpk()] . ')"> <i class="fa fa-pencil" > </i> </a>';
        }
        if ($this->can_delete($value, $mperm)) {
            $action .= '<a class="btn btn-xs text-white btn-danger" title="Delete" ng-click="delete(' . $value[$this->getpk()] . ')"> <i class="fa fa-trash" > </i> </a>';

        }

        return $action;
    }

    public function get_ignoresort()
    {
        return !empty($this->ignoresort) ? $this->ignoresort : [];
    }

    public function can_edit($value, $mperm)
    {
        if ($mperm['role'] != 'admin' && !in_array('admin.' . $this->slug . '.update', $mperm['actions'])) {
            return false;
        }
        return true;
    }

    public function can_delete($value, $mperm)
    {
        if ($mperm['role'] != 'admin' && !in_array('admin.' . $this->slug . '.delete', $mperm['actions'])) {
            return false;
        }
        return true;
    }

    public function can_view($value, $mperm)
    {
        return false;
        if ($mperm['role'] != 'admin' && !in_array('admin.' . $this->slug . '.view', $mperm['actions'])) {
        }
        return true;
    }

    public function can_add($mperm)
    {
        if ($mperm['role'] != 'admin' && !in_array('admin.' . $this->slug . '.add', $mperm['actions'])) {
            return false;
        }
        return true;
    }



    public function can_bulk($mperm)
    {
        if ($mperm['role'] != 'admin' && !in_array('admin.' . $this->slug . '.bulkaction', $mperm['actions'])) {
            return false;
        }
        return true;
    }

    public function can_export($mperm)
    {
        if ($mperm['role'] != 'admin' && !in_array('admin.' . $this->slug . '.export', $mperm['actions'])) {
            return false;
        }
        return true;
    }


    public function gridCol($request)
    {
        $isort = $this->get_ignoresort();
        $gridCol = [];
        //array_push($gridCol, ['slug' => 'ckbox', 'title' => '<div class="checkbox"><label><input ng-model="chk_all" class="chk_all" ng-change="chkbox_change()" type="checkbox"></label></div>', 'sclass' => '']);
        foreach ($this->getgridCol() as $key => $value) {
            $slug = is_numeric($key) ? $value : $key;
            $title = is_numeric($key) ? Common::format_colname($value) : $value;
            $scalss = !in_array($slug, $isort) ? 'is_sort' : '';
            $scalss .= $scalss == 'is_sort' && !empty($request['sortby']) && !empty($request['sortdir']) && $request['sortby'] == $slug ? '  ' . $request['sortdir'] . '_sort' : '';
            array_push($gridCol, ['slug' => $slug, 'title' =>  $title, 'sclass' => $scalss]);
        }
        array_push($gridCol, ['slug' => 'actions', 'title' => 'Actions', 'sclass' => '']);
        return $gridCol;
    }

    public function getgridCol()
    {
        return !empty($this->gridCol) ? $this->gridCol : [];
    }

    public function bulkaction($slug, Request $request)
    {
        //Common::check_access();
        if ($slug == 'delete') {
            return $this->bulkaction_delete($request);
        } else if ($slug == 'updatecol') {
            return $this->bulkaction_updatecol($request);
        }
        return $this->bulkaction_($slug, $request);
    }

    public function bulkaction_($slug, $request)
    {
        return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
    }

    public function bulkaction_updatecol($request)
    {
        $obj = $this->model::find($request['id']);
        $obj->{$request['col']} = $request['val'];
        if ($obj->update()) {
            return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully'];
        } else {
            return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
        }
    }

    public function bulkaction_delete($request)
    {
        $request['ids'] = !empty($request['ids']) ? explode(',', $request['ids']) : [0];
        $obj = $this->model::whereIn($this->getpk(), $request['ids']);
        $this->remove_file($obj->get(), 'multi');
        if ($obj->delete()) {
            return ['flag' => 1, 'msg' => (count($request['ids']) - 1) . ' Records Deleted Successfully'];
        } else {
            return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
        }
    }

    public function get_forms($request, $feildvalues = [])
    {
        $this->forms = $this->forms ?? [];
        $form_arr = [];
        $form_render_arr = [];
        $form_str = '';
        foreach ($this->forms as $key => $attr) {
            $fval =  $feildvalues[$key] ?? '';
            $data = Common::format_feilddata($key, $fval, $attr);
            array_push($form_arr, $data);
            if ($data['type'] == 'html') {
                $render =  $form_str .= view($data['view'], ['data' => $data]);
            } else {
                $render = $form_str .= view('admin.feilds.' . $data['type'], ['data' => $data]);
            }
            array_push($form_render_arr, $render);
        }
        return ['form_arr' => $form_arr, 'form_str' => $form_str, 'form_render_arr' => $form_render_arr, 'flag' => 1];
    }

    public function add(Request $request)
    {
        //Common::check_access();
        //return $request->all();
        return $this->get_forms($request);
    }

    public function save(Request $request)
    {
        //Common::check_access('admin.' . $this->slug . '.add');
        $data = $this->prepare_insert($request->all());

        $err = $this->checkCrud($data);
        if (!empty($err)) {
            return ['flag' => 2, 'msg' => $err, 'data' => $err];
        }

        $obj = new $this->model;
        foreach ($data as $key => $value) {
            $obj->$key = $value;
        }

        if ($obj->save()) {
            return ['flag' => 1, 'msg' => $this->mTitle . ' inserted Successfully.', 'data' => $obj->toArray()];
        } else {
            return ['flag' => 2, 'msg' => ' Oops! Something went worng.', 'data' => []];
        }
        return $data;
    }

    public function prepare_insert($data)
    {
        unset($data['_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function edit($id, Request $request)
    {
        //Common::check_access();
        $data = $this->model::find($id);
        if (empty($data)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.'];
        }
        return $this->get_forms($request, $data);
    }

    public function checkCrud($data, $id = 0)
    {
        return [];
    }

    public function update($id, Request $request)
    {
        //Common::check_access('admin.' . $this->slug . '.edit');
        $obj = $this->model::find($id);
        if (empty($obj)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.', 'data' => []];
        }

        $data = $this->prepare_update($request->all());
        $err = $this->checkCrud($data, $id);
        if (!empty($err)) {
            return ['flag' => 2, 'msg' => $err, 'data' => $err];
        }

        foreach ($data as $key => $value) {
            $obj->$key = $value;
        }

        if ($obj->update()) {
            return ['flag' => 1, 'msg' => $this->mTitle . ' Updated Successfully.', 'data' => $obj->toArray()];
        } else {
            return ['flag' => 2, 'msg' => ' Oops! Something went worng.', 'data' => []];
        }
    }

    public function prepare_update($data)
    {
        unset($data['_token']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function filefeilds()
    {
        $files = [];
        foreach ($this->forms as $key => $value) {
            if (!empty($value['type']) && in_array($value['type'], ['image', 'multipleimage', 'file', 'multiplefile'])) {
                $files[$key] = $value['type'];
            }
        }
        return $files;
    }

    public function remove_file($data, $type = 'single')
    {
        $data = $data->toArray();
        $cols = $this->filefeilds();
        $list = [];
        foreach ($cols as $key => $value) {
            if ($type == 'single') {
                $data[$key] = $data[$key] ?? '';
                array_push($list, $data[$key]);
            } else {
                foreach (array_column($data, $key) as $key2 => $value2) {
                    array_push($list, $value2);
                }
            }
        }
        foreach ($list as $key => $value) {
            Common::del_file($value);
        }
    }

    public function delete($id, Request $request)
    {
        //Common::check_access();
        $obj = $this->model::find($id);
        if (empty($obj)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.', 'data' => []];
        }
        $this->remove_file($obj);
        if ($obj->delete()) {
            return ['flag' => 1, 'msg' => $this->mTitle . ' Deleted Successfully.'];
        } else {
            return ['flag' => 2, 'msg' => ' Oops! Something went worng.', 'data' => []];
        }
    }

    public function getviewcol()
    {
        $viewCol = $this->viewCol ?? [];
        $data = [];
        foreach ($viewCol as $key => $value) {
            $slug = is_numeric($key) ? $value : $key;
            $title = is_numeric($key) ? ucfirst($value) : $value;
            $data[$slug] = $title;
        }
        return $data;
    }
    public function view($id, Request $request)
    {
        //Common::check_access();
        $obj = $this->model::find($id);
        if (empty($obj)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.', 'data' => []];
        }
        $obj = $obj->toArray();
        $obj = $this->format_viewdata($obj);
        $str = '<table class="table table-bordered table-hover">';
        foreach ($this->getviewcol() as $key => $value) {
            $obj[$key] = !empty($obj[$key]) ? $obj[$key] : '';
            $obj[$key] = is_array($obj[$key]) ? json_encode($obj[$key])  : $obj[$key];
            $str .= '<tr>';
            $str .= '<td>' . $value . '</td>';
            $str .= '<td>' .  $obj[$key]  . '<td>';
            $str .= '<tr>';
        }
        $str .= '</table>';
        return ['flag' => 1, 'data' => $str];
    }

    public function format_viewdata($data)
    {
        return $data;
    }

    public function getrules()
    {
        $rules = [];
        $this->forms = $this->forms ?? [];
        foreach ($this->forms as $key => $value) {
            $rules[$key] = $value['rules'] ?? '';
        }
        return $rules;
    }

    public function getmsg()
    {
        $msg = [];
        $this->forms = $this->forms ?? [];
        foreach ($this->forms as $key => $value) {
            $msg[$key] = $value['msg'] ?? '';
        }
        return $msg;
    }

    public function export()
    {
        $filename = "test";

        //header info for browser
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$filename.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        $data = $this->model::all()->toArray();
        $sep = "\t";
        foreach ($data as $key => $value) {
            $schema_insert = "";
            foreach ($value as $key2 => $value2) {
                if (!isset($value2))
                    $schema_insert .= "NULL" . $sep;
                elseif ($value2 != "")
                    $schema_insert .= (is_array($value2) ? json_encode($value2) : $value2) . $sep;
                else
                    $schema_insert .= "" . $sep;

                $schema_insert = str_replace($sep . "$", "", $schema_insert);
                $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
                $schema_insert .= "\t";
            }
            print(trim($schema_insert));
            print "\n";
        }
    }

    function faker(Request $request)
    {
        $faker = Factory::create('en_US');
        for ($i = 0; $i < 10; $i++) {
            $this->fakerdata($faker);
        }
        return 'done';
    }

    function fakerdata($faker)
    {
        dd('die');
        $obj = new $this->model;
        return $obj;
    }

    public function getrand($arr)
    {
        return !empty($arr) ? array_rand($arr) : 0;
    }

    public function dummy_image($faker, $dir, $type = 'nightlife', $width = 400, $height = 300)
    {
        $upload_dir =  'images/' . $dir;
        $upload_path = str_replace('/', DIRECTORY_SEPARATOR, base_path('public/' . $upload_dir));
        $target_file = mt_rand(1, 1000) . md5(date('YmdHis')) . '.jpg';
        $image = file_get_contents($faker->imageUrl($width, $height, $type));
        @mkdir($upload_path, 0777, true);
        file_put_contents($upload_path . DIRECTORY_SEPARATOR . $target_file, $image);
        return $upload_dir . DIRECTORY_SEPARATOR . $target_file;
    }

    public function alltags($col)
    {
        $final = [];
        //dd($this->model::all());
        foreach ($this->model::pluck($col, $this->getpk())->toArray() as $key => $value) {
            if (!empty($value)) {
                foreach ($value as $key2 => $value2) {
                    $final[$value2] = $value2;
                }
            }
        }
        //dd($final);
        return $final;
    }
}
