<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
Route::get('/clear-config', function () {
    $exitCode = Artisan::call('config:cache');
    return 'Application cache cleared';
});
Route::group([

    'middleware' => ['api', 'localization'],
    // 'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth'

], function ($router) {
    // Route::post('/login', [AuthController::class, 'login']);
    Route::post('login', 'App\Http\Controllers\Api\AuthController@login');
    Route::post('register', 'App\Http\Controllers\Api\AuthController@register');
    Route::post('send_otp', 'App\Http\Controllers\Api\ForgotPasswordController@send_otp');
    Route::post('forgot_password', 'App\Http\Controllers\Api\ForgotPasswordController@forgot_password');
    Route::post('verify_otp', 'App\Http\Controllers\Api\ForgotPasswordController@verify_otp');
    Route::post('reset_password', 'App\Http\Controllers\Api\ForgotPasswordController@reset_password');

    // /******notification****/
    // Route::get('notification', 'App\Http\Controllers\Api\UserController@notification');

});
Route::group([ 'middleware' => ['jwt.verify', 'localization'] ], function ($router) {
    Route::post('logout', 'App\Http\Controllers\Api\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\Api\AuthController@refresh');
    Route::post('change_password', 'App\Http\Controllers\Api\UserController@change_password');
    Route::get('get_profile', 'App\Http\Controllers\Api\UserController@getProfile');
    Route::post('update_profile', 'App\Http\Controllers\Api\UserController@update_profile');
    Route::post('upload_image', 'App\Http\Controllers\Api\UserController@upload_image');
    Route::post('create_profile', 'App\Http\Controllers\Api\UserController@create_profile');

});
