<?php

use Illuminate\Http\Request;
use App\EmailActivity;

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

Route::resource('email-activities', 'EmailActivityController');
Route::get('/email-activities/{id}/send', 'EmailActivityController@sendEmail');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
