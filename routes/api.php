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

Route::post('v1/login', 'UserController@login')->name('users.login');
Route::post('v1/register', 'UserController@store')->name('users.store');
Route::post('v1/forgot-password', 'UserController@forgotPassword')->name('users.forgotpassword');
Route::get('v1/check-link-recouver-password/{hash}', 'UserController@likeRecouverExpired')->name('users.linkrecouverexpired');
Route::post('v1/recouver-password', 'UserController@recouverPassword')->name('users.recouverpassword');

Route::group(['prefix' => 'v1', 'middleware' => ['jwt.verify','throttle:5000,1']], function () {

  Route::apiResources([
    'tasklist'  =>  'TaskListController',
    'tasks'  =>  'TasksController',
    'lives' => 'LiveController',
    'live-comment' => 'LiveCommentController',
    'copy' => 'CopyController',
    'partnervideovdi' => 'PartnerVideoVDIController',
    'courses' => 'CourseController',
    'users' => 'UserController',
  ]);

  Route::get('videovdi/getByType/{type}', 'PartnerVideoVDIController@getByType')->name('partnervideovdi.getByType');
  Route::get('videovdi/downloadVideo/{id}', 'PartnerVideoVDIController@downloadVideo')->name('partnervideovdi.downloadVideo');
  Route::put('task/close/{id}', 'TasksController@closeTask')->name('tasks.closeTask');
  Route::get('list/tasks/{id}', 'TasksController@tasksByList')->name('tasks.tasksByList');
  Route::get('live-comment/live/{id}', 'LiveCommentController@getCommentByLive')->name('live-comment.commentByLive');
  Route::post('logout', 'UserController@logout')->name('users.logout');
  
  // Users
  Route::post('users/admininsert', 'UserController@adminInsert')->name('users.admininsert');
  Route::post('users/checkEmailByMaicoList', 'UserController@checkEmailByMaicoList')->name('users.checkEmailByMaicoList');
  Route::put('users/updatePassword/{user}', 'UserController@updatePassword')->name('users.updatePassword');
  Route::put('users/updateUserHasCourses/{user}', 'UserController@updateUserHasCourses')->name('users.updateUserHasCourses');
  
  // Courses
  Route::get('courses/getCoursesByUser/{user_id}', 'CourseController@getCoursesByUser')->name('courses.getCoursesByUser');
  
  //Copies
  Route::get('copy/getCopyByCourseID/{course_id}', 'CopyController@getCopyByCourseID')->name('courses.getcopybycourseid');

});


Route::get('v1/send-mail', function () {

  $details = [
      'name' => 'Gustavo Xavier de Oliveira',
      'link' => 'google.com.br'
  ];
 
  Mail::to('gustasv00@gmail.com')->send(new \App\Mail\ForgotPassword($details));
 
  dd("Email is Sent.");
});