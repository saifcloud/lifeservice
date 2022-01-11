<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

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

Route::post('register',[AuthController::class,'index']);

Route::get('category-list',[CategoryController::class,'index']);



//login
Route::get('login',[AuthController::class,'index']);


Route::post('verification',[AuthController::class,'create']);

//vendor
Route::group(['prefix'=>'customer'],function(){

});


  
Route::group(['prefix'=>'provider'],function(){
    
});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
