<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait Base
{
    public function module_list($key = '')
    {
        $rlist = [
            'index' => ['template' => '/base/index.html', 'controller' => 'baseController'],
            'add' => ['template' => '/base/add.html', 'controller' => 'baseFormController'],
            'edit' => ['template' => '/base/edit.html', 'controller' => 'baseFormController'],
            'view' => ['template' => '/base/view.html', 'controller' => 'baseViewController']
        ];
        $list = [
            'category' => ['title' => 'Category', 'Controller' => 'CategoryController'],
            'items' => ['title' => 'Items', 'Controller' => 'ItemsController'],
            'users' => ['title' => 'Users', 'Controller' => 'UsersController'],
            'userroles' => ['title' => 'User Roles', 'Controller' => 'UserRolesController'],
            'adminuser' => ['title' => 'Admin User', 'Controller' => 'AdminUserController'],
            'sitesetting' => [
                'title' => 'Site Setting', 'Controller' => 'SitesettingController',
                'routes' => [
                    'index' => ['template' => '/sitesetting/index.html', 'controller' => 'baseController']
                ],
            ],
            'userprofile' => [
                'title' => 'User Profile', 'Controller' => 'userprofileController',
                'routes' => [
                    'index' => ['template' => '/userprofile/index.html', 'controller' => 'userprofileController']
                ],
            ]
        ];
        foreach ($list as $key => $value) {
            $list[$key]['pk'] = $list[$key]['pk'] ?? 'id';
            $list[$key]['routes'] = $list[$key]['routes'] ?? $rlist;
            foreach ($list[$key]['routes'] as $key2 => $value2) {
                $list[$key]['routes'][$key2]['controller'] = str_replace('base', $key, $value2['controller']);
            }
        }

        return $list;
    }
}
