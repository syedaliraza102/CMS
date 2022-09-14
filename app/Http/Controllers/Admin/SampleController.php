<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sample;
use App\Http\Controllers\Controller;
use App\Traits\AdminCrud;
use \App\Traits\Base;

class SampleController extends Controller
{

    use AdminCrud, Base;

    public function __construct()
    {
        $this->model = Sample::class;
        $this->mTitle = 'Sample';
        $this->slug = 'sample';

        $this->gridCol = ['id', 'thumb', 'title', 'slug', 'display_order', 'status', 'created_at' => 'Created At'];
        $this->viewCol = ['id', 'thumb', 'title', 'slug', 'display_order', 'status', 'created_at' => 'Created At'];
        $this->statusoptions = ['a' => 'Active', 'd' => 'Deactive'];
        $this->forms = [
            'title' => ['rules' => 'required|maxlength:255'],
            'slug' => ['rules' => 'required|maxlength:255'],
            'thumb' => ['type' => 'image', 'dir' => 'blog'],
            'sfile' => ['type' => 'file', 'dir' => 'blog'],
            'mfile' => ['type' => 'multiplefile', 'dir' => 'blog'],
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
            'title' => [],
            'slug' => [],
            'status' => ['type' => 'select', 'options' => $this->statusoptions, 'operator' => '=']
        ];
    }

    public function format_griddata($data, $mperm)
    {
        foreach ($data as $key => $value) {
            $data[$key]['ckbox'] = '<div class="checkbox"><label><input type="checkbox" class="chk_record" data-id="' . $value[$this->getpk()] . '" ></label></div>';
            $data[$key]['status'] = $this->gridswitch('status', 'a', 'd', $value);
            $data[$key]['thumb'] = $this->gridimage($value['thumb']);
            $data[$key]['actions'] = $this->action_formate($value, $mperm);
        }
        return $data;
    }
}
