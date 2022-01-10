<?php

namespace App\Http\Controllers\API\Shopper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Product;
use App\Models\Product_color;
use App\Models\Like;
use App\Models\Review;
use App\Models\Follow;
use App\Models\Order_details;
use App\Models\Retweet;
use App\Models\Notification;
use App\Models\Auth_token;


use Carbon\Carbon;


class ShopperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);
        
         $sk = isset($request->skip) ? $request->skip:0; 

        $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',1)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);  
      
      
      
       // return $vendors =  User::where('role',2)
       //  ->select('id','image','name')
       //  ->where('status',1)
       //  ->where('is_deleted',0)
       //  ->get();

       $productRaw = Product::where('status',1)
                                ->where('is_deleted',0)
                                ->latest()
                                ->limit(15)
                                ->skip($sk)
                                ->get();
                                
                                
      if(count($productRaw) ==0) return response()->json(['status'=>false,'message'=>'Product not found.']);

       $product =[];
       foreach ($productRaw as $key => $value) {
        
          $fav = Like::where('product_id',$value->id)
                              ->where('user_id',$user->id)
                              ->first();

         $rawSize  =[];
            foreach ($value->product_size as $key1 => $value1) {
               
               $cofsize = [];
               $pc = Product_color::where('size_id',$value1->size_id)->where('product_id',$value1->product_id)->where('is_deleted',0)->get();
               foreach($pc as $cr){
                   $cofsize[] = [
                   'id'=>$cr->color->id,
                   'color_name'=>$cr->color->color_name,
                   'colors'=>$cr->color->name,
                   'img1'=>$cr->img1,
                   'img2'=>$cr->img2,
                   'img3'=>$cr->img3,
                   'img4'=>$cr->img4
                   ];
               }
               
               $rawSize[] = [
                'id'=>$value1->size->id,
                'name'=>$value1->size->name,
                'size_color'=>$cofsize
               ];
            }


            $rawColor = [];
            foreach ($value->product_color as $key2 => $value2) {
               $rawColor[] = [
                'id'=>$value2->color->id,
                'name'=>$value2->color->name,
                'img1'=>($value2->img1) ? $value2->img1:$value->img1,
                'img2'=>($value2->img2) ? $value2->img2:'',
                'img3'=>($value2->img3) ? $value2->img3:'',
                'img4'=>($value2->img4) ? $value2->img4:'',
               ]; 
            }

            $rawRewiew  =[];
            foreach ($value->review as $key2 => $value2) {
                
                $reviewImage =[];
                
                if(!empty($value2->img1)){
                      $reviewImage[0] = $value2->img1;
                }
                 if(!empty($value2->img2)){
                      $reviewImage[1] = $value2->img2;
                }
                 if(!empty($value2->img3)){
                      $reviewImage[2] = $value2->img3;
                }
                 if(!empty($value2->img4)){
                      $reviewImage[3] = $value2->img4;
                }
              
                
               $rawRewiew[] = [
                'id'     =>$value2->user->id,
                'image'   =>$value2->user->image,
                'name'   =>$value2->user->name,
                'comment'=>$value2->comment,
                'review_images' =>$reviewImage,
                'date'   =>Carbon::parse($value2->created_at)->format('d F Y ')
               ];
            }
         $checkable = Order_details::where('user_id',$user->id)->where('product_id',$value->id)->first();
         $checkfollow = Follow::where('user_id',$value->user->id)->where('follower_id',$user->id)->first();
         $checkretweet = Retweet::where('user_id',$user->id)->where('product_id',$value->id)->where('is_deleted',0)->first();
       
        // $notifcation =  Follow::where('follower_id',$user->id)->where('user_id',$value->vendor_id)->first();
        $product[] = [
                    'vendor_id'         =>$value->user->id,
                    'vendor_image'      =>$value->user->image,
                    'vendor_name'       =>$value->user->name,
                    'en_category'       =>$value->category->en_category,
                    'ar_category'       =>$value->category->ar_category,
                    'brief'             =>"New Collection from ".$value->user->name,
                    'product_id'        =>$value->id,
                    'image1'            =>$value->img1,
                    'image2'            =>($value->img2) ? $value->img2:'',
                    'image3'            =>($value->img3) ? $value->img3:'',
                    'image4'            =>($value->img4) ? $value->img4:'',
                    'title'             =>$value->title,
                    'description'       =>$value->description,
                    'price'             =>round($value->price,2),
                    'liked'             =>(!empty($fav) ? 1:0),
                    'like_count'        =>$value->like->count(),
                    'comments_count'    =>count($rawRewiew),
                    'product_size'      =>$rawSize,
                    'product_color'     =>$rawColor,
                    'review_status'     =>($checkable) ? 1:0,
                    'follows_status'    =>($checkfollow) ? 1:0,
                    'reviews'           =>$rawRewiew,
                    'retweet_status'    =>($checkretweet) ? 1:0,
                    'notification_status'=>($checkfollow) ? $checkfollow->allow_notification:0,
                    'web_url'=>'http://cuma.co/'
                   
        ];
       }
      

       $data['status']  = true;
       $data['data']    = ['product'=>$product];
       $data['message'] = "Shopper home data.";

       return response()->json($data);






    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function show_stores_list(Request $request)
    // {
    //     //
    //     if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
    //     if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);


    //     $user = User::where('id',$request->user_id)->where('role',1)->where('auth_token',$request->token)->where('status',1)->where('is_deleted',0)->first();
    //     if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);  



    //   $vendor = User::select('id','image','name')->where('role',2)->where('status',1)->where('is_deleted',0)->get();
       
    //   $list = [];
    //   foreach ($vendor as $key => $value) {
    //       # code...
    //     $list[] = [
    //               'id'=>$value->id,
    //               'image'=>$value->image,
    //               'name'=>$value->name,
    //               'followers'=>$value->follower->count(),
    //     ];

    //   }

    //   $data['status']  = true;
    //   $data['data']    = ['vendor_list'=>$list];
    //   $data['message'] = "Stores list data.";

    //   return response()->json($data);


    // }
    
    
    
     public function show_stores_list(Request $request)
    {
        //
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);


        // $user = User::where('id',$request->user_id)->where('role',1)->where('auth_token',$request->token)->where('status',1)->where('is_deleted',0)->first();
        // if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);  
        
        
        
         $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',1)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);  
        
        
        

        $sk = isset($request->skip) ? $request->skip:0;


        $vendor = User::select('id','image','name')->where('role',2)->where('status',1)->where('is_deleted',0)->limit(15)->skip($sk)->get();
        
    //     $arrvendor = [];
    //     foreach($vendor as $row){
            
    //       $rtwt =Retweet::where('vendor_id',$row->id)->count();
    //       $like =Like::where('vendor_id',$row->id)->count();
           
    //       $arrvendor[] = [
               
    //          ];
    //   }
        
     
       
       $list = [];
       foreach ($vendor as $key => $valuee) {
           # code...
           
           $rtwt =Retweet::where('vendor_id',$valuee->id)->count();
           $like =Like::where('vendor_id',$valuee->id)->count();
           
           
        $product =[];
        foreach ($valuee->products as $key => $value) {
         
          $fav = Like::where('product_id',$value->id)
                              ->where('user_id',$user->id)
                              ->first();

         $rawSize  =[];
            foreach ($value->product_size as $key1 => $value1) {
               $rawSize[] = [
                'id'=>$value1->size->id,
                'name'=>$value1->size->name
               ];
            }


            $rawColor = [];
            foreach ($value->product_color as $key2 => $value2) {
               $rawColor[] = [
                'id'=>$value2->color->id,
                'name'=>$value2->color->name,
                'img1'=>($value2->img1) ? $value2->img1:$value->img1,
                'img2'=>($value2->img2) ? $value2->img2:'',
                'img3'=>($value2->img3) ? $value2->img3:'',
                'img4'=>($value2->img4) ? $value2->img4:'',
               ]; 
            }

            $rawRewiew  =[];
            foreach ($value->review as $key2 => $value2) {
                
                $reviewImage =[];
                
                if(!empty($value2->img1)){
                      $reviewImage[0] = $value2->img1;
                }
                 if(!empty($value2->img2)){
                      $reviewImage[1] = $value2->img2;
                }
                 if(!empty($value2->img3)){
                      $reviewImage[2] = $value2->img3;
                }
                 if(!empty($value2->img4)){
                      $reviewImage[3] = $value2->img4;
                }
              
                
               $rawRewiew[] = [
                'id'     =>$value2->user->id,
                'image'   =>$value2->user->image,
                'name'   =>$value2->user->name,
                'comment'=>$value2->comment,
                'review_images' =>$reviewImage,
                'date'   =>Carbon::parse($value2->created_at)->format('d F Y ')
               ];
            }
         $checkable = Order_details::where('user_id',$user->id)->where('product_id',$value->id)->first();
         $checkfollow = Follow::where('user_id',$value->user->id)->where('follower_id',$user->id)->first();

        $product[] = [
                    'vendor_id'         =>$value->user->id,
                    'vendor_image'      =>$value->user->image,
                    'vendor_name'       =>$value->user->name,
                    'en_category'       =>$value->category->en_category,
                    'ar_category'       =>$value->category->ar_category,
                    'brief'             =>"New Collection from ".$value->user->name,
                    'product_id'        =>$value->id,
                    'image1'            =>$value->img1,
                    'image2'            =>($value->img2) ? $value->img2:'',
                    'image3'            =>($value->img3) ? $value->img3:'',
                    'image4'            =>($value->img4) ? $value->img4:'',
                    'title'             =>$value->title,
                    'description'       =>$value->description,
                    'price'             =>round($value->price,2),
                    'liked'             =>(!empty($fav) ? 1:0),
                    'like_count'        =>$value->like->count(),
                    'comments_count'    =>count($rawRewiew),
                    'product_size'      =>$rawSize,
                    'product_color'     =>$rawColor,
                    'review_status'     =>($checkable) ? 1:0,
                    'follows_status'    =>($checkfollow) ? 1:0,
                    'reviews'           =>$rawRewiew,
                     'web_url'=>'http://cuma.co/'
                   
        ];
       }
           
           
           
        $list[] = [
                   'id'=>$valuee->id,
                   'image'=>$valuee->image,
                   'name'=>$valuee->name,
                   'followers'=>$valuee->follower->count(),
                   'top'=>$rtwt+$like,
                   'product'=>$product
        ];

       }
      
       $price = array_column($list, 'top');
       array_multisort($price, SORT_DESC, $list);
      
      
       $data['status']  = true;
       $data['data']    = ['vendor_list'=>$list];
       $data['message'] = "Stores list data.";

       return response()->json($data);


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function store_details(Request $request)
    {
        //
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);


        // $user = User::where('id',$request->user_id)
        // ->where('role',1)
        // ->where('status',1)
        // ->where('is_deleted',0)
        // ->where('auth_token',$request->token)
        // ->first();
        
        // if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
        
        
         $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',1)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);  
        
        
        
        

        if(empty($request->vendor_id)) return response()->json(['status'=>false,'message'=>'Vendor id is required.']);


       $vendor = User::where('role',2)
                                        ->where('id',$request->vendor_id)
                                        ->where('status',1)
                                        ->where('is_deleted',0)
                                        ->first();
                                        
      
       $basicDetails = [
                         'id'   =>$vendor->id,
                         'image'=>$vendor->image,
                         'name'=>$vendor->name,
                         'en_category'=>$vendor->category->en_category,
                         'ar_category'=>$vendor->category->ar_category,
                         'bio'=>($vendor->bio) ? $vendor->bio:'',
                         'rating'=>$vendor->reviews->avg('rating'),
                         'followers'=>$vendor->follower->count(),
                         'following'=>$vendor->following->count(),
        ];


       
        $subcategory =[];
        foreach ($vendor->vendor_subcategory as $key => $value) {
            # code...
            $subcategory[] = [
                              'id'=>$value->subcategory->id,
                              'image'=>$value->subcategory->image,
                              'en_subcategory'=>$value->subcategory->en_subcategory,
                              'ar_subcategory'=>$value->subcategory->ar_subcategory,
                              'status'=> ($value->subcategory->id==$request->subcategory_id) ? 1:0,           
            ];
        }
        

        $slider = [];
        foreach($vendor->slider_images as $rows){
           $slider[] = ['id'=>$rows->id,'image'=>$rows->image]; 
        }

        //  $summerCollection = [
        //  ['id'=>1, 'image'=>$vendor->image,'title'=>'static','price'=>10, 'liked'=>0],
        //  ['id'=>2, 'image'=>$vendor->image,'title'=>"static",'price'=>10, 'liked'=>0],
        //  ['id'=>3, 'image'=>$vendor->image,'title'=>'static','price'=>10, 'liked'=>0],
        // ];
        
        
       $summerCollection=[];
       $summer = Product::where('vendor_id',$request->vendor_id)->where('is_summer_collection',1)->where('status',1)->where('is_deleted',0)->get();
       foreach($summer as $value){
          
          
            $rawSize1  =[];
            foreach ($value->product_size as $key1 => $value1) {
                
            $cofsize1 = [];
               
               foreach($value1->size_color as $cr){
                   $cofsize1[] = [
                   'id'=>$cr->color->id,
                   'color_name'=>$cr->color->color_name,
                   'colors'=>$cr->color->name,
                   'img1'=>$cr->img1,
                   'img2'=>$cr->img2,
                   'img3'=>$cr->img3,
                   'img4'=>$cr->img4
                   ];
               }
               
               $rawSize1[] = [
                'id'=>$value1->size->id,
                'name'=>$value1->size->name,
                'size_color'=>$cofsize1
               ];
            }


            $rawColor1 = [];
            foreach ($value->product_color as $key2 => $value2) {
               $rawColor1[] = [
                'id'  =>$value2->color->id,
                'name'=>$value2->color->name,
                'img1'=>($value2->img1) ? $value2->img1:$value->img1,
                'img2'=>($value2->img2) ? $value2->img2:'',
                'img3'=>($value2->img3) ? $value2->img3:'',
                'img4'=>($value2->img4) ? $value2->img4:'',
               ]; 
            }
        
        
          $fav1 = Like::where('product_id',$value->id)
                              ->where('user_id',$user->id)
                              ->first();
                              
                              
                              
            $reviewImage1 =[];
                
                if(!empty($value2->img1)){
                      $reviewImage1[0] = $value2->img1;
                }
                 if(!empty($value2->img2)){
                      $reviewImage1[1] = $value2->img2;
                }
                 if(!empty($value2->img3)){
                      $reviewImage1[2] = $value2->img3;
                }
                 if(!empty($value2->img4)){
                      $reviewImage1[3] = $value2->img4;
                }
                              
                              
                              
         $rawRewiew1  =[];
            foreach ($value->review as $key2 => $value2) {
               $rawRewiew1[] = [
                'id'     =>$value2->user->id,
                'image'   =>$value2->user->image,
                'name'   =>$value2->user->name,
                'comment'=>$value2->comment,
                'review_images' =>$reviewImage1,
                'date'   =>Carbon::parse($value2->created_at)->format('d F Y ')
               ];
            }
         
         
         $checkable1 = Order_details::where('user_id',$user->id)->where('product_id',$value->id)->first();
         $checkfollow1 = Follow::where('user_id',$value->user->id)->where('follower_id',$user->id)->first();
         
        $summerCollection[] = [
                    'id'=>$value->user->id,
                    'vendor_image'=>$value->user->image,
                    'vendor_name'=>$value->user->name,
                    'en_category'=>$value->category->en_category,
                    'ar_category'=>$value->category->ar_category,
                    'subcategory_id'=>$value->subcategory_id,
                    'brief'=>"New Collection from ".$value->user->name,
                    'product_id'=>$value->id,
                    'image1'=>$value->img1,
                    'image2'=>($value->img2) ? $value->img2:'',
                    'image3'=>($value->img3) ? $value->img3:'',
                    'image4'=>($value->img4) ? $value->img4:'',
                    'title'=>$value->title,
                    'description'=>$value->description,
                    'price'=>round($value->price,2),
                    'liked'=>(!empty($fav1) ? 1:0),
                    'like_count'=>$value->like->count(),
                    'review_status'     =>($checkable1) ? 1:0,
                    'follows_status'    =>($checkfollow1) ? 1:0,
                    'comments_count'=>count($rawRewiew1),
                    'product_size'      =>$rawSize1,
                    'product_color'     =>$rawColor1,
                    'reviews'           =>$rawRewiew1,
                    'web_url'=>'http://cuma.co/'
                   
        ];
        
       }
        
       


       $product =[];
       
       $sk = isset($request->skip) ? $request->skip:0;
       
       $prod = Product::where('vendor_id',$request->vendor_id)->where('status',1)->where('is_deleted',0);
       
       if($request->subcategory_id){
            $prod = $prod->where('subcategory_id',$request->subcategory_id);
       }
       $prod = $prod->limit(15)->skip($sk)->get();
       foreach ($prod as $key => $value) {
           

         $rawSize  =[];
         
         
         
         
            foreach ($value->product_size as $key1 => $value1) {
            //   $rawSize[] = [
            //     'id'=>$value1->size->id,
            //     'name'=>$value1->size->name
            //   ];
            
            
              $cofsize = [];
               
               foreach($value1->size_color as $cr){
                   $cofsize[] = [
                   'id'=>$cr->color->id,
                   'color_name'=>$cr->color->color_name,
                   'colors'=>$cr->color->name,
                   'img1'=>$cr->img1,
                   'img2'=>$cr->img2,
                   'img3'=>$cr->img3,
                   'img4'=>$cr->img4
                   ];
               }
               
               $rawSize[] = [
                'id'=>$value1->size->id,
                'name'=>$value1->size->name,
                'size_color'=>$cofsize
               ];
            }
            
            
        


            $rawColor = [];
            
            
            foreach ($value->product_color as $key2 => $value2) {
               $rawColor[] = [
                'id'  =>$value2->color->id,
                'name'=>$value2->color->name,
                'img1'=>($value2->img1) ? $value2->img1:$value->img1,
                'img2'=>($value2->img2) ? $value2->img2:'',
                'img3'=>($value2->img3) ? $value2->img3:'',
                'img4'=>($value2->img4) ? $value2->img4:'',
               ]; 
            }
        
        
          $fav = Like::where('product_id',$value->id)
                              ->where('user_id',$user->id)
                              ->first();
                              
                              
                              
            $reviewImage =[];
                
                if(!empty($value2->img1)){
                      $reviewImage[0] = $value2->img1;
                }
                 if(!empty($value2->img2)){
                      $reviewImage[1] = $value2->img2;
                }
                 if(!empty($value2->img3)){
                      $reviewImage[2] = $value2->img3;
                }
                 if(!empty($value2->img4)){
                      $reviewImage[3] = $value2->img4;
                }
                              
                              
                              
         $rawRewiew  =[];
            foreach ($value->review as $key2 => $value2) {
               $rawRewiew[] = [
                'id'     =>$value2->user->id,
                'image'   =>$value2->user->image,
                'name'   =>$value2->user->name,
                'comment'=>$value2->comment,
                'review_images' =>$reviewImage,
                'date'   =>Carbon::parse($value2->created_at)->format('d F Y ')
               ];
            }
         
         
         $checkable = Order_details::where('user_id',$user->id)->where('product_id',$value->id)->first();
         $checkfollow = Follow::where('user_id',$value->user->id)->where('follower_id',$user->id)->first();
         
        $product[] = [
                    'id'=>$value->user->id,
                    'vendor_image'=>$value->user->image,
                    'vendor_name'=>$value->user->name,
                    'en_category'=>$value->category->en_category,
                    'ar_category'=>$value->category->ar_category,
                    'subcategory_id'=>$value->subcategory_id,
                    'brief'=>"New Collection from ".$value->user->name,
                    'product_id'=>$value->id,
                    'image1'=>$value->img1,
                    'image2'=>($value->img2) ? $value->img2:'',
                    'image3'=>($value->img3) ? $value->img3:'',
                    'image4'=>($value->img4) ? $value->img4:'',
                    'title'=>$value->title,
                    'description'=>$value->description,
                    'price'=>round($value->price,2),
                    'liked'=>(!empty($fav) ? 1:0),
                    'like_count'=>$value->like->count(),
                    'review_status'     =>($checkable) ? 1:0,
                    'follows_status'    =>($checkfollow) ? 1:0,
                    'comments_count'=>count($rawRewiew),
                    'product_size'      =>$rawSize,
                    'product_color'     =>$rawColor,
                    'reviews'           =>$rawRewiew,
                     'web_url'=>'http://cuma.co/'
                   
        ];
       }
      


   
       $data['status']  = true;
       $data['data']    = ['basic_details'=>$basicDetails,'subcategory'=>$subcategory,'banner'=>$slider,'summer_collection'=>$summerCollection,'product'=>$product];
       $data['message'] = "Stores details.";

       return response()->json($data);



        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function like(Request $request)
    {
        //
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);


        // $user = User::where('id',$request->user_id)
        // ->where('role',1)
        // ->where('status',1)
        // ->where('is_deleted',0)
        // ->where('auth_token',$request->token)
        // ->first();
        
        // if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
        
        
        
        
         $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',1)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
        

        $product = Product::find($request->product_id);

        $checkLike = Like::where('product_id',$product->id)
         ->where('vendor_id',$product->vendor_id)
         ->first();

         if(!empty($checkLike)){
            $checkLike->delete();
            $data['status'] = true;
            $data['message'] = "Removed from like list.";
         }else{

            $like = new Like;
            $like->product_id = $product->id;
            $like->vendor_id  = $product->vendor_id;
            $like->user_id    = $request->user_id;
            $like->save();
            $data['status'] = true;
            $data['message'] = "Added in like list.";
         }

        return response()->json($data);

       







    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function like_list(Request $request)
    {
        //
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);


        // $user = User::where('id',$request->user_id)
        // ->where('role',1)
        // ->where('status',1)
        // ->where('is_deleted',0)
        // ->where('auth_token',$request->token)
        // ->first();
        
        // if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
        
        
        
         $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',1)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        


        $productRaw = Like::where('user_id',$user->id)->get();
        $product = [];
        foreach ($productRaw as $key => $value) {
            # code...
            $rawSize  =[];
            foreach ($value->product->product_size as $key1 => $value1) {
                
                
               $cofsize = [];
               
               foreach($value1->size_color as $cr){
                   $cofsize[] = [
                   'id'=>$cr->color->id,
                   'color_name'=>$cr->color->color_name,
                   'colors'=>$cr->color->name,
                   'img1'=>($cr->img1) ? $cr->img1:"",
                   'img2'=>($cr->img2) ? $cr->img2:"",
                   'img3'=>($cr->img3) ? $cr->img3:"",
                   'img4'=>($cr->img4) ? $cr->img4:""
                   ];
               }
               
               $rawSize[] = [
                'id'=>$value1->size->id,
                'name'=>$value1->size->name,
                'size_color'=>$cofsize
               ];
            }
            
            
            //   $rawSize[] = [
            //     'id'=>$value1->size->id,
            //     'name'=>$value1->size->name
            //   ];
            // }

             
             $rawRewiew  =[];
            foreach ($value->product->review as $key2 => $value2) {
                
                $reviewImage =[];
                
                if(!empty($value2->img1)){
                      $reviewImage[0] = $value2->img1;
                }
                 if(!empty($value2->img2)){
                      $reviewImage[1] = $value2->img2;
                }
                 if(!empty($value2->img3)){
                      $reviewImage[2] = $value2->img3;
                }
                 if(!empty($value2->img4)){
                      $reviewImage[3] = $value2->img4;
                }
              
                
               $rawRewiew[] = [
                'id'     =>$value2->user->id,
                'image'   =>$value2->user->image,
                'name'   =>$value2->user->name,
                'comment'=>$value2->comment,
                'review_images' =>$reviewImage,
                'date'   =>Carbon::parse($value2->created_at)->format('d F Y ')
               ];
            }
            
           
            
            $rawColor = [];
            foreach ($value->product->product_color as $key2 => $value5) {

              $fav = Like::where('product_id',$value->product->id)
                              ->where('user_id',$user->id)
                              ->first();


               $rawColor[] = [
                'id'=>$value5->color->id,
                'name'=>$value5->color->name,
                'img1'=>($value5->img1)? $value5->img1 :$value->product->img1,
                'img2'=>($value5->img2) ? $value5->img2:$value->product->img1,
                'img3'=>($value5->img3) ? $value5->img3:'',
                'img4'=>($value5->img4) ? $value5->img4:'',
               ]; 
            }
            
            $checkable = Order_details::where('user_id',$user->id)->where('product_id',$value->product->id)->first();
            $checkfollow = Follow::where('user_id',$value->product->user->id)->where('follower_id',$user->id)->first();
           

            $product[] = [
                'vendor_id'         =>$value->product->user->id,
                'vendor_image'      =>$value->product->user->image,
                'vendor_name'       =>$value->product->user->name,
                'en_category'       =>$value->product->category->en_category,
                'ar_category'       =>$value->product->category->ar_category,
                'id'                =>$value->product->id, 
                'title'             =>$value->product->title, 
                'description'       =>$value->product->description, 
                'img1'              =>$value->product->img1,
                'img2'              =>($value->product->img2) ? $value->product->img2:'',
                'img3'              =>($value->product->img3) ? $value->product->img3:'',
                'img4'              =>($value->product->img4) ? $value->product->img4:'', 
                'sub_subcategory_id'=>$value->product->sub_subcategory_id, 
                'subcategory_id'    =>$value->product->subcategory_id, 
                'category_id'       =>$value->product->category_id,
                'price'             =>round($value->product->price,2),
                'product_size'      =>$rawSize,
                'product_color'     =>$rawColor,
                'liked'             =>(!empty($fav) ? 1:0),
                'reviews'           =>$rawRewiew,
                'review_status'     =>($checkable) ? 1:0,
                'follows_status'    =>($checkfollow) ? 1:0,
                'reviews'           =>$rawRewiew,
                'notification_status'=>($checkfollow) ? $checkfollow->allow_notification:0,
                'web_url'=>'http://cuma.co/'

            ];
        }


          $data['status'] = true;
          $data['data'] = ['products'=>$product];
          $data['message'] = "Added in liked list.";
         

        return response()->json($data);



    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function retweet(Request $request)
    {
        
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);
        if(empty($request->product_id)) return response()->json(['status'=>false,'message'=>'Product id is required.']);


        // $user = User::where('id',$request->user_id)
        // ->where('role',1)
        // ->where('status',1)
        // ->where('is_deleted',0)
        // ->where('auth_token',$request->token)
        // ->first();
        
        // if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
        
        
         $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',1)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
        
            
           $prodExist = Retweet::where('product_id',$request->product_id)->where('user_id',$user->id)->where('is_deleted',0)->first();
           if(!empty($prodExist)){
             $prodExist->is_deleted =1;
             $prodExist->save();
             
             
             $data12['status'] = true;
             $data12['message'] = "Retweet undo successfully.";
             
             return response()->json($data12);
             
           }
            
            $prod = Product::find($request->product_id);
            $retweet = new Retweet;
            $retweet->product_id = $request->product_id;
            $retweet->user_id = $user->id;
            $retweet->vendor_id = $prod->vendor_id;
            $retweet->save();
          
          
          
          
            
        //  $userd = User::find($prod->vendor_id);
         
         $venderAuht = Auth_token::where('fcm_token','!=','')->where('user_id',$prod->vendor_id)->get();
          
          $notification = new Notification();   
          $notification->user_id   = $user->id;
          $notification->vendor_id = $prod->vendor_id;
          $notification->title     = "Product retweeted by a user.";
          $notification->body      = "test test";
          $notification->type      = "Retweeted";
          $notification->n_type    = 1;
          $notification->save();
                    
          
         foreach($venderAuht as $row1){
             
         $json_data =[
                    "to" => $row1->fcm_token,
                    "notification" => [
                        "body" =>"test test",
                        "title" =>"Product retweeted by a user.",
                        "type"=>"Retweeted"
                        // "icon" => "ic_launcher"
                    ]
                ];
                
                
          

          $data = json_encode($json_data);
          $url = 'https://fcm.googleapis.com/fcm/send';
          //header with content_type api key
          $headers = array(
                'Content-Type:application/json',
                'Authorization:key=AAAARvckVyM:APA91bH0MG3_sAHNxiGXN5VYRm3prNjnm4fvxzSI5WnCh2E8aD9XsUn8IptEpmtgeJCUywIzsu-Ww1Xa-nYMhtga60kh59Qebk4-7JLPvYig0x9JgRtKk-SCo42ZvnqTWoXUCgk-XdF2'
            );
            //CURL request to route notification to FCM connection server (provided by Google)
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);
            // if ($result === FALSE) {
            //     die('Oops! FCM Send Error: ' . curl_error($ch));
            // }
            // print_r($result);
            curl_close($ch);
            
         }
            
        
        
        
        
         $data1['status'] = true;
         $data1['message'] = "Retweet successfully.";
         
        
        return response()->json($data1);
        
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function notification_status(Request $request)
    {
        //
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);
        if(empty($request->vendor_id)) return response()->json(['status'=>false,'message'=>'vendor id is required.']);
        // if(empty($request->notification_status)) return response()->json(['status'=>false,'message'=>'notification status is required.']);


        // $user = User::where('id',$request->user_id)
        // ->where('role',1)
        // ->where('status',1)
        // ->where('is_deleted',0)
        // ->where('auth_token',$request->token)
        // ->first();
        
        // if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
        
        
         $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',1)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
        
        
        $follow_check = Follow::where('follower_id',$user->id)->where('user_id',$request->vendor_id)->first();
        
        if(empty($follow_check)) return response()->json(['status'=>false,'message'=>'You havent follow this user.']);
       
        $follow_check->allow_notification = ($follow_check->allow_notification==1)? 0:1;
        if($follow_check->save()){
              return response()->json(['status'=>true,'message'=>'Notification status has been changed.','allow_notification'=>$follow_check->allow_notification]);
        }else{
              return response()->json(['status'=>false,'message'=>'Try again.']);
        }
        
      
        
    }
    
    
    
    
    public function logout(Request $request)
    {
        
     if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
     if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);

    //  $user = User::where('id',$request->user_id)->where('auth_token',$request->token)->first();
    //  if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
    
    
        $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',1)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
     
     
         $ckdevice->auth_token = "";
         $ckdevice->fcm_token="";
        //  $user->device_token="";
         $ckdevice->save();
     
     return response()->json(['status'=>true,'message'=>'logout successfully.']);
     
     
     
    }
    
    
    
     public function get_notification(Request $request){
        
     if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
     if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);

    //  $user = User::where('id',$request->user_id)->where('auth_token',$request->token)->where('is_deleted',0)->first();
    //  if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
    
    
    
     $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',1)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
        
        
        
     
     $notificationRaw = Notification::where('n_type',1)->where('user_id',$user->id)->get();
     $capsule = [];
     
     foreach($notificationRaw as $key => $row){
        $capsule[] = [
                      'title'=>$row->title,
                      'body'=>$row->body,
                      'type'=>$row->type,
                      'datatime'=> Carbon::parse($row->created_At)->format('d F Y')
            ]; 
     }
     
     
     
     return response()->json(['status'=>true,'data'=>["notification"=>$capsule],'message'=>'Notification list.']);
     
     
     
    }
    
}
