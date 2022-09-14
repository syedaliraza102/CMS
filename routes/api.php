<?php
use Illuminate\Http\Request;
use \App\User;
use App\Models\Category;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
*/

//header('Access-Control-Allow-Origin', "*");

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/iadam/api', 'Api\PagesController@iadam');
Route::post('/auth/login', function (Request $request) {
    $user = User::where('email', $request['email'])->first();
    if (empty($user)) {
        return ['flag' => 2, 'msg' => 'Email is not valid. Please try another email'];
    }
    $validCredentials = Hash::check($request['password'], $user->getAuthPassword());
    if ($validCredentials) {
        return ['flag' => 1, 'msg' => 'Login Successfully', 'data' => $user];
    } else {
        return ['flag' => 2, 'msg' => 'Email or password is not valid'];
    }
    return $request->all();
});
Route::get('/pagedetails/{slug}', 'Api\PagesController@pagedetails')->name('pages');
Route::post('/speaches/useby', 'Api\SpeachesapiController@useby');
Route::post('/speaches/save', 'Api\SpeachesapiController@saveResult');
