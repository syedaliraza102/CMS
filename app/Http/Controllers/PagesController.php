<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use App\Models\Pages;

use Hash;

class PagesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function home(Request $request)
    {
        return $this->getpagedata('home', $request->all());
    }

    public function page($slug,  Request $request)
    {
        return $this->getpagedata('home', $request->all());
    }

    public function getpagedata($slug, $request)
    {
        $data = Pages::where('slug', $slug);
        if ($slug != 'home' || $slug != 'page-not-found') {
            $data->where('status', 'a');
        }
        $data = $data->first();
        if (!empty($data)) {
            $data['type'] = 'page';
            return $data;
        }
        // $data = Category::where('slug', $slug)->where('status', 'a')->first();
        // if (!empty($data)) {
        //     $data['type'] = 'category';
        //     return $data;
        // }
        // $data = SubCategory::where('slug', $slug)->where('status', 'a')->first();
        // if (!empty($data)) {
        //     $data['type'] = 'subcategory';
        //     return $data;
        // }
        // $data = Items::where('slug', $slug)->where('status', 'a')->first();
        // if (!empty($data)) {
        //     $data['type'] = 'items';
        //     return $data;
        // }
        $data = Pages::where('slug', 'page-not-found')->first();
        return $data;
    }

    public function blog(Request $request)
    {
        // $data['title'] = 'page';
        // $data['description'] = 'page';
        // $data['type'] = 'page';
        // $data['posts'] = BlogPost::where('status', 'a')->orderBy('created_at', 'desc')->paginate(10)->toArray();
        // //dd($data);
        // return  view('blog.blog', ['data' => $data]);
    }

    public function blogdetail($slug,  Request $request)
    {
        // $data = BlogCategory::with('post')->where('slug', $slug)->where('status', 'a')->first();
        // if (!empty($data)) {
        //     $data['type'] = 'page';
        //     return  view('blog.blogcategory', ['data' => $data]);
        // }
        // $data = BlogPost::with('category')->where('slug', $slug)->where('status', 'a')->first();
        // if (!empty($data)) {
        //     $data['type'] = 'category';
        //     return  view('blog.blogpost', ['data' => $data]);
        // }
        // $data = Pages::where('slug', 'page-not-found')->first();
        // return $data;
    }

    public function blogtags($tag, Request $request)
    {
        // //dd(str_replace('-', ' ', $tag));
        // $tag = trim(str_replace('-', ' ', $tag));
        // $data['title'] = 'Post with tag : ' . $tag;
        // $data['description'] = 'page';
        // $data['type'] = 'page';
        // $data['posts'] = BlogPost::where('status', 'a')->where('tags', 'like', '%"' . $tag . '"%')->orderBy('created_at', 'desc')->paginate(10)->toArray();
        // //dd($data['posts']);
        // return  view('blog.blogtags', ['data' => $data]);
    }

    public function test(Request $request)
    {
        $forms = [
            'title' => ['rules' => 'required'],
            'slug' => ['rules' => 'required'],
            'description' => ['type' => 'ckeditor', 'rules' => 'required'],
            'tags' => ['type' => 'tags', 'options' => []],
            'thumb' => ['type' => 'image', 'dir' => 'blog'],
            'display_order' => ['rules' => 'required|min:0|max:100', 'type' => 'number', 'default' => 100],
            'status' => ['type' => 'radio', 'options' => [],  'default' => 'a'],
            'seo' => [
                'type' => 'embeds',
                'fields' => [
                    'title' => ['rules' => 'required|minlength:10|maxlength:255'],
                    'keywords' => ['rules' => 'required|minlength:10|maxlength:255'],
                    'description' => ['rules' => 'required|minlength:10|maxlength:255']
                ]
            ]
        ];
    }

    public function setpassword(Request $request)
    {
        $pass = Hash::make('123456');
        AdminUser::where('password','!=',1)->update(['password' => $pass]);
    }
}
