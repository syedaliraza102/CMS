<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use \App\Common;


// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/savechapterdata', 'HomeController@savechapterdata')->name('savechapterdata');
// Route::get('/save-student-data', 'HomeController@saveStudentData')->name('saveStudentData');
Route::get('/sync-lesson', 'HomeController@syncLesson')->name('syncLesson');
Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    $exitCode1 = Artisan::call('view:clear');
    $exitCode2 = Artisan::call('route:clear');
    $exitCode3 = Artisan::call('clear-compiled');
    $exitCode4 = Artisan::call('config:cache');
    dd($exitCode, $exitCode1, $exitCode2, $exitCode3, $exitCode4);
    // return what you want
});
Auth::routes();

//Route::get('/', 'PagesController@home')->name('home');
Route::get('/home', 'PagesController@home')->name('home');
Route::post('/savescore', 'ScoreController@savescore')->name('savescore');
Route::post('/getlessondata', 'ScoreController@getlessondata')->name('getlessondata');
Route::get('admin/dashboard/graphdata', 'Admin\DashboardController@dashboard_data')->name('graphdata');


Route::post('admin/getspeeachresult', 'Admin\SpeachesController@getspeeachresult');
Route::get('admin/teacherrecording/delete/{id}', 'Admin\RecordingController@delete');
Route::post('admin/teacherstudiorecaddpoint', 'Admin\RecordingController@teacherstudiorecaddpoint');
Route::get('admin/teacherrecording/get/{id}', 'Admin\RecordingController@getData');
Route::get('admin/studentnotattempt/{id}', 'Admin\RecordingController@studentnotattempt');
Route::get('admin/studentcompleted/{id}', 'Admin\RecordingController@studentcompleted');
Route::get('admin/studentattempt/{id}', 'Admin\RecordingController@studentattempt');
Route::get('admin/studentattempt/{id}', 'Admin\RecordingController@studentattempt');
Route::post('admin/studentrecordingattemptadd/', 'Admin\StudentrecordingController@studentrecordingattemptadd');
Route::get('admin/studentrecording/get/{id}', 'Admin\StudentrecordingController@getData');
Route::get('/admin/classes', 'Admin\ClassController@classList');
Route::get('/admin/students', 'Admin\ClassController@studentList');

Route::get('admin/dashboard/student/notification ', 'Admin\NotificationController@notification');

Route::post('/admin/addteacherrec', 'Admin\RecordingController@addteacherrec');
Route::post('/admin/updateteacherrec', 'Admin\RecordingController@updateteacherrec');

Route::post('/getfragmentdata', 'ScoreController@getfragmentdata')->name('getfragmentdata');
Route::post('/deactiveroom', 'ScoreController@deactiveroom')->name('deactiveroom');
Route::post('/saveroomdata', 'ScoreController@saveroomdata')->name('saveroomdata');
Route::post('/multiroom-points', 'ScoreController@multiroomPoints')->name('multiroomPoints');
Route::post('/get-extra-training-data', 'ScoreController@getExtraTrainingData')->name('getExtraTrainingData');
Route::post('/save-relaxation-data', 'ScoreController@saveRelaxationData')->name('saveRelaxationData');
Route::get('/relaxation-leader-board', 'RelaxationDataController@redirectUser')->name('relaxation.redirectUser');

Route::post('/save-card', 'ScoreController@saveCard')->name('admin.student.saveCard');
Route::post('/getponits', 'ScoreController@getPonits')->name('admin.student.getponits');
Route::post('/unuseditem', 'ScoreController@saveUnusedItem')->name('admin.student.saveUnusedItem');
Route::post('/purchaseitem', 'ScoreController@purchaseItem')->name('admin.student.purchaseItem');
Route::get('/redirect-user', 'ScoreController@redirectUser')->name('redirectUser');

Route::get('/admin/login', 'AdminController@login')->name('admin.login')->middleware('adminaccess');
Route::get('/', 'AdminController@index')->name('admin')->middleware('adminaccess');
Route::get('/admin', 'AdminController@index')->name('admin')->middleware('adminaccess');
Route::get('/admin/data', 'AdminController@data')->name('admin.data')->middleware('adminaccess');
Route::get('/admin/dashboard/data', 'AdminController@dashboardData')->name('admin.dashboardData')->middleware('adminaccess');
Route::post('/admin/buycard/{id}', 'AdminController@buyCard')->name('admin.buycard')->middleware('adminaccess');
Route::post('/admin/printcard/{id}', 'AdminController@printCard')->name('admin.printcard')->middleware('adminaccess');
Route::post('admin/auth/login', 'AdminController@userlogin')->name('admin.userlogin');
Route::post('admin/userprofile', 'AdminController@userprofile')->name('admin.userprofile')->middleware('adminaccess');
Route::post('admin/updateuserprofile', 'AdminController@updateuserprofile')->name('admin.updateuserprofile')->middleware('adminaccess');
Route::post('admin/sitesetting', 'AdminController@sitesetting')->name('admin.sitesetting')->middleware('adminaccess');
Route::post('admin/updatesitesetting', 'AdminController@updatesitesetting')->name('admin.updatesitesetting')->middleware('adminaccess');

Route::get('admin/getalllevels', 'AdminController@getalllevels');

Route::post('admin/schoolsetting', 'AdminController@schoolsetting')->name('admin.schoolsetting')->middleware('adminaccess');
Route::post('admin/updateschoolsetting', 'AdminController@updateschoolsetting')->name('admin.updateschoolsetting')->middleware('adminaccess');

Route::post('admin/subscription', 'AdminController@subscription')->name('admin.subscription')->middleware('adminaccess');
Route::post('admin/updatesubscription', 'AdminController@updatesubscription')->name('admin.updatesubscription')->middleware('adminaccess');
Route::post('admin/lesson/getfromdata', 'Admin\\LessonController@getfromdata')->name('admin.lesson.getfromdata')->middleware('adminaccess');
Route::post('admin/points/update', 'Admin\\PointsController@pointsUpdate')->name('admin.points.pointsUpdate')->middleware('adminaccess');
Route::post('admin/fragment/getcatdata', 'Admin\\FragmentController@GetCatDataRequest')->name('admin.lesson.getcatdata')->middleware('adminaccess');

Route::post('admin/getstudentlessonform/{lesson_id}/{student_id}', 'Admin\\AssigmentLessonController@getStudentLessonForm')->name('admin.AssigmentLesson.getcatdata')->middleware('adminaccess');
Route::post('admin/savestudentlessonform/{lesson_id}/{student_id}', 'Admin\\AssigmentLessonController@saveStudentLessonForm')->name('admin.AssigmentLesson.savecatdata')->middleware('adminaccess');

Route::post('admin/relaxationclass/getstudentlist', 'Admin\\RelaxationClassController@getstudentlist')->name('admin.relaxationclass.getstudentlist')->middleware('adminaccess');

Route::post('admin/relaxationclass/getfragmentlist', 'Admin\\RelaxationClassController@getFragmentList')->name('admin.relaxationclass.getFragmentList')->middleware('adminaccess');

Route::post('admin/relaxationclass/relaclassactdect', 'Admin\\RelaxationClassController@relaclassactdect')->middleware('adminaccess');
Route::post('admin/classdata', 'Admin\\SchoolController@getClassData')->middleware('adminaccess');



Route::post('admin/multiplayerroom/formdata', 'Admin\\MultiplayerRoomController@formdata')->name('admin.multiplayerroom.formdata')->middleware('adminaccess');
Route::post('admin/multiplayerroom/saveroom', 'Admin\\MultiplayerRoomController@saveroom')->name('admin.multiplayerroom.saveroom')->middleware('adminaccess');




$module_arr = [];
foreach (Common::module_list() as $key => $value) {
    $module_arr[$key] = 'Admin\\' . $value['Controller'];
}
foreach ($module_arr as $key => $value) {
    Route::get('/admin/' . $key . '/faker', $value . '@faker')->name('admin.' . $key . '.faker');
    Route::get('/admin/' . $key . '/griddata', $value . '@griddata')->name('admin.' . $key . '.griddata')->middleware('adminaccess');
    Route::post('/admin/' . $key . '/add', $value . '@add')->name('admin.' . $key . '.add')->middleware('adminaccess');
    Route::post('/admin/' . $key . '/save', $value . '@save')->name('admin.' . $key . '.save')->middleware('adminaccess');
    Route::post('/admin/' . $key . '/edit/{id}', $value . '@edit')->name('admin.' . $key . '.edit')->middleware('adminaccess');
    Route::post('/admin/' . $key . '/update/{id}', $value . '@update')->name('admin.' . $key . '.update')->middleware('adminaccess');
    Route::post('/admin/' . $key . '/delete/{id}', $value . '@delete')->name('admin.' . $key . '.delete')->middleware('adminaccess');
    Route::post('/admin/' . $key . '/view/{id}', $value . '@view')->name('admin.' . $key . '.view')->middleware('adminaccess');
    Route::post('/admin/' . $key . '/export/{id}', $value . '@export')->name('admin.' . $key . '.export')->middleware('adminaccess');
    Route::post('/admin/' . $key . '/bulkaction/{slug}', $value . '@bulkaction')->name('admin.' . $key . '.bulkaction')->middleware('adminaccess');
}

Route::post('image_upload', 'AdminController@image_upload')->name('image_upload');

Route::get('admin/multiplayerroom/getData/{id}', 'Admin\MultiplayerRoomController@getData');

Route::post('image_remove', 'AdminController@image_remove')->name('image_remove');
Route::post('facker/{slug}', 'AdminController@faker')->name('faker');

Route::post('/admin/customaction/sendnewsletters/{id}', 'AdminController@customaction')->name('admin.sendnewsletters')->middleware('adminaccess');
Route::post('/admin/submitcustomaction/sendnewsletters/{id}', 'AdminController@submitcustomaction')->name('admin.submitcustomaction')->middleware('adminaccess');

Route::post('/admin/customaction/{actionslug}/{id}', 'AdminController@customaction')->name('admin.customaction')->middleware('adminaccess');
Route::post('/admin/submitcustomaction/{actionslug}/{id}', 'AdminController@submitcustomaction')->name('admin.submitcustomaction')->middleware('adminaccess');

Route::get('/dashboard', 'Dashboard\\DashboardController@index')->name('dashboard.index');
Route::get('/dashboard/account_settings', 'Dashboard\\DashboardController@account_settings')->name('dashboard.account_settings');
Route::post('/dashboard/submit_account_settings', 'Dashboard\\DashboardController@submit_account_settings')->name('dashboard.submit_account_settings');
Route::get('/dashboard/profile', 'Dashboard\\DashboardController@profile')->name('dashboard.profile');
Route::get('/dashboard/editprofile', 'Dashboard\\DashboardController@editprofile')->name('dashboard.editprofile');
Route::post('/dashboard/updateprofile', 'Dashboard\\DashboardController@updateprofile')->name('dashboard.updateprofile');

Route::get('blog/{slug}', 'PagesController@blogdetail')->name('blog.detail');
Route::get('blog/tags/{tag}', 'PagesController@blogtags')->name('blog.tags');
Route::get('blog', 'PagesController@blog')->name('blog');
Route::get('test', 'PagesController@test')->name('test');
Route::get('setpassword', 'PagesController@setpassword')->name('setpassword');
Route::get('{slug}', 'PagesController@page')->name('page');
