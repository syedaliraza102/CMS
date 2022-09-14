<?php

namespace App;

use App\Models\BlogPost;
use App\Models\BlogCategory;

class Common
{

    public static function module_list($key = '')
    {
        $rlist = [
            'index' => ['template' => '/base/index.html', 'controller' => 'baseController'],
            'add' => ['template' => '/base/add.html', 'controller' => 'baseFormController'],
            'edit' => ['template' => '/base/edit.html', 'controller' => 'baseFormController'],
            'view' => ['template' => '/base/view.html', 'controller' => 'baseViewController'],
        ];
        $list = [

            'users' => ['title' => 'Users', 'Controller' => 'UsersController'],
            //'students' => ['title' => 'Students', 'Controller' => 'StudentsController'],
            //'teachers' => ['title' => 'Teachers', 'Controller' => 'TeachersController'],
            'assignteacher' => ['title' => 'Assign Teacher', 'Controller' => 'AssignTeacherController'],
            //'schooladmins' => ['title' => 'School Admins', 'Controller' => 'SchoolAdminsController'],
            'userroles' => ['title' => 'Admin Roles', 'Controller' => 'UserRolesController'],
            'adminuser' => ['title' => 'Admin User', 'Controller' => 'AdminUserController'],
            'pages' => ['title' => 'CMS Pages', 'Controller' => 'PagesController'],
            'school' => ['title' => 'School', 'Controller' => 'SchoolController'],
            'class' => ['title' => 'Class', 'Controller' => 'ClassController'],
            'game' => ['title' => __('title.game'), 'Controller' => 'GameController'],
            'relaxationclass' => ['title' => 'Relaxation Class', 'Controller' => 'RelaxationClassController'],
            'level' => ['title' => 'level', 'Controller' => 'LevelController'],
            //'lesson' => ['title' => 'Lesson', 'Controller' => 'LessonController'],
            //'fragment' => ['title' => 'Fragments', 'Controller' => 'FragmentController'],
            'package' => ['title' => 'Packages', 'Controller' => 'PackageController'],
            'sample' => ['title' => 'sample', 'Controller' => 'SampleController'],
            'offlinehomework' => ['title' => 'Offline Homework', 'Controller' => 'OfflineHomeworkController'],

            // 'relaxationclass' => [
            //     'title' => 'Student Score', 'Controller' => 'RelaxationClassController',
            //     'routes' => [
            //         'index' => ['parms' => '?type', 'template' => '/relaxationclass/index.html', 'controller' => 'relaxationclassController']
            //     ],
            // ],

            'relaxationclass' => [
                'title' => 'Extra Training', 'Controller' => 'RelaxationClassController',
                'routes' => [
                    'index' => ['template' => '/base/index.html', 'controller' => 'baseController'],
                    'add' => ['template' => '/relaxationclass/add.html', 'controller' => 'relaxationclassFormController'],
                    'edit' => ['template' => '/school/edit.html', 'controller' => 'baseFormController'],
                    'view' => ['template' => '/base/view.html', 'controller' => 'baseViewController'],
                ],
            ],

            'school' => [
                'title' => 'School', 'Controller' => 'SchoolController',
                'routes' => [
                    'index' => ['template' => '/base/index.html', 'controller' => 'baseController'],
                    'add' => ['template' => '/school/add.html', 'controller' => 'baseFormController'],
                    'edit' => ['template' => '/school/edit.html', 'controller' => 'baseFormController'],
                    'view' => ['template' => '/base/view.html', 'controller' => 'baseViewController'],
                ],
            ],
            'schooladmins' => [
                'title' => 'School Admins', 'Controller' => 'SchoolAdminsController',
                'routes' => [
                    'index' => ['template' => '/schooladmins/index.html', 'controller' => 'baseController'],
                    'add' => ['template' => '/schooladmins/add.html', 'controller' => 'baseFormController'],
                    'edit' => ['template' => '/schooladmins/edit.html', 'controller' => 'baseFormController'],
                    'view' => ['template' => '/base/view.html', 'controller' => 'baseViewController'],
                ],
            ],
            'students' => [
                'title' => 'Students', 'Controller' => 'StudentsController',
                'routes' => [
                    'index' => ['template' => '/schooladmins/index.html', 'controller' => 'baseController'],
                    'add' => ['template' => '/schooladmins/add.html', 'controller' => 'baseFormController'],
                    'edit' => ['template' => '/schooladmins/edit.html', 'controller' => 'baseFormController'],
                    'view' => ['template' => '/base/view.html', 'controller' => 'baseViewController'],
                ],
            ],
            'teachers' => [
                'title' => 'Teachers', 'Controller' => 'TeachersController',
                'routes' => [
                    'index' => ['template' => '/schooladmins/index.html', 'controller' => 'baseController'],
                    'add' => ['template' => '/schooladmins/add.html', 'controller' => 'baseFormController'],
                    'edit' => ['template' => '/schooladmins/edit.html', 'controller' => 'baseFormController'],
                    'view' => ['template' => '/base/view.html', 'controller' => 'baseViewController'],
                ],
            ],
            // 'parents' => [
            //     'title' => 'School Admins', 'Controller' => 'SchoolAdminsController',
            //     'routes' => [
            //         'index' => ['template' => '/schooladmins/index.html', 'controller' => 'baseController'],
            //         'add' => ['template' => '/schooladmins/add.html', 'controller' => 'baseFormController'],
            //         'edit' => ['template' => '/schooladmins/edit.html', 'controller' => 'baseFormController'],
            //         'view' => ['template' => '/base/view.html', 'controller' => 'baseViewController'],
            //     ],
            // ],

            'game' => [
                'title' => __('title.game'), 'Controller' => 'GameController',
                'routes' => [
                    'index' => ['template' => '/game/index.html', 'controller' => 'baseController'],
                    'add' => ['template' => '/game/add.html', 'controller' => 'baseFormController'],
                    'edit' => ['template' => '/game/edit.html', 'controller' => 'baseFormController'],
                    'view' => ['template' => '/base/view.html', 'controller' => 'baseViewController'],
                ],
            ],
            'speaches' => ['title' => 'Speaches Lab', 'Controller' => 'SpeachesController'],
            'dashboard' => ['title' => 'Dashboard', 'Controller' => 'DashboardController'],
            'teacherrecording' => ['title' => 'Recording Studio', 'Controller' => 'RecordingController'],
            'studentrecording' => ['title' => 'Student Audio Studio', 'Controller' => 'StudentrecordingController'],

            'assigmentlesson' => ['title' => 'Assignment Lesson', 'Controller' => 'AssigmentLessonController'],
            'assigmentlesson' => [
                'title' => 'Assignment Lesson', 'Controller' => 'AssigmentLessonController',
                'routes' => [
                    'index' => ['parms' => '?clone', 'template' => '/assigmentlesson/index.html', 'controller' => 'assigmentlessonController'],
                    'add' => ['parms' => '?clone', 'template' => '/assigmentlesson/add.html', 'controller' => 'assigmentlessonFormController'],
                    'edit' => ['parms' => '?clone', 'template' => '/assigmentlesson/edit.html', 'controller' => 'assigmentlessonFormController'],
                    'view' => ['parms' => '?lesson_id?student_id', 'template' => '/base/view.html', 'controller' => 'baseViewController'],
                ],
            ],
            'multiplayerroom' => [
                'title' => 'Multiplayer Room', 'Controller' => 'MultiplayerRoomController',
                'routes' => [
                    'index' => ['parms' => '?clone', 'template' => '/multiplayerroom/index.html', 'controller' => 'multiplayerroomController'],
                    'add' => ['parms' => '?clone', 'template' => '/multiplayerroom/add.html', 'controller' => 'multiplayerroomFormController'],
                    'edit' => ['parms' => '?clone', 'template' => '/multiplayerroom/edit.html', 'controller' => 'multiplayerroomFormController'],
                    'view' => ['parms' => '?lesson_id?student_id', 'template' => '/base/view.html', 'controller' => 'baseViewController'],
                ],
            ],

            'relaxationdata' => [
                'title' => 'Relaxation Leader Board', 'Controller' => 'RelaxationDataController',
                'routes' => [
                    'index' => ['parms' => '?clone', 'template' => '/relaxationdata/index.html', 'controller' => 'relaxationdataController'],
                    'add' => ['parms' => '?clone', 'template' => '/relaxationdata/add.html', 'controller' => 'relaxationdataFormController'],
                    'edit' => ['parms' => '?clone', 'template' => '/relaxationdata/edit.html', 'controller' => 'relaxationdataFormController'],
                    'view' => ['parms' => '?lesson_id?student_id', 'template' => '/base/view.html', 'controller' => 'baseViewController'],
                ],
            ],

            'cards' => [
                'title' => 'Cards', 'Controller' => 'CardsController',
                'routes' => [
                    'index' => ['parms' => '?clone', 'template' => '/cards/index.html', 'controller' => 'cardsController'],
                    'add' => ['parms' => '?clone', 'template' => '/cards/add.html', 'controller' => 'cardsFormController'],
                    'edit' => ['parms' => '?clone', 'template' => '/cards/edit.html', 'controller' => 'cardsFormController'],
                    'view' => ['parms' => '?lesson_id?student_id', 'template' => '/base/view.html', 'controller' => 'baseViewController'],
                ],
            ],

            'fragment' => [
                'title' => 'Fragment', 'Controller' => 'FragmentController',
                'routes' => [
                    'index' => ['parms' => '?type?clone', 'template' => '/fragment/index.html', 'controller' => 'fragmentController'],
                    'add' => ['parms' => '?type?clone', 'template' => '/fragment/add.html', 'controller' => 'fragmentFormController'],
                    'edit' => ['parms' => '?type?clone', 'template' => '/fragment/edit.html', 'controller' => 'fragmentFormController'],
                    'view' => ['parms' => '?type?clone', 'template' => '/base/view.html', 'controller' => 'baseViewController'],
                ],
            ],
            'lesson' => [
                'title' => __('title.lesson'), 'Controller' => 'LessonController',
                'routes' => [
                    'index' => ['parms' => '?type?clone', 'template' => '/lesson/index.html', 'controller' => 'baseController'],
                    'add' => ['parms' => '?type?clone', 'template' => '/lesson/add.html', 'controller' => 'lessonFormController'],
                    'edit' => ['parms' => '?type?clone', 'template' => '/lesson/edit.html', 'controller' => 'lessonFormController'],
                    'view' => ['parms' => '?type?clone', 'template' => '/base/view.html', 'controller' => 'baseViewController'],
                ],
            ],

            'sitesetting' => [
                'title' => 'Site Setting', 'Controller' => 'SitesettingController',
                'routes' => [
                    'index' => ['template' => '/sitesetting/index.html', 'controller' => 'baseController']
                ],
            ],

            'schoolsetting' => [
                'title' => 'School Setting', 'Controller' => 'SchoolsettingController',
                'routes' => [
                    'index' => ['template' => '/schoolsetting/index.html', 'controller' => 'schoolsettingController']
                ],
            ],
            'points' => [
                'title' => 'Student Score', 'Controller' => 'PointsController',
                'routes' => [
                    'index' => ['parms' => '?type', 'template' => '/points/index.html', 'controller' => 'pointsController']
                ],
            ],
            'teacherstudentlessonscore' => [
                'title' => 'Student Score', 'Controller' => 'TeacherStudentLessonScoreController',
                'routes' => [
                    'index' => ['parms' => '?type', 'template' => '/teacherstudentlessonscore/index.html', 'controller' => 'teacherStudentLessonScoreController']
                ],
            ],
            'studentlessonscore' => [
                'title' => 'Student Score', 'Controller' => 'StudentLessonScoreController',
                'routes' => [
                    'index' => ['parms' => '?type', 'template' => '/studentlessonscore/index.html', 'controller' => 'studentLessonScoreController']
                ],
            ],
            'subscription' => [
                'title' => 'Subscription', 'Controller' => 'SubscriptionController',
                'routes' => [
                    'index' => ['template' => '/subscription/index.html', 'controller' => 'subscriptionController']
                ],
            ],
            'userprofile' => [
                'title' => 'User Profile', 'Controller' => 'userprofileController',
                'routes' => [
                    'index' => ['template' => '/userprofile/index.html', 'controller' => 'userprofileController']
                ],
            ],
        ];
        foreach ($list as $key => $value) {
            $list[$key]['pk'] = $list[$key]['pk'] ?? 'id';
            $list[$key]['routes'] = $list[$key]['routes'] ?? $rlist;
            foreach ($list[$key]['routes'] as $key2 => $value2) {
                $list[$key]['routes'][$key2]['controller'] = str_replace('base', $key, $value2['controller']);
            }
        }
        //$list['school']['routes']['index']['template'] = '/school/index.html';
        //dd($list['school']['routes']['index']);

        return $list;
    }

    public static function all_routes()
    {
        $data = Self::module_list();
        $rlist = [];
        foreach ($data as $key => $value) {
            if ($key != 'userprofile' && $key != 'sitesetting') {
                $rlist['admin.' . $key . ".index"] =  'Show ';
                $rlist['admin.' . $key . ".add"] =  'Add';
                $rlist['admin.' . $key . ".update"] =  'Update';
                $rlist['admin.' . $key . ".delete"] =  'delete';
                $rlist['admin.' . $key . ".view"] =  'view';
                $rlist['admin.' . $key . ".export"] =  'export';
                $rlist['admin.' . $key . ".bulkaction"] =  'Bulk Action';
            }
        }
        $rlist['admin.userprofile'] =  'User profile';
        $rlist['admin.sitesetting'] =  'Site Settings';
        return $rlist;
    }

    public static function role_routes()
    {
        $data = Self::module_list();
        foreach ($data as $key => $value) {
            if ($key != 'userprofile' && $key != 'sitesetting') {
                $rlist[$value['title']]['admin.' . $key . ".index"] =  'Show ';
                $rlist[$value['title']]['admin.' . $key . ".add"] =  'Add';
                $rlist[$value['title']]['admin.' . $key . ".update"] =  'Update';
                $rlist[$value['title']]['admin.' . $key . ".delete"] =  'delete';
                $rlist[$value['title']]['admin.' . $key . ".view"] =  'view';
                $rlist[$value['title']]['admin.' . $key . ".export"] =  'Export';
                $rlist[$value['title']]['admin.' . $key . ".bulkaction"] =  'Bulk Action';
            }
        }
        $rlist['Newsletters']['admin.sendnewsletters'] =  'Send Newsletters';
        $rlist['User Profile']['admin.userprofile'] =  'User profile';
        $rlist['Site Setting']['admin.sitesetting'] =  'Site Settings';
        $rlist['Notification'] = ['admin.notification' =>  'Admin Notification', 'admin.bulkaction' =>  'Bulk Action', 'admin.markasread' =>  'Mark As Read'];
        return $rlist;
    }


    public static function sidebar()
    {
        $data = [
            ['slug' => 'home', 'icon' => 'fa fa-dashboard', 'title' => 'Dashboard', 'display_order' => 1, 'phpslug' => 'admin.home'],
            [
                'slug' => 'sitemangement',
                'title' => 'Site Managemant',
                'display_order' => 2,
                'child' => [
                    ['slug' => 'sitesetting', 'title' => 'Site setting'],
                    ['slug' => 'sample', 'title' => 'Sample']
                ]
            ],
            ['slug' => 'school', 'icon' => 'fa fa-dashboard', 'title' => 'School', 'display_order' => 1, 'phpslug' => 'admin.school'],
            // [
            //     'slug' => 'usersmagemant',
            //     'title' => 'Admin Magemant',
            //     'display_order' => 3,
            //     'child' => [
            //         ['slug' => 'adminuser', 'title' => 'Admin Users'],
            //         ['slug' => 'userroles', 'title' => 'Admin Roles'],
            //         ['slug' => 'notification', 'title' => 'Notification'],
            //     ]
            // ],

            // [
            //     'slug' => 'Content Management',
            //     'child' => [
            //         ['slug' => 'pages', 'title' => 'CMS Pages'],
            //         ['slug' => 'blogcategory', 'title' => 'Blog Category'],
            //         ['slug' => 'blogpost', 'title' => 'Blog Post']
            //     ]
            // ],
            // [
            //     'slug' => 'categorymangement',
            //     'title' => 'Items mangement',
            //     'child' => [
            //         ['slug' => 'category', 'title' => 'Category'],
            //         ['slug' => 'subcategory', 'title' => 'Sub Category'],
            //         ['slug' => 'items', 'title' => 'Items']
            //     ]
            // ],
            // [
            //     'slug' => 'crm',
            //     'title' => 'User & Newsletters',
            //     'child' => [
            //         ['slug' => 'users', 'title' => 'Users', 'icon' => 'fa fa-users'],
            //         ['slug' => 'newsletters', 'title' => 'Newsletters'],
            //         ['slug' => 'subscribers', 'title' => 'Subscribers']
            //     ]
            // ]
        ];
        $user_roles =  Self::user_roles();
        foreach ($data as $key => $value) {
            $data[$key] = Self::formate_node($value['slug'], $value, $user_roles);
            if (!empty($data[$key]['child'])) {
                foreach ($data[$key]['child'] as $key2 => $value2) {
                    if ($user_roles['role'] != 'admin' && !in_array($value2['phpslug'], $user_roles['actions'])) {
                        unset($data[$key]['child'][$key2]);
                    }
                }
                if (empty($data[$key]['child'])) {
                    unset($data[$key]);
                }
            } else {
                if ($user_roles['role'] != 'admin' && !in_array($data[$key]['phpslug'], $user_roles['actions'])) {
                    unset($data[$key]);
                }
            }
        }
        array_multisort(array_column($data, 'display_order'), SORT_ASC, $data);

        return $data;
    }

    public static function is_file($path = '')
    {
        $file = str_replace('/', DIRECTORY_SEPARATOR, base_path('public/' . $path));
        return (file_exists($file)) && !is_dir($file);
    }
    public  static function formate_node($key, $value)
    {
        $value['title'] = !empty($value['title']) ? $value['title'] : ucfirst($key);
        $value['icon'] = !empty($value['icon']) ? $value['icon'] : 'fa fa-bars';
        $value['display_order'] = !empty($value['display_order']) ? $value['display_order'] : 100;
        $value['phpslug'] = $value['phpslug'] ?? 'admin.' . $key . '.index';
        if (!empty($value['child'])) {
            foreach ($value['child'] as $key2 => $value2) {
                $value['child'][$key2] = Self::formate_node($value2['slug'], $value2);
            }
        }
        return $value;
    }

    public static function format_fileval($type, $value)
    {
        $final = [];
        if (!empty($value)) {
            if ($type == 'image' || $type == 'file') {
                $temp = self::format_sfile($value);
                if (!empty($temp)) {
                    array_push($final, $temp);
                }
            } else {
                $value = is_array($value) ? $value : [];
                foreach ($value as $key => $value2) {
                    $temp = self::format_sfile($value2);
                    if (!empty($temp)) {
                        array_push($final, $temp);
                    }
                }
            }
        }
        return $final;
    }

    public static function format_sfile($value)
    {
        $temp = [];
        $img_ext = ['png', 'gif', 'jpg', 'jpeg'];
        $fnm = pathinfo($value, PATHINFO_BASENAME);
        $ext = strtolower(pathinfo($value, PATHINFO_EXTENSION));
        $file = str_replace('/', DIRECTORY_SEPARATOR, base_path('public/' . $value));
        if (file_exists($file) && !is_dir($file)) {
            $temp = [
                'filename' => $fnm,
                'link' => url($value),
                'viewpath' => in_array($ext, $img_ext) ? url('public/' . $value) : url('public/images/ext/' . $ext . ".png"),
                'path' => $value,
            ];
        }
        return $temp;
    }

    public static function format_colname($name)
    {
        return str_replace(['_', '-', '[', ']'], [' ', ' ', ' ', ''], ucfirst($name));
    }

    public static function format_feilddata($name, $value, $attr)
    {
        $data = [];
        $data['name'] = $name;
        $data['type'] = $attr['type'] ?? 'text';
        $data['title'] = $attr['title'] ?? str_replace(['_', '-', '[', ']'], [' ', ' ', ' ', ''], ucfirst($name));
        $data['default'] = $attr['default'] ?? '';
        $data['value'] =  $value != '' ? $value : $data['default'];
        $data['helper'] = $attr['helper'] ?? '';
        $data['fattr'] = $attr['fattr'] ?? '';
        $data['placeholder'] = $attr['placeholder'] ?? 'Enter ' . $data['title'];
        $data['msg'] = $attr['msg'] ?? [];
        $data['rules'] = !empty($attr['rules']) ? Self::rules($data['type'], $data['title'], $attr['rules'], $data['msg']) : '';
        $data['id'] = $attr['id'] ?? Self::formate_id($data['name'] . "_" . $data['type'] . "_id");
        $data['class'] = $attr['class'] ?? '';
        $data['titlewidth'] = $attr['titlewidth'] ?? '2';
        $data['feildwidth'] = $attr['feildwidth'] ?? '8';
        if ($data['type'] == 'select' || $data['type'] == 'checkbox' || $data['type'] == 'radio' || $data['type'] == 'multipleselect' || $data['type'] == 'tags' || $data['type'] == 'stags') {
            $data['options'] = $attr['options'] ?? [];
        } else if ($data['type'] == 'image' || $data['type'] == 'multipleimage' || $data['type'] == 'file' || $data['type'] == 'multiplefile') {
            $data['dir'] = $attr['dir'] ?? 'upload';
            $data['value'] = Self::format_fileval($data['type'], $data['value']);
        } else if ($data['type'] == 'embeds') {
            $data['fields'] = $attr['fields'] ?? [];
            $data['value'] = is_array($data['value']) ? $data['value'] : [];
            $data['fields_str'] = '';
            foreach ($data['fields'] as $key => $value) {
                $fval = $data['value'][$key] ?? '';
                $data['fields_str'] .= Self::render_field($name . '[' . $key . ']', $fval, $value);
            }
        } else if ($data['type'] == 'tabs') {
            $data['value'] = is_array($data['value']) ? $data['value'] : [];
            $data['tabs'] = $attr['tabs'] ?? [];
            foreach ($data['tabs'] as $key => $value) {
                $data['tabs'][$key]['title'] = $value['title'] ?? 'Tab ' . ($key + 1);
                $data['tabs'][$key]['tab_id'] = $value['tab_id'] ?? Self::formate_id($data['tabs'][$key]['title']);
                $value['fields'] = $value['fields'] ?? [];
                $data['tabs'][$key]['fields_str'] = '';
                foreach ($value['fields'] as $key2 => $value2) {
                    $fval = $data['value'][$key2] ?? '';
                    $data['tabs'][$key]['fields_str'] .= Self::render_field($key2, $fval, $value2);
                }
            }
        } else if ($data['type'] == 'html') {
            $data['htmldata'] = $attr['htmldata'] ?? [];
            $data['view'] = $attr['view'] ?? '';
        }
        return $data;
    }

    public static function formate_id($name)
    {
        return  strtolower(str_replace(['[', ']', ' '], ['_', '_', '_'], $name));
    }

    public static function render_field($name, $value, $attr)
    {
        $data = Self::format_feilddata($name, $value, $attr);
        
        return view('admin.feilds.' . $data['type'], ['data' => $data])->render();
    }

    public static function render_tabs($tabs, $value = [])
    {
        $fdata = [
            'type' => 'tabs',
            'values' => $value,
            'tabs' => $tabs
        ];

        return self::render_field('test', $value, $fdata);
    }

    public static function user_roles()
    {
        $role = session('admin_role', ''); //$adminuser['admin_role'] ?? '';
        $actions = session('admin_role_actions', []); //$adminuser['admin_role_data']['actions'] ?? [];
        array_push($actions, 'admin.home');
        return ['role' => $role, 'actions' => $actions];
    }

    public static function can_access($route_name)
    {
        if (session('admin_role') == 'admin') {
            return 'true';
        }
        return in_array($route_name, session('admin_role_actions', [])) ? 'true' : 'false';
    }

    public static function check_access($route_name = '')
    {
        $route_name = $route_name != '' ? $route_name : \Request::route()->getName();
        if (Self::can_access($route_name) == 'false') {
            echo json_encode(['flag' => 2, 'msg' => 'Access Denied']);
            die;
        }
    }

    public static function seodata($seo, $pagedata)
    {
        $final = [];
        if (!empty($pagedata)) {
            $final['title'] = $seo['title'] ?? '';
            $final['description'] = $seo['description'] ?? '';
            $final['keywords'] = $seo['keywords'] ?? '';
        } else {
            $final['title'] = 'Page Not Found';
            $final['description'] = 'Page Not Found';
            $final['keywords'] = 'Page Not Found';
        }
        return $final;
    }

    public static function rules($type, $title, $rules, $msg = [])
    {
        $rules = !empty($rules) ?  explode('|', $rules)  : [];
        $msg = !empty($rules) && is_array($msg) ?  $msg  : [];
        $rules_str = '';
        foreach ($rules as $key => $value) {
            $rarr = explode(':', $value);
            $parms = !empty($rarr[1]) ?  explode(',', $rarr[1]) : [];
            $rulename =  $rarr[0] ?? '';
            $rmsg =  $msg[$rulename] ??  '';
            if (in_array($rulename, ['required', 'email', 'digits', 'numeric', 'alpha_num'])) {
                if ($rulename == 'required' && in_array($type, ['image', 'multipleimage', 'file', 'multiplefile'])) {
                    $rules_str .= ' imgreq="' . (strpos($rulename, 'image') !== false ? 'file' : 'image') . '" ';
                } else {
                    $rules_str .= ' data-rule-' . $rulename . '="true" ';
                }
            } else if (in_array($rulename, ['min', 'max', 'minlength', 'maxlength'])) {
                $parms[0] = $parms[0] ?? 0;
                $rules_str .= ' data-rule-' . $rulename . '="' . $parms[0] . '" ';
            } else if (in_array($rulename, ['same'])) {
                $rules_str .= ' data-rule-' . $rulename . '="' . $parms[0] . '" ';
            } else if (in_array($rulename, ['between'])) {
                $parms[0] = $parms[0] ?? 0;
                $parms[1] = $parms[1] ?? 1;
                $rules_str .= ' between="' . $parms[0] . ' to ' . $parms[1] . '" ';
            } else if (in_array($rulename, ['after', 'after_or_equal', 'before', 'before_or_equal'])) {
                $parms[0] = $parms[0] ?? 0;
                $rules_str .= ' ' . $rulename . '="' . $parms[0] . '" ';
            }
            $rules_str .= ' data-msg-' . $rulename . "='" . $rmsg . "'";
        }
        return $rules_str;
    }
    public static function blog_tag_counts()
    {
        $data = [];
        foreach (BlogPost::where('status', 'a')->select('tags')->get()->toArray() as $key => $value) {
            if (!empty($value['tags'])) {
                foreach ($value['tags'] as $key2 => $value2) {
                    $value2 = ucfirst(strtolower(trim($value2)));
                    $data[$value2] = !empty($data[$value2]) ? $data[$value2] + 1 : 1;
                }
            }
        }
        return $data;
    }

    public static function blog_sidebar()
    {
        $recent_post = BlogPost::where('status', 'a')->orderBy('created_at', 'desc')->whereHas('category')->limit(10)->get()->toArray();
        $blog_category = BlogCategory::where('status', 'a')->withCount('post')->whereHas('post')->orderBy('display_order')->get()->toArray();
        $blog_tag_counts = Self::blog_tag_counts();

        return [
            'recent_post' => $recent_post,
            'blog_category' => $blog_category,
            'blog_tag_counts' => $blog_tag_counts,
        ];
    }
    public static function img_filter($path)
    {
        $fullpath = str_replace('/', DIRECTORY_SEPARATOR, public_path($path));
        return file_exists($fullpath) &&  !is_dir($fullpath) ? $path : '';
    }

    public static function show_more($data, $link = '', $len = 275)
    {
        return strlen($data) <= $len ? $data : substr($data, 0, $len) . '....<a href="' . $link . '">Show More </a>';
    }

    public static function del_file($path)
    {
        if (!empty($path)) {
            if (!is_array($path)) {
                $file = str_replace('/', DIRECTORY_SEPARATOR, public_path($path));
                if (file_exists($file) && !is_dir($file)) {
                    unlink($file);
                }
            } else {
                foreach ($path as $key => $value) {
                    $file = str_replace('/', DIRECTORY_SEPARATOR, public_path($value));
                    if (file_exists($file) && !is_dir($file)) {
                        unlink($file);
                    }
                }
            }
        }
    }
}
