<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AdminUser;
use App\Models\UserRoles;
use App\Http\Controllers\Controller;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use Hash;
use App\Common;
use Illuminate\Support\Facades\Route;

class AdminUserController extends Controller
{

    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = AdminUser::class;
        $this->mTitle = 'AdminUser';
        $this->slug = 'adminuser';
        $this->gridCol = ['id', 'avatar', 'name', 'email', 'admin_role', 'status', 'created_at' => 'Created At'];
        $this->viewCol = ['id', 'avatar', 'name', 'email', 'admin_role', 'status', 'created_at' => 'Created At'];
        $this->statusoptions = ['active' => 'Active', 'pending' => 'Pending', 'block' => 'Block'];
        $this->usertypeoptions = UserRoles::where('slug', '!=', 'admin')->pluck('title', 'slug')->toArray();

        $rules = (!empty(Route::current()->parameter('id')) ? '' : 'required|') . 'minlength:8|maxlength:20|alpha_num';

        $this->forms = [
            'firstname' => ['rules' => 'required'],
            'lastname' => ['rules' => 'required'],
            'avatar' => ['type' => 'image', 'dir' => 'user'],
            'email' => ['rules' => 'required|email'],
            'password' => ['rules' => $rules, 'helper' => "(Leave this feild Empty if you don't want to change password)"],
            'confirm_password' => ['rules' => $rules . '|same:password'],
            'user_type' => ['type' => 'hidden', 'default' => 'a'],
            'status' => ['type' => 'select', 'default' => 'pending', 'options' => $this->statusoptions],
            'admin_role' => ['type' => 'select', 'options' => $this->usertypeoptions, 'rules' => 'required'],
        ];
        $this->filters = [
            'name' => ['width' => 3],
            'email' => ['width' => 3],
            'status' => ['type' => 'select', 'options' => $this->statusoptions, 'operator' => '='],
            'admin_role' => ['type' => 'select', 'options' => $this->usertypeoptions, 'operator' => '='],
        ];
    }

    public function get_griddata($request, $gridCol, $mperm)
    {
        $data = $this->model::where('admin_role', '!=', 'admin')->whereRaw($this->get_where($request, $gridCol));
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            $data[$key]['ckbox'] = '<div class="checkbox"><label><input type="checkbox" class="chk_record" data-id="' . $value[$this->getpk()] . '" ></label></div>';

            $data[$key]['actions'] = '';
            if ($data[$key]['status'] != 'block') {
                $data[$key]['actions'] = '<a class="btn btn-xs btn-danger" title="Block User" ng-click="addaction($event,' . "'blockuser'" . ',' . $value[$this->getpk()] . ')"> <i class="fa fa-ban" aria-hidden="true"></i> </a>';
            }
            if ($data[$key]['status'] != 'active') {
                $data[$key]['actions'] .= '<a class="btn btn-xs btn-success" title="Active User" ng-click="addaction($event,' . "'activeuser'" . ',' . $value[$this->getpk()] . ')"> <i class="fa fa-check" > </i> </a>';
            }
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

    public function edit($id, Request $request)
    {

        //Common::check_access();
        $data = $this->model::find($id);
        unset($data['password']);
        if (empty($data)) {
            return ['flag' => 4, 'msg' => $this->mTitle . ' Not found.'];
        }
        return $this->get_forms($request, $data);
    }

    public function prepare_insert($data)
    {
        unset($data['_token']);
        unset($data['confirm_password']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['password'] = Hash::make($data['password']);

        return $data;
    }

    public function prepare_update($data)
    {
        unset($data['_token']);
        unset($data['confirm_password']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        return $data;
    }

    function fakerdata($faker)
    {
        $obj = new $this->model;
        $obj->firstname = $faker->firstNameMale;
        $obj->lastname = $faker->lastName;
        $obj->user_type = 'a';
        $obj->avatar = $this->dummy_image($faker, 'user', 'people');
        $obj->status = $this->getrand($this->statusoptions);
        $obj->email = $faker->unique()->safeEmail;
        $obj->admin_role = $this->getrand($this->usertypeoptions);
        $obj->email_verified_at = $faker->dateTimeBetween('-3 years', 'now');
        $obj->password = \Hash::make('12345678');
        $obj->remember_token = '';
        $obj->created_at = $faker->dateTimeBetween('-3 years', 'now');
        $obj->updated_at = $faker->dateTimeBetween('-3 years', 'now');
        return $obj->save();
    }
}
