<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Users;
use App\Http\Controllers\Controller;
use App\Traits\AdminCrud;
use \App\Traits\Base;

class UsersController extends Controller
{

    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = Users::class;
        $this->mTitle = 'Users';
        $this->slug = 'users';
        $this->gridCol = ['id',  'name', 'email',  'created_at' => 'Created At'];
        $this->viewCol = ['id', 'avatar', 'name', 'email', 'status', 'created_at' => 'Created At'];
        $this->statusoptions = ['active' => 'Active', 'pending' => 'Pending', 'block' => 'Block'];
        $this->forms = [
            'firstname' => ['rules' => 'required'],
            'lastname' => ['rules' => 'required'],
            'user_name' => ['rules' => 'required'],
            'avatar' => ['type' => 'image', 'dir' => 'user'],
            'email' => ['rules' => 'required|email'],
            'user_type' => ['type' => 'hidden', 'default' => 'c'],
            //'email_verified_at2' => ['type' => 'date', 'rules' => 'after:email_verified_at'],
            'status' => ['type' => 'radio', 'default' => 'pending', 'options' => $this->statusoptions],

        ];
        $this->filters = [
            'name' => ['width' => 4],
            'email' => ['width' => 4],
            'status' => ['type' => 'select', 'width' => 4, 'options' => $this->statusoptions, 'operator' => '='],
        ];
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            $data[$key]['ckbox'] = '<div class="checkbox"><label><input type="checkbox" class="chk_record" data-id="' . $value[$this->getpk()] . '" ></label></div>';

            $data[$key]['actions'] = '';
            // if ($data[$key]['status'] != 'block') {
            //     $data[$key]['actions'] = '<a class="btn btn-xs btn-danger" title="Block User" ng-click="addaction($event,' . "'blockuser'" . ',' . $value[$this->getpk()] . ')"> <i class="fa fa-ban" aria-hidden="true"></i> </a>';
            // }
            // if ($data[$key]['status'] != 'active') {
            //     $data[$key]['actions'] .= '<a class="btn btn-xs btn-success" title="Active User" ng-click="addaction($event,' . "'activeuser'" . ',' . $value[$this->getpk()] . ')"> <i class="fa fa-check" > </i> </a>';
            // }
            $data[$key]['actions'] .= $this->action_formate($value, $mperm);
            $data[$key]['avatar'] = $this->gridimage($value['avatar']);
            $data[$key]['name'] = $value['firstname'] . ' ' . $value['lastname'];
            $data[$key]['status'] =  $data[$key]['status'] == 'active' ? $this->gridlabel($data[$key]['status'], 'success') : $this->gridlabel($data[$key]['status'], 'danger');
        }
        return $data;
    }

    public function bulkaction_($slug, $request)
    {
        //return $slug;
        if ($slug == 'activeuser') {
            $obj = $this->model::find($request['id']);
            $obj->status = 'active';
            if ($obj->update()) {
                return ['flag' => 1, 'msg' =>  'User Activated Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        }
        if ($slug == 'blockuser') {
            $obj = $this->model::find($request['id']);
            $obj->status = 'block';
            if ($obj->update()) {
                return ['flag' => 1, 'msg' =>  'User Blocked Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        }
        if ($slug == 'bulk_block_user') {
            $request['ids'] = !empty($request['ids']) ? explode(',', $request['ids']) : [0];
            $obj = $this->model::whereIn($this->getpk(), $request['ids']);
            if ($obj->update(['status' => 'block'])) {
                return ['flag' => 1, 'msg' => (count($request['ids']) - 1) . ' User Blocked Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        }
        if ($slug == 'bulk_active_user') {
            $request['ids'] = !empty($request['ids']) ? explode(',', $request['ids']) : [0];
            $obj = $this->model::whereIn($this->getpk(), $request['ids']);
            if ($obj->update(['status' => 'active'])) {
                return ['flag' => 1, 'msg' => (count($request['ids']) - 1) . ' User Activated Successfully'];
            } else {
                return ['flag' => 2, 'msg' => ' Oopss ! Something Went Wrong'];
            }
        }
    }

    public function bulkactions()
    {
        return [
            ['slug' => 'delete', 'title' => 'Delete'],
            ['slug' => 'bulk_block_user', 'title' => 'Block User'],
            ['slug' => 'bulk_active_user', 'title' => 'Active User']
        ];
    }


    public function get_where($request, $gridCol)
    {
        $where = '1 = 1';
        $filterval =  !empty($_COOKIE['admin_datatable_' . $this->slug]) ? json_decode($_COOKIE['admin_datatable_' . $this->slug], true) : [];
        $filters = $this->filters ?? [];
        foreach ($filters as $key => $value) {
            $fval = $filterval["filter_" . $key] ?? '';
            if ($fval != '' && $fval != 'all' && $fval !== 0 && $fval != '0' && $key != 'name') {
                $operator = $value['operator'] ?? 'like';
                if ($operator != 'like') {
                    $where .= ' and  (' . $key . '  ' . $operator . ' "' . $fval . '"  )';
                } else {
                    $where .= ' and  (' . $key . ' like  "%' . $fval . '%"  )';
                }
            } else if ($fval != '' && $fval != 'all' && $fval !== 0 && $fval != '0' && $key == 'name') {
                $where .= ' and  ( concat(firstname," ",lastname) like   "%' . $fval . '%"  )';
            }
        }
        return $where;
    }

    function fakerdata($faker)
    {

        $obj = new $this->model;
        $obj->firstname = $faker->firstNameMale;
        $obj->lastname = $faker->lastName;
        $obj->user_type = 'c';
        //$obj->avatar = $this->dummy_image($faker, 'user', 'people');
        $obj->email = $faker->unique()->safeEmail;
        $obj->email_verified_at = $faker->dateTimeBetween('-3 years', 'now');
        $obj->password = \Hash::make('12345678');
        $obj->created_at = $faker->dateTimeBetween('-3 years', 'now');
        $obj->updated_at = $faker->dateTimeBetween('-3 years', 'now');
        return $obj->save();
    }
}
