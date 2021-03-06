<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/campanha/{slug}', 'CampaignController@redirect')->name('campanha');
Route::get('/parceria/{slug}', 'CampaignController@redirectParceria')->name('parceria');

Route::get('/message', function () {
    $message['id'] = "2";
    $message['user'] = "Juan Perez";
    $message['message'] =  "Prueba mensaje desde Pusher";
    event(new App\Events\ChatMessage($message));
    echo "Success send";
});

Route::get('/generate-password/{pass}', 'UserController@generatePassword')->name('generate_pass');