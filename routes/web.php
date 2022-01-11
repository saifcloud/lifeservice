<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Models\Apply;
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



// Route::get('/database-clear', 'AuthController@databaseclear');
Route::get('/database-clear',function(){
    Subcategory::truncate();
});

Route::get('/', function () {
    return view('welcome');
});


Route::get('/clear', function() {
    
    
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
 
 });

Route::get("webview/upload-document",[AuthController::class,'upload_document']);
Route::post("webview/upload-document",[AuthController::class,'upload_document']);

Route::get("webview/document-pending",[AuthController::class,'document_pending']);

Route::get("webview/rejected-document",[AuthController::class,'rejected_document']);
Route::post("webview/rejected-document",[AuthController::class,'rejected_document']);