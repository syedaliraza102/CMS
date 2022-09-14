<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Pages;
use App\Http\Controllers\Controller;
use App\Traits\AdminCrud;
use \App\Traits\Base;
use \App\Common;

class PagesController extends Controller
{

    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = Pages::class;
        $this->mTitle = 'Pages';
        $this->slug = 'pages';
        $this->mperm = Common::user_roles();
        $this->gridCol = ['id', 'banner_image', 'title', 'slug', 'status',  'created_at' => 'Created At'];
        $this->viewCol = ['id', 'banner_image', 'image', 'title', 'slug', 'status', 'display_order', 'created_at' => 'Created At'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->forms = [
            'title' => ['rules' => 'required'],
            'slug' => ['rules' => 'required'],
            'description' => ['type' => 'ckeditor', 'rules' => 'required'],
            'banner_image' => ['type' => 'image', 'dir' => 'pages'],
            'image' => ['type' => 'multipleimage', 'dir' => 'pages'],
            'display_order' => ['type' => 'number', 'default' => 100, 'rules' => 'required|min:0|max:100'],
            'status' => ['type' => 'radio', 'options' => $this->statusoptions,  'default' => 'a'],
            'seo' => [
                'type' => 'embeds',
                'fields' => [
                    'title' => ['rules' => 'required|minlength:10|maxlength:255'],
                    'keywords' => ['rules' => 'required|minlength:10|maxlength:255'],
                    'description' => ['rules' => 'required|minlength:10|maxlength:255']
                ]
            ]
        ];
        $this->filters = [
            'title' => ['width' => 4],
            'slug' => ['width' => 4],
            'status' => ['type' => 'select', 'options' => $this->statusoptions, 'operator' => '=', 'width' => 4]
        ];
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            if (!in_array($value['slug'], ['home', 'page-not-found'])) {
                $data[$key]['ckbox'] = '<div class="checkbox"><label><input type="checkbox" class="chk_record" data-id="' . $value[$this->getpk()] . '" ></label></div>';
            }
            $data[$key]['banner_image'] = $this->gridimage($value['banner_image']);
            $data[$key]['image'] = $this->gridimage($value['image']);
            $data[$key]['status'] = $this->gridswitch('status', 'a', 'd', $value);
            $data[$key]['actions'] = '';
            $data[$key]['actions'] .= '<a class="btn btn-xs btn-primary" target="_blank" title="Redirect To Page" href="' . url($value['slug']) . '" > <i class="fa fa-share-square-o" > </i> </a>';
            $data[$key]['actions'] .= $this->action_formate($value, $mperm);
        }
        return $data;
    }

    public function can_delete($value, $mperm)
    {
        if ($mperm['role'] != 'admin' && !in_array('admin.' . $this->slug . '.delete', $mperm['actions'])) {
            return false;
        }
        if (in_array($value['slug'], ['home', 'page-not-found'])) {
            return false;
        }
        return true;
    }

    function fakerdata($faker)
    {
        $obj = new $this->model;
        $obj->title = $faker->sentence(3, true);
        $obj->banner_image = $this->dummy_image($faker, 'pages', 'nightlife', 1920, 700);
        $obj->slug = $obj->title;
        $obj->image = [$this->dummy_image($faker, 'pages')];
        $obj->description = $faker->paragraph(100, true);
        $obj->seo = ['title' => $faker->sentence, 'keywords' => $faker->sentence, 'description' =>  $faker->paragraph(3, true)];
        $obj->status = $this->getrand($this->statusoptions);
        $obj->display_order = mt_rand(0, 100);
        $obj->created_at = $faker->dateTimeBetween('-3 years', 'now');
        $obj->updated_at = $faker->dateTimeBetween('-3 years', 'now');
        return $obj->save();
    }
}
