<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Rotas SEM necessidade de verificação de conexão por token
 */
Route::prefix('v1')->group(function () {
  Route::post('login', 'UserController@login')->name('users.login');
  Route::post('register', 'UserController@store')->name('users.store');
  Route::post('forgot-password', 'UserController@forgotPassword')->name('users.forgotpassword');
  Route::get('check-link-recouver-password/{hash}', 'UserController@likeRecouverExpired')->name('users.linkrecouverexpired');
  Route::post('recouver-password', 'UserController@recouverPassword')->name('users.recouverpassword');  
});


/**
 * Rotas com login
 */
Route::group(['prefix' => 'v1', 'middleware' => ['jwt.verify','throttle:5000,1']], function () {
  Route::apiResources([
    'tasklist'  =>  'TaskListController',
    'tasks'  =>  'TasksController',
    'lives' => 'LiveController',
    'live-comment' => 'LiveCommentController',
    'copy' => 'CopyController',
    'partnervideo' => 'PartnerVideoController',
    'courses' => 'CourseController',
    'users' => 'UserController',
  ]);

  Route::put('task/close/{id}', 'TasksController@closeTask')->name('tasks.closeTask');
  Route::get('list/tasks/{id}', 'TasksController@tasksByList')->name('tasks.tasksByList');
  Route::get('live-comment/live/{id}', 'LiveCommentController@getCommentByLive')->name('live-comment.commentByLive');
  Route::post('logout', 'UserController@logout')->name('users.logout');
  
  // Users
  Route::post('users/admininsert', 'UserController@adminInsert')->name('users.admininsert');
  Route::post('users/admininsertimported', 'UserController@adminInsertImported')->name('users.admininsertimported');
  Route::post('users/checkEmailByMaicoList', 'UserController@checkEmailByMaicoList')->name('users.checkEmailByMaicoList');
  Route::put('users/updatePassword/{user}', 'UserController@updatePassword')->name('users.updatePassword');
  Route::put('users/updateUserHasCourses/{user}', 'UserController@updateUserHasCourses')->name('users.updateUserHasCourses');
  
  // Courses
  Route::get('courses/getCoursesByUser/{user_id}', 'CourseController@getCoursesByUser')->name('courses.getCoursesByUser');
  Route::get('courses/getCoursesByLive/{live_id}', 'CourseController@getCoursesByLive')->name('courses.getCoursesByLive');
  
  //Copies
  Route::get('copy/getCopyByCourseID/{course_id}', 'CopyController@getCopyByCourseID')->name('copy.getcopybycourseid');
    
  //Partner Videos
  Route::get('partnervideo/getVideos/{course_id}/{type}', 'PartnerVideoController@getVideos')->name('partnervideo.getvideos');
  Route::get('partnervideo/getByType/{type}', 'PartnerVideoController@getByType')->name('partnervideo.getByType');
  Route::get('partnervideo/downloadVideo/{id}', 'PartnerVideoController@downloadVideo')->name('partnervideo.downloadVideo');

});

// Route::get('send-mail', function () {

//   $details = [
//       'name' => 'Gustavo Xavier de Oliveira',
//       'link' => 'google.com.br'
//   ];
 
//   Mail::to('gustasv00@gmail.com')->send(new \App\Mail\ForgotPassword($details));
 
//   dd("Email is Sent.");
// });