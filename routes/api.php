<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;





// register
Route::post('register',[AuthController::class,'index']);




//vendor
use App\Http\Controllers\API\Vendor\CategoryController;
use App\Http\Controllers\API\Vendor\VendorController;
use App\Http\Controllers\API\Vendor\ProfileController;
use App\Http\Controllers\API\Vendor\ProductController;
use App\Http\Controllers\API\Vendor\SubcategoryController;
use App\Http\Controllers\API\Vendor\OrderController;



//shopper

use App\Http\Controllers\API\Shopper\ShopperController;
use App\Http\Controllers\API\Shopper\ProductController as ShopperProduct;
use App\Http\Controllers\API\Shopper\ProfileController as ShopperProfile;
use App\Http\Controllers\API\Shopper\CartController;
use App\Http\Controllers\API\Shopper\OrderController  as ShopperOrder; 


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
Route::get('category-list',[CategoryController::class,'index']);



//login
Route::get('login',[AuthController::class,'index']);


Route::post('verification',[AuthController::class,'create']);

//vendor
Route::group(['prefix'=>'vendor'],function(){

	Route::post('home',[VendorController::class,'index']);

    Route::post('product-details',[ProductController::class,'product_details']);

    Route::post('reviews',[ProductController::class,'get_reviews']);

	Route::post('subcategory-selected-products',[ProductController::class,'index']);


    //PROFILE
	Route::post('get-profile-details',[ProfileController::class,'index']);

	Route::post('update-profile-details',[ProfileController::class,'store']);
   
    
   //ADD PRODUCT
	Route::post('get-vendor-subcategory',[SubcategoryController::class,'create']);
    
	Route::post('get-color-size-for-product',[VendorController::class,'create']);



	Route::post('add-product',[ProductController::class,'store']);
	
    Route::post('get-product',[ProductController::class,'get_product']);
    
    Route::post('update-product',[ProductController::class,'update']);
    
    Route::post('remove-size-color',[ProductController::class,'remove_size_color']);
    
    Route::post('remove-size-color-image',[ProductController::class,'remove_size_color_image']);
    
    Route::post('delete-size',[ProductController::class,'delete_size']);
    
    Route::post('delete-product-image',[ProductController::class,'delete_product_image']);
    
    
    
    

	Route::post('order',[OrderController::class,'index']);

	Route::post('change-order-status',[OrderController::class,'store']);

	Route::post('sale',[OrderController::class,'sale']);
	
	Route::post('add-account',[VendorController::class,'add_account']);
	
    Route::post('my-accounts',[VendorController::class,'my_accounts']);
    
    Route::post('switch-to-account',[VendorController::class,'switch_to_account']);
    
    //not done
    Route::post('logout',[VendorController::class,'logout']);
    
    
    //slider images upload
    Route::post('upload-silder-images',[VendorController::class,'upload_slider_images']);
    
    Route::post('get-silder-images',[VendorController::class,'get_slider_images']);
   
    Route::post('delete-silder-images',[VendorController::class,'delete_slider_images']);
    
    
    Route::post('testproduct',[ProductController::class,'testproduct']);
    
    Route::post('get-notifications',[VendorController::class,'get_notification']);

});


  
Route::group(['prefix'=>'shopper'],function(){
    
    //home
	Route::post('home',[ShopperController::class,'index']);
    
    //store profile
	Route::post('store-details',[ShopperController::class,'store_details']);
    
    //explorer
	Route::post('products-list',[ShopperProduct::class,'index']);

	//store list
	Route::post('show-stores-list',[ShopperController::class,'show_stores_list']);

	//store products
	Route::post('store-products',[ShopperProduct::class,'store_products']);


	//like
	Route::post('like',[ShopperController::class,'like']);

	//liked list
	Route::post('like-list',[ShopperController::class,'like_list']);

    //profile
	Route::post('profile',[ShopperProfile::class,'index']);

	 //profile
	Route::post('profile-update',[ShopperProfile::class,'store']);

	//follow unfollow
	Route::post('follow',[ShopperProfile::class,'follow']);

	//add to cart
	Route::post('add-to-cart',[CartController::class,'store']);
    
    //manage quantity
    Route::post('cart-product-qty',[CartController::class,'manager_qty']);

    //cart
	Route::post('cart',[CartController::class,'index']);


	 //remove from card
	Route::post('remove-cart-product',[CartController::class,'remove_product']);

	//order
	Route::post('order',[ShopperOrder::class,'index']);

	//review
	Route::post('review',[ShopperProduct::class,'review']);
	
	
   //retweet
	Route::post('retweet',[ShopperController::class,'retweet']);
	
	 //not done
    Route::post('logout',[ShopperController::class,'logout']);
    
    
    // notification
    Route::post('notification-status',[ShopperController::class,'notification_status']);
	
	
	Route::post('get-notifications',[ShopperController::class,'get_notification']);

});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
