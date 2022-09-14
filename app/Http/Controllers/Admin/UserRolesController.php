<?php

namespace App\Http\Controllers\Admin;


use App\Models\UserRoles;
use App\Http\Controllers\Controller;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use \App\Common;

class UserRolesController extends Controller
{

    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = UserRoles::class;
        $this->mTitle = 'Admin Roles';
        $this->slug = 'userroles';
        $this->gridCol = ['id', 'title', 'slug', 'created_at' => 'Created At'];
        $this->viewCol = ['id', 'title', 'slug', 'actions', 'created_at' => 'Created At'];
        $this->forms = [
            'title' => ['rules' => 'required'],
            'slug' => ['rules' => 'required'],
            'actions' => ['type' => 'html', 'titlewidth' => 3, 'feildwidth' => 9, 'view' => 'admin.userrole', 'htmldata' => ['routes' => Common::role_routes()]]
        ];
    }

    public function get_griddata($request, $gridCol, $mperm)
    {
        $data = $this->model::where('slug', '!=', 'admin')->whereRaw($this->get_where($request, $gridCol));
        if (!empty($request['sortby']) && !empty($request['sortdir'])) {
            $data->orderBy($request['sortby'], $request['sortdir']);
        }
        $data = $data->paginate($request['limit'])->toArray();
        $data['data'] = $this->format_griddata($data['data'], $mperm);
        return $data;
    }
}
