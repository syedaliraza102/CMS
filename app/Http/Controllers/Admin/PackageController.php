<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Http\Controllers\Controller;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use \App\Common;

class PackageController extends Controller
{

    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = Package::class;
        $this->mTitle = 'Package';
        $this->slug = 'package';
        $this->pk = 'package_id';

        $this->gridCol = ['package_id', 'package_name' => 'Package name',    'created_at' => 'Created At'];
        $this->viewCol = ['package_id', 'package_name', 'principal_name',   'created_at' => 'Created At'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->forms = [
            'package_name' => ['rules' => 'required|maxlength:255'],
            //'status' => ['type' => 'radio', 'options' => $this->statusoptions,  'default' => 'a'],
        ];
        $this->filters = [
            'package_name' => ["width" => 12, 'title' => 'Package name', 'placeholder' => 'Seac'],
            //'status' => ['type' => 'select', "width" => 4, 'options' => $this->statusoptions, 'operator' => '=']
        ];
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
        dd($data);
        unset($data['_token']);
        unset($data['id']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function prepare_update($data)
    {

        $data['package_id'] = $data['id'];
        unset($data['_token']);
        unset($data['id']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }


    public function get_griddata($request, $gridCol, $mperm)
    {
        $data = $this->model::whereRaw($this->get_where($request, $gridCol));
        //$data = $this->model::whereRaw('1 = 1');
        $request['sortby'] = $request['sortby'] == 'id' ? 'package_id' : $request['sortby'];
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }


    public function edit($id, Request $request)
    {
        //Common::check_access();
        $data = $this->model::find($id);
        if (empty($data)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.'];
        }
        $formData = $this->get_forms($request, $data);
        $formData['formData'] = $data;
        //array_push($formData,[''])
        return $formData;
    }

    public function can_delete($value, $mperm)
    {
        return false;
    }

    public function can_view($value, $mperm)
    {
        return false;
    }
}
