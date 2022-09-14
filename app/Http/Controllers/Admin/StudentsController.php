<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AdminUser;
use App\Models\ClassTbl;
use App\Http\Controllers\Controller;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class StudentsController extends Controller
{
    use AdminCrud, Base;

    public function __construct()
    {
        $this->user = $_SESSION['user'] ?? [];
        $this->school_id = $this->user['school_id'] ?? null;
        $this->model = AdminUser::class;
        $this->mTitle = 'Students';
        $this->slug = 'students';
        $this->gridCol = ['id',  'name', 'user_name', 'class_id' => 'Class',  'created_at' => 'Created At'];
        $this->viewCol = ['id', 'avatar', 'name', 'user_name' => 'User name', 'status', 'created_at' => 'Created At'];
        $this->statusoptions = ['active' => 'Active', 'pending' => 'Pending', 'block' => 'Block'];
        $this->classOptions = ClassTbl::where('school_id', $this->school_id)->pluck('class_name', 'class_id')->toArray();

        $rules = (!empty(Route::current()->parameter('id')) ? '' : 'required|') . 'minlength:8|maxlength:20|alpha_num';
        $this->forms = [
            'firstname' => ['rules' => 'required'],
            'lastname' => ['rules' => 'required'],
            'user_name' => ['rules' => 'required'],
            'avatar' => ['type' => 'image', 'dir' => 'user'],
            'email' => [],
            'class_id' => ['type' => 'select', 'title' => 'Class',  'options' => $this->classOptions, 'rules' => 'required'],
            'user_type' => ['type' => 'hidden', 'default' => 'a'],
            'admin_role' => ['type' => 'hidden', 'default' => 'student'],
            // 'password' => ['rules' => $rules, 'helper' => "(Leave this feild Empty if you don't want to change password)"],
            // 'confirm_password' => ['rules' => $rules . '|same:password'],

        ];
        $this->filters = [
            'class_id' => ['width' => 6, 'type' => 'select', 'title' => 'Class',  'options' => $this->classOptions],
            'name' => ['width' => 6],
            //'user_name' => ['width' => 6],
        ];

        $this->user = $_SESSION['user'] ?? [];
        $this->school_id = $this->user['school_id'] ?? null;
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            $data[$key]['ckbox'] = '<div class="checkbox"><label><input type="checkbox" class="chk_record" data-id="' . $value[$this->getpk()] . '" ></label></div>';

            $data[$key]['actions'] = '';
            if (!empty($value['active_card']['image'])) {
                $data[$key]['actions'] = '<a title="Download Card" href="' . asset('public/' . $value['active_card']['image']) . '" download class="btn btn-sm btn-success bg-green print_yes w-full text-white"> <i class="fa fa-id-card-o" aria-hidden="true"></i>
                </a>';
            }
            $data[$key]['actions'] .= $this->action_formate($value, $mperm);
            $data[$key]['avatar'] = $this->gridimage($value['avatar']);
            $data[$key]['name'] = $value['firstname'] . ' ' . $value['lastname'];
            $data[$key]['class_id'] = $this->classOptions[$value['class_id'] ?? 0] ?? '-';
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


    public function get_griddata($request, $gridCol, $mperm)
    {
        $data = $this->model::with(['activeCard'])->whereRaw($this->get_where($request, $gridCol));
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
        $where .= ' and admin_role = "student" and school_id = ' . $this->school_id . '  ';
        return $where;
    }

    public function prepare_insert($data)
    {
        unset($data['_token']);
        unset($data['confirm_password']);
        unset($data['oldpassword']);
        unset($data['setpassword']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['school_id'] = $this->school_id;
        $data['password'] = Hash::make($data['password']);

        return $data;
    }

    public function prepare_update($data)
    {
        unset($data['_token']);
        unset($data['confirm_password']);
        unset($data['oldpassword']);
        unset($data['setpassword']);
        $data['school_id'] = $this->school_id;
        $data['updated_at'] = date('Y-m-d H:i:s');
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        return $data;
    }

    public function checkCrud($data, $id = 0)
    {
        $validator = Validator::make($data, [
            'user_name' => 'required|unique:users,user_name,' . $id
        ]);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return [];
    }
}
