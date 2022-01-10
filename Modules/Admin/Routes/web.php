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

Route::get('admin', 'AuthController@index');
Route::post('admin/login','AuthController@store');


Route::group(['prefix'=>'admin','middleware'=>['admin']],function() {

     Route::get('dashboard', 'AdminController@index');
     
     //category
     Route::get('category','CategoryController@index');
     
     Route::get('category-create','CategoryController@create');
     Route::post('category-create','CategoryController@create');
     
     Route::get('category-edit/{id}','CategoryController@edit');
     Route::post('category-edit/{id}','CategoryController@edit');
     Route::post('category-update','CategoryController@update');
     Route::get('category-delete/{id}','CategoryController@destroy');



     //subcategory
     Route::get('subcategory','SubcategoryController@index');
     
     Route::get('subcategory-create','SubcategoryController@create');
     Route::post('subcategory-create','SubcategoryController@create');
     
     Route::get('subcategory-edit/{id}','SubcategoryController@edit');
     Route::post('subcategory-edit/{id}','SubcategoryController@edit');
     Route::post('subcategory-update','SubcategoryController@update');
     Route::get('subcategory-delete/{id}','SubcategoryController@destroy');

     
     
     Route::get('document-setting','AdminController@document_setting');
     Route::post('document-setting','AdminController@document_setting');
     
     Route::post('reject-document','AdminController@reject_document');
     Route::post('approved-document','AdminController@approved_document');
     Route::post('action-on-driver','AdminController@action_on_driver');




       //subsubcategory
    //  Route::get('sub-subcategory','SubsubcategoryController@index');
    //  Route::get('sub-subcategory-create','SubsubcategoryController@create');
    //  Route::post('sub-subcategory-store','SubsubcategoryController@store');
    //  Route::get('sub-subcategory-edit/{id}','SubsubcategoryController@edit');
    //  Route::post('sub-subcategory-update','SubsubcategoryController@update');
    //  Route::get('sub-subcategory-delete/{id}','SubsubcategoryController@destroy');

    //  Route::post('get-subcategory','SubsubcategoryController@get_subcategory');





    //  Route::get('type','TypeController@index');
    //  Route::get('type-create','TypeController@create');
    //  Route::post('type-store','TypeController@store');
    //  Route::get('type-edit/{id}','TypeController@edit');
    //  Route::post('type-update','TypeController@update');
    //  Route::get('type-delete/{id}','TypeController@destroy');

    //  Route::post('get-subsubcategory','TypeController@get_subsubcategory');
 



    //  //size
    //  Route::get('size','SizeController@index');
    //  Route::get('size-create','SizeController@create');
    //  Route::post('size-store','SizeController@store');
    //  Route::get('size-edit/{id}','SizeController@edit');
    //  Route::post('size-update','SizeController@update');
    //  Route::get('size-delete/{id}','SizeController@destroy');

    //  Route::post('get-type','TypeController@get_type');



    //   //color
    //  Route::get('color','ColorController@index');
    //  Route::get('color-create','ColorController@create');
    //  Route::post('color-store','ColorController@store');
    //  Route::get('color-edit/{id}','ColorController@edit');
    //  Route::post('color-update','ColorController@update');
    //  Route::get('color-delete/{id}','ColorController@destroy');


     Route::get('logout','AdminController@destroy');
      
     Route::get('profile','AdminController@profile');
     Route::post('profile','AdminController@profile_post');




});
