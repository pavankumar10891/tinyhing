<?php
use Illuminate\Http\Request;

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
		
Route::group(['namespace'=>'Api\v1','middleware' => 'App\Http\Middleware\GuestApi'], function(){


});

Route::group(['namespace'=>'Api\v1','middleware' => 'App\Http\Middleware\AuthApi'], function(){

  
});