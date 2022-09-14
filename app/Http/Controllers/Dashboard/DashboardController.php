<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use Validator;
use App\Models\Subscribers;
use Hash;

class DashboardController extends Controller
{

    public function __construct()
    { }


    public function index(Request $request)
    {
        return view('dashboard.index');
    }

    public function account_settings(Request $request)
    {
        $data = \Auth::user()->toArray();
        $data['is_subscribed'] = empty(Subscribers::where('email', $data['email'])->first()) ? 'no' : 'yes';
        //dd($data['is_subscribed']);
        return view('dashboard.account_settings', ['data' => $data]);
    }

    public function submit_account_settings(Request $request)
    {
        $user = User::find(\Auth::user()->id);
        if (!empty($request['password'])) {
            $validator = Validator::make($request->all(), [
                'password' => ['required', 'string', 'min:8', 'confirmed']
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $user->password = Hash::make($request['password']);
            $user->update();
        }

        if (!empty($request['is_subscribed'])) {
            $sbcount = Subscribers::where('email', $user->email)->first();
            if (empty($sbcount)) {
                $obj = new Subscribers;
                $obj->email = $user->email;
                $obj->user_type = 'customer';
                $obj->status = 'a';
                $obj->created_at = date('Y-m-d H:i:s');
                $obj->updated_at = date('Y-m-d H:i:s');
                $obj->save();
            }
        } else {
            Subscribers::where('email', $user->email)->delete();
        }
        return redirect(route('dashboard.account_settings'));
    }

    public function profile(Request $request)
    {
        return view('dashboard.profile');
    }

    public function editprofile(Request $request)
    {

        $data = \Auth::user()->toArray();
        //dd($data);
        return view('dashboard.editprofile', ['data' => $data]);
    }

    public function updateprofile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|alpha|max:255|min:3',
            'lastname' => 'required|alpha|max:255|min:3',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user = User::find(\Auth::user()->id);

        $user->firstname = $request['firstname'];
        $user->lastname = $request['lastname'];
        $file = $request->file('avatar');
        if (!empty($file)) {
            $destinationPath = public_path('images/user');
            $filename =  md5(date('YmdHis')) . '.' . $file->getClientOriginalExtension();
            $user->avatar = 'images/user/' . $filename;
            $file->move($destinationPath, $filename);
        }
        $user->update();

        return redirect(route('dashboard.editprofile'));
    }
}
