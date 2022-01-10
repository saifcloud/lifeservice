<?php

namespace App\Http\Controllers\API\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Product_size;
use App\Models\Product_color;
use App\Models\Review;
use App\Models\Follow;
use App\Models\Notification;
use App\Models\Auth_token;

use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // subcategory-selected-products
    public function index(Request $request)
    {
        //
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);

        // $user = User::where('id',$request->user_id)->where('auth_token',$request->token)->first();
        // if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
        
        
          $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',2)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
        


        $product = Product::where('vendor_id',$user->id)
                                                        ->where('status',1)
                                                        ->where('is_deleted',0);
                                                        // ->limit(18)
                                                      
                                                        
        if(!empty($request->subcategory_id)){
            $product = $product->where('subcategory_id',$request->subcategory_id);
        }
        $product =  $product->get(); 
        
        $productRaw = [];    
        foreach ($product as $key => $value) {

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

            $productRaw[] = [
                'id'                =>$value->id, 
                'title'             =>$value->title, 
                'description'       =>$value->description, 
                'img1'              =>$value->img1,
                'img2'              =>($value->img2) ? $value->img2:'',
                'img3'              =>($value->img3) ? $value->img3:'',
                'img4'              =>($value->img4) ? $value->img4:'', 
                'sub_subcategory_id'=>($value->sub_subcategory_id) ? $value->sub_subcategory_id:'', 
                'subcategory_id'    =>$value->subcategory_id, 
                'category_id'       =>$value->category_id,
                'price'             =>round($value->price,2),
                'product_size'      =>$rawSize,
                'product_color'     =>$rawColor,
                'reviews'           =>'',
                'web_url'           =>'http://cuma.co/'
            ];
        }
     
       $sct = isset($request->subcategory_id) ? $request->subcategory_id:'';
       $data['status']  = true;
       $data['data']    = ['product'=>$productRaw,'selected_subcategory'=>$sct];
       $data['message'] = 'Home page data.';
       return response()->json($data);


        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         
        //  print_r($request->all()); die;
        
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);

        // $user = User::where('id',$request->user_id)->where('auth_token',$request->token)->where('role',2)->first();
        // if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
        
        
         
          $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',2)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        

          

        $product                       = new Product;
        $product->title                = $request->title;
        $product->description          = $request->description;
        $product->price                = $request->price;
        
        $file1 = $request->image[0];
        $filename1 = time().'1.'.$file1->getClientOriginalExtension();
        $file1->move('public/images/product', $filename1);
        $product->img1                 = '/public/images/product/'.$filename1;
        
        if(isset($request->image[1])){
        $file2 = $request->image[1];
        $filename2 = time().'2.'.$file2->getClientOriginalExtension();
        $file2->move('public/images/product', $filename2);  
        $product->img2                 = '/public/images/product/'.$filename2;
        }
        
        
        if(isset($request->image[2])){
        $file3 = $request->image[2];
        $filename3 = time().'3.'.$file3->getClientOriginalExtension();
        $file3->move('public/images/product', $filename3);
        $product->img3                 = '/public/images/product/'.$filename3;
        }
       
        
        if(isset($request->image[3])){
        $file4 = $request->image[3];
        $filename4 = time().'4.'.$file4->getClientOriginalExtension();
        $file4->move('public/images/product', $filename4);
        $product->img4                 = '/public/images/product/'.$filename4;
        }
        
 
        $product->vendor_id            = $user->id;
        $product->type_id              = $request->type_id;
        $product->sub_subcategory_id   = $request->sub_subcategory_id;
        $product->subcategory_id       = $request->subcategory_id;
        $product->category_id          = $user->category_id;
        $product->is_summer_collection = $request->is_summer_collection;
        $product->save();

        if(!empty($request->size)){
            foreach (explode(",", $request->size) as $key => $value) {
                # code...
                $p_size = new Product_size;
                $p_size->size_id    = $value;
                $p_size->product_id = $product->id;
                $p_size->save();
            }
        }
         
        //  echo "<pre>";
        //  print_r($request->color); die; 
         
         
         if(isset($request->color) &&  count($request->color) > 0 ){
             
         foreach ($request->color as $key => $value) {
           
           foreach($value as $key1 => $rows){
               
               
                $p_size1 = new Product_color;
                $p_size1->color_id    = $key1;
                $p_size1->size_id     = $key;
                $p_size1->product_id  = $product->id;
                
                if(isset($rows[0])){
                    $file5 = $rows[0];
                    $filename5 = time().$key.'1'.$file5->getClientOriginalName();
                    $file5->move('public/images/product', $filename5);
                    $p_size1->img1        = '/public/images/product/'.$filename5;
                }

                if(isset($rows[1])){
                $file6 = $rows[1];
                $filename6 = time().$key.'2'.$file6->getClientOriginalName();
                $file6->move('public/images/product', $filename6);
                $p_size1->img2        = '/public/images/product/'.$filename6;
                }

                if(isset($rows[2])){
                $file7 = $rows[2];
                $filename7 = time().$key.'3'.$file7->getClientOriginalName();
                $file7->move('public/images/product', $filename7);
                 $p_size1->img3        = '/public/images/product/'.$filename7;
                }


                if(isset($rows[3])){
                $file8 = $rows[3];
                $filename8 = time().$key.'4'.$file8->getClientOriginalName();
                $file8->move('public/images/product', $filename8);
                $p_size1->img4        = '/public/images/product/'.$filename8;
                }
                
                $p_size1->save(); 
               
           }
              
           
          
          
        
            }

        }
        
        
       $myfollowers =  Follow::where('user_id',$user->id)->where('allow_notification',1)->get();
        
        foreach($myfollowers as $row){
            
         $userd = Auth_token::where('user_id',$row->follower_id)->where('fcm_token','!=','')->get();
         
         
          $notification = new Notification();   
          $notification->user_id   = $row->follower_id;
          $notification->vendor_id = $user->id;
          $notification->title     = $user->name." added new product";
          $notification->body      = "test test";
          $notification->type      = "ORDER";
          $notification->n_type    = 1;
          $notification->save();
          
          
         foreach($userd as $value){
             
             
         
         $json_data =[
                    "to" => $value->fcm_token,
                    "notification" => [
                        "body" => "test test",
                        "title" =>$user->name." added new product",
                        "type"=>"ORDER"
                        // "icon" => "ic_launcher"
                    ]
                ];
         
        //   $notification = new Notification();   
        //   $notification->user_id   = $userd->id;
        //   $notification->vendor_id = $user->id;
        //   $notification->title     = $user->name." added new product";
        //   $notification->body      = "test test";
        //   $notification->type      = "ORDER";
        //   $notification->n_type    = 1;
        //   $notification->save();

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
            if ($result === FALSE) {
                die('Oops! FCM Send Error: ' . curl_error($ch));
            }
            // print_r($result);
            curl_close($ch);
            
          }
            
        }
        
        
        
        
       $data1['status']  = true;
       $data1['message'] = 'Item added successfully.';
       return response()->json($data1);







    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function product_details(Request $request)
    {
        //
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);

        // $user = User::where('id',$request->user_id)->where('auth_token',$request->token)->where('role',2)->first();
        // if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
        
        
         
          $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',2)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
        
 
          $product = Product::where('id',$request->product_id)
                                                          ->where('vendor_id',$user->id)
                                                          ->where('status',1)
                                                          ->where('is_deleted',0)
                                                          ->first();
                                                          
        
        if(empty($product)) return response()->json(['status'=>false,'message'=>'No any product.']);

        $productRaw = [];    
        $rawSize  =[];
        
       
        foreach ($product->product_size as $key1 => $value1) {
            
            
              $cofsize = [];
               $productcolormain = Product_color::where('product_id',$value1->product_id)->where('size_id',$value1->size_id)->where('is_deleted',0)->get();
               foreach($productcolormain as $cr){
                   $cofsize[] = [
                   'id'=>$cr->id,
                   'color_id'=>$cr->color->id,
                   'color_name'=>$cr->color->color_name,
                   'colors'=>$cr->color->name,
                   'img1'=>($cr->img1) ? $cr->img1:"",
                   'img2'=>($cr->img2) ? $cr->img2:"",
                   'img3'=>($cr->img3) ? $cr->img3:"",
                   'img4'=>($cr->img4) ? $cr->img4:""
                   ];
               }
               
               
               $rawSize[] = [
                'size_id'=>$value1->id,
                'id'=>$value1->size->id,
                'name'=>$value1->size->name,
                'size_color'=>$cofsize
               ];
            }


            $rawColor = [];
            $productcolor = Product_color::where('product_id',$product->product_id)->where('is_deleted',0)->get();
            foreach ($productcolor as $key2 => $value2) {
               $rawColor[] = [
                'id'=>$value2->color->id,
                'name'=>$value2->color->name,
                'size_id'=>$value2->size_id,
                'img1'=>($value2->img1)? $value2->img1:$product->img1,
                'img2'=>($value2->img2) ? $value2->img2:'',
                'img3'=>($value2->img3) ? $value2->img3:'',
                'img4'=>($value2->img4) ? $value2->img4:'',
               ]; 
            }


            $rawReview = [];
            foreach ($product->review as $key3 => $value3) {

                $reviewImage =[];
                
                if(!empty($value3->img1) && $value3->img1 !=null){
                      $reviewImage[0] = ($value3->img1 !=0)? $value3->img1:"";
                }
                 if(!empty($value3->img2) && $value3->img2 !=null){
                      $reviewImage[1] = ($value3->img2 !=0) ? $value3->img2:"";
                }
                 if(!empty($value3->img3) && $value3->img3 !=null){
                      $reviewImage[2] = ($value3->img3 !=0) ? $value3->img3:"";
                }
                 if(!empty($value3->img4) && $value3->img4 !=null){
                      $reviewImage[3] = ($value3->img4 !=0) ? $value3->img4:"";
                }
                
                
               $rawReview[] = [
                'id'=>$value3->id,
                'user_id'  =>$value3->user->id,
                'image'  =>$value3->user->image,
                'name'   =>$value3->user->name,
                'comment'=>$value3->comment,
                'review_images' =>$reviewImage,
                'date'=>Carbon::parse($value3->created_at)->format('d F Y ')
                
               ]; 
            }

            $productRaw = [
                'id'                  =>$product->id, 
                'title'               =>$product->title, 
                'description'         =>$product->description, 
                'img1'                =>$product->img1,
                'img2'                =>($product->img2) ? $product->img2:'',
                'img3'                =>($product->img3) ? $product->img3:'',
                'img4'                =>($product->img4) ? $product->img4:'',
                'is_summer_collection'=>$product->is_summer_collection,
                'sub_subcategory_id'  =>$product->sub_subcategory_id, 
                'en_subsubcategory'   =>($product->subsubcategory) ? $product->subsubcategory->en_subsubcategory:"", 
                'ar_subsubcategory'   =>($product->subsubcategory) ? $product->subsubcategory->ar_subsubcategory:"", 
                'subcategory_id'      =>$product->subcategory_id, 
                'en_subcategory'      =>$product->subcategory->en_subcategory, 
                'ar_subcategory'      =>$product->subcategory->ar_subcategory, 
                'category_id'         =>$product->category_id,
                'en_category'         =>$product->category->en_category,
                'ar_category'         =>$product->category->ar_category,
                'price'               =>round($product->price,2),
                'product_size'        =>$rawSize,
                'product_color'       =>$rawColor,
                'like_count'          =>$product->like->count(),
                'comments_count'      =>count($rawReview),
                'reviews'             =>$rawReview,
                'web_url'             =>'http://cuma.co/'
            ];
        




       $data['status']  = true;
       $data['data']    = ['product'=>$productRaw];
       $data['message'] = 'Single product details.';
       return response()->json($data);



    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get_reviews(Request $request)
    {
        //
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);

        // $user = User::where('id',$request->user_id)
        //                                     ->where('auth_token',$request->token)
        //                                     ->where('role',2)
        //                                     ->where('is_deleted',0)
        //                                     ->first();


        // if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
        
        
         
          $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',2)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
        

        if(empty($request->product_id)) return response()->json(['status'=>false,'message'=>'Product id is required.']);
        $skip = (empty($request->skip)) ? 0:$request->skip;
        
        $reviewRaw = Review::where('product_id',$request->product_id)
        ->where('is_deleted',0)
        ->skip($skip)
        ->limit(2)
        ->get();
        $review = [];
        foreach ($reviewRaw as $key => $value) {
            
            
              $reviewImage =[];
                
                if(!empty($value->img1)){
                      $reviewImage[0] = $value->img1;
                }
                 if(!empty($value->img2)){
                      $reviewImage[1] = $value->img2;
                }
                 if(!empty($value->img3)){
                      $reviewImage[2] = $value->img3;
                }
                 if(!empty($value->img4)){
                      $reviewImage[3] = $value->img4;
                }
                
                
            $review[] = [
                'id'=>$value->id,
                'user_id'  =>$value->user->id,
                'image'  =>$value->user->image,
                'name'   =>$value->user->name,
                'comment'=>$value->comment,
                'review_images' =>$reviewImage,
                'date'=>Carbon::parse($value->created_at)->format('d F Y ')
            ];
        }

        
       
       $data['status']  = true;
       $data['data']    = ['review'=>$review];
       $data['message'] = 'Reviews list.';
       return response()->json($data);





    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
     
    
    
    public function get_product(Request $request){
        
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);

        // $user = User::where('id',$request->user_id)->where('auth_token',$request->token)->where('role',2)->first();
        // if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
        
        
         
          $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',2)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
        
        
        $product = Product::find($request->product_id);
        
        
               $productImage=[];
               
                if(!empty($product->img1)){
                      $productImage[0] = $product->img1;
                }
                 if(!empty($product->img2)){
                      $productImage[1] = $product->img2;
                }
                 if(!empty($product->img3)){
                      $productImage[2] = $product->img3;
                }
                 if(!empty($product->img4)){
                      $productImage[3] = $product->img4;
                }
                
         $arr_size = [];
         $arrt=[];
         foreach($product->product_size as $rowsize){
             $arr_size[] = ['size_id'=>$rowsize->size_id];
             $arrt[] = $rowsize->size_color;
         }
         
         
        $data['status'] = true;
        $data['data'] = [
            
            'id'                  =>$product->id,
            'title'               =>$product->title,
            'description'         =>$product->description,
            'price'               =>round($product->price,2),
            'image'               =>$productImage,
            'sizes'               =>$arr_size,
            'color'               =>$arrt,
            'category_id'         =>$product->category_id,
            'subcategory_id'      =>$product->subcategory_id,
            'sub_subcategory_id'  =>($product->sub_subcategory_id) ? $product->sub_subcategory_id:'',
            'type_id'             =>($product->type_id) ? $product->type_id:'',
            'is_summer_collection'=>$product->is_summer_collection
            
            ];
        $data['message'] = "Get product details";
        return response()->json($data);
       
        
    }
    
    
    // not completed
    // public function update(Request $request)
    // {
    //     // print_r($request->color); die;
    //       //  print_r($request->all()); die;
    //     if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
    //     if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);
    //     if(empty($request->product_id)) return response()->json(['status'=>false,'message'=>'product id is required.']);

    //     $user = User::where('id',$request->user_id)->where('auth_token',$request->token)->where('role',2)->first();
    //     if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);

          

    //     $product                       = Product::find($request->product_id);
    //     $product->title                = $request->title;
    //     $product->description          = $request->description;
    //     $product->price                = $request->price;
        
    //     // $file1 = $request->image[0];
    //     // $filename1 = time().'1.'.$file1->getClientOriginalExtension();
    //     // $file1->move('public/images/product', $filename1);
    //     // $product->img1                 = '/public/images/product/'.$filename1;

    //     // if(isset($request->image[1])){
    //     // $file2 = $request->image[1];
    //     // $filename2 = time().'2.'.$file2->getClientOriginalExtension();
    //     // $file2->move('public/images/product', $filename2);  
    //     // $product->img2                 = '/public/images/product/'.$filename2;
    //     // }
        
        
    //     // if(isset($request->image[2])){
    //     // $file3 = $request->image[2];
    //     // $filename3 = time().'3.'.$file3->getClientOriginalExtension();
    //     // $file3->move('public/images/product', $filename3);
    //     // $product->img3                 = '/public/images/product/'.$filename3;
    //     // }
       
        
    //     // if(isset($request->image[3])){
    //     // $file4 = $request->image[3];
    //     // $filename4 = time().'4.'.$file4->getClientOriginalExtension();
    //     // $file4->move('public/images/product', $filename4);
    //     // $product->img4                 = '/public/images/product/'.$filename4;
    //     // }
        
        
    //     if(isset($request->img1) && !empty($request->img1)){
    //     $file1 = $request->img1;
    //     $filename1 = time().'1.'.$file1->getClientOriginalExtension();
    //     $file1->move('public/images/product', $filename1);
    //     $product->img1                 = '/public/images/product/'.$filename1;
        
    //      }

    //     if(isset($request->img2) && !empty($request->img2)){
    //     $file2 = $request->img2;
    //     $filename2 = time().'2.'.$file2->getClientOriginalExtension();
    //     $file2->move('public/images/product', $filename2);  
    //     $product->img2                 = '/public/images/product/'.$filename2;
    //     }
        
        
    //     if(isset($request->img3) && !empty($request->img3)){
    //     $file3 = $request->img3;
    //     $filename3 = time().'3.'.$file3->getClientOriginalExtension();
    //     $file3->move('public/images/product', $filename3);
    //     $product->img3                 = '/public/images/product/'.$filename3;
    //     }
       
        
    //     if(isset($request->img4) && !empty($request->img4)){
    //     $file4 = $request->img4;
    //     $filename4 = time().'4.'.$file4->getClientOriginalExtension();
    //     $file4->move('public/images/product', $filename4);
    //     $product->img4                 = '/public/images/product/'.$filename4;
    //     }
        
        
        
 
    //     // $product->vendor_id            = $user->id;
    //     // $product->type_id              = $request->type_id;
    //     // $product->sub_subcategory_id   = $request->sub_subcategory_id;
    //     // $product->subcategory_id       = $request->subcategory_id;
    //     // $product->category_id          = $user->category_id;
    //     $product->is_summer_collection = $request->is_summer_collection;
    //     $product->save();

    //     if(!empty($request->size)){
            
    //          Product_size::where('product_id',$product->id)->update(['is_deleted'=>1]);
    //         foreach (explode(",", $request->size) as $key => $value) {
    //             # code...
                
                
    //             $p_size = new Product_size;
    //             $p_size->size_id    = $value;
    //             $p_size->product_id = $product->id;
    //             $p_size->save();
    //         }
    //     }
         
        
    //      if(isset($request->color) &&  count($request->color) > 0 ){
             
    //           Product_color::where('product_id',$product->id)->update(['is_deleted'=>1]);
              
    //      foreach ($request->color as $key => $value) {
           
    //       foreach($value as $key1 => $rows){
               
    //           $oldimg =  Product_color::where('product_id',$product->id)->where('color_id',$key1)->where('size_id',$key)->first();
                
    //             $p_size1 = new Product_color;
    //             $p_size1->color_id    = $key1;
    //             $p_size1->size_id     = $key;
    //             $p_size1->product_id  = $product->id;
                
    //             if(isset($rows['img1'])){
    //                 $file5 = $rows['img1'];
    //                 $filename5 = time().$key1.'1.'.$file5->getClientOriginalExtension();
    //                 $file5->move('public/images/product', $filename5);
    //                 $p_size1->img1        = '/public/images/product/'.$filename5;
    //             }else{
    //                 $p_size1->img1 = $oldimg->img1;
    //             }

    //             if(isset($rows['img2'])){
    //             $file6 = $rows['img2'];
    //             $filename6 = time().$key1.'2.'.$file6->getClientOriginalExtension();
    //             $file6->move('public/images/product', $filename6);
    //             $p_size1->img2        = '/public/images/product/'.$filename6;
    //             }else{
    //                 $p_size1->img2 = $oldimg->img2;
    //             }

    //             if(isset($rows['img3'])){
    //             $file7 = $rows['img3'];
    //             $filename7 = time().$key1.'3.'.$file7->getClientOriginalExtension();
    //             $file7->move('public/images/product', $filename7);
    //              $p_size1->img3        = '/public/images/product/'.$filename7;
    //             }else{
    //                 $p_size1->img3 = $oldimg->img3;
    //             }


    //             if(isset($rows['img4'])){
    //             $file8 = $rows['img4'];
    //             $filename8 = time().$key1.'4.'.$file8->getClientOriginalExtension();
    //             $file8->move('public/images/product', $filename8);
    //             $p_size1->img4        = '/public/images/product/'.$filename8;
    //             }else{
    //               $p_size1->img4 = $oldimg->img4;
    //             }
                
    //             $p_size1->save(); 
               
    //       }
              
           
          
          
        
    //         }

    //     }
    
    
    
    
    
    
    
    //temporary close
    
    
    
    //  public function update(Request $request)
    // {
    //     //  print_r($request->all()); die;
    //         // echo "<pre>";
    //     //  return $request->all(); 
            
    //     if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
    //     if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);
    //     if(empty($request->product_id)) return response()->json(['status'=>false,'message'=>'product id is required.']);

    //     $user = User::where('id',$request->user_id)->where('auth_token',$request->token)->where('role',2)->first();
    //     if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);

          

    //     $product                       = Product::find($request->product_id);
    //     $product->title                = $request->title;
    //     $product->description          = $request->description;
    //     $product->price                = $request->price;
    //     $product->response_data        = $request->img1;
    //     // $file1 = $request->image[0];
    //     // $filename1 = time().'1.'.$file1->getClientOriginalExtension();
    //     // $file1->move('public/images/product', $filename1);
    //     // $product->img1                 = '/public/images/product/'.$filename1;

    //     // if(isset($request->image[1])){
    //     // $file2 = $request->image[1];
    //     // $filename2 = time().'2.'.$file2->getClientOriginalExtension();
    //     // $file2->move('public/images/product', $filename2);  
    //     // $product->img2                 = '/public/images/product/'.$filename2;
    //     // }
        
        
    //     // if(isset($request->image[2])){
    //     // $file3 = $request->image[2];
    //     // $filename3 = time().'3.'.$file3->getClientOriginalExtension();
    //     // $file3->move('public/images/product', $filename3);
    //     // $product->img3                 = '/public/images/product/'.$filename3;
    //     // }
       
        
    //     // if(isset($request->image[3])){
    //     // $file4 = $request->image[3];
    //     // $filename4 = time().'4.'.$file4->getClientOriginalExtension();
    //     // $file4->move('public/images/product', $filename4);
    //     // $product->img4                 = '/public/images/product/'.$filename4;
    //     // }
        
        
    //     if(isset($request->img1) && !empty($request->img1)){
    //         $file1 = $request->img1;
    //         $filename1 = time().'1.'.$file1->getClientOriginalExtension();
    //         $file1->move('public/images/product', $filename1);
    //         $product->img1                 = '/public/images/product/'.$filename1;
        
    //      }else{
    //          $product->img1  = $product->img1;
    //      }

    //     if(isset($request->img2) && !empty($request->img2)){
    //     $file2 = $request->img2;
    //     $filename2 = time().'2.'.$file2->getClientOriginalExtension();
    //     $file2->move('public/images/product', $filename2);  
    //     $product->img2                 = '/public/images/product/'.$filename2;
    //     }else{
    //          $product->img2  = $product->img2;
    //     }
        
        
    //     if(isset($request->img3) && !empty($request->img3)){
    //     $file3 = $request->img3;
    //     $filename3 = time().'3.'.$file3->getClientOriginalExtension();
    //     $file3->move('public/images/product', $filename3);
    //     $product->img3                 = '/public/images/product/'.$filename3;
    //     }else{
    //          $product->img3  = $product->img3;
    //     }
       
        
    //     if(isset($request->img4) && !empty($request->img4)){
    //     $file4 = $request->img4;
    //     $filename4 = time().'4.'.$file4->getClientOriginalExtension();
    //     $file4->move('public/images/product', $filename4);
    //     $product->img4                 = '/public/images/product/'.$filename4;
    //     }else{
    //          $product->img4  = $product->img4;
    //     }
        
        
        
 
    //     // $product->vendor_id            = $user->id;
    //     // $product->type_id              = $request->type_id;
    //     // $product->sub_subcategory_id   = $request->sub_subcategory_id;
    //     // $product->subcategory_id       = $request->subcategory_id;
    //     // $product->category_id          = $user->category_id;
    //     $product->is_summer_collection = $request->is_summer_collection;
    //     $product->save();

    //     if(!empty($request->size)){
            
            
    //         foreach (explode(",", $request->size) as $key => $value) {
    //             # code...
    //             $prodSize =  Product_size::where('product_id',$product->id)->where('size_id',$value)->where('is_deleted',0)->first();
    //             if(empty($prodSize)){
    //                 $p_size = new Product_size;
    //                 $p_size->size_id    = $value;
    //                 $p_size->product_id = $product->id;
    //                 $p_size->save();      
    //             }
                
    //         }
    //     }
         
        
    //      if(isset($request->color) &&  count($request->color) > 0 ){
             
    //         //   Product_color::where('product_id',$product->id)->update(['is_deleted'=>1]);
              
    //      foreach ($request->color as $key => $value) {
           
    //       foreach($value as $key1 => $rows){
               
    //             $p_size1 =  Product_color::where('product_id',$product->id)->where('color_id',$key1)->where('size_id',$key)->where('is_deleted',0)->first();
                
    //             if(empty($p_size1)){
                    
    //                 $p_size1 = new Product_color;
    //                 $p_size1->color_id    = $key1;
    //                 $p_size1->size_id     = $key;
    //                 $p_size1->product_id  = $product->id;
    //             }
               
                
    //             if(isset($rows['img1'])){
    //                 $file5 = $rows['img1'];
    //                 $filename5 = time().$key1.'1.'.$file5->getClientOriginalExtension();
    //                 $file5->move('public/images/product', $filename5);
    //                 $p_size1->img1        = '/public/images/product/'.$filename5;
    //             }else{
    //                 $p_size1->img1 = ($p_size1->img1) ? $p_size1->img1:0;
    //             }

    //             if(isset($rows['img2'])){
    //             $file6 = $rows['img2'];
    //             $filename6 = time().$key1.'2.'.$file6->getClientOriginalExtension();
    //             $file6->move('public/images/product', $filename6);
    //             $p_size1->img2        = '/public/images/product/'.$filename6;
    //             }else{
    //                 $p_size1->img2 = ($p_size1->img2) ? $p_size1->img2:0;
    //             }

    //             if(isset($rows['img3'])){
    //             $file7 = $rows['img3'];
    //             $filename7 = time().$key1.'3.'.$file7->getClientOriginalExtension();
    //             $file7->move('public/images/product', $filename7);
    //              $p_size1->img3        = '/public/images/product/'.$filename7;
    //             }else{
                   
    //                 $p_size1->img3 = ($p_size1->img3) ? $p_size1->img3:0;
    //             }


    //             if(isset($rows['img4'])){
    //             $file8 = $rows['img4'];
    //             $filename8 = time().$key1.'4.'.$file8->getClientOriginalExtension();
    //             $file8->move('public/images/product', $filename8);
    //             $p_size1->img4        = '/public/images/product/'.$filename8;
    //             }else{
    //               $p_size1->img4 = ($p_size1->img4)? $p_size1->img4:0;
    //             }
                
    //             $p_size1->save(); 
               
    //       }
              
           
          
          
        
    //         }

    //     }
        
        
        
        
        
    // //   $myfollowers =  Follow::where('user_id',$user->id)->where('allow_notification',1)->get();
        
    // //     foreach($myfollowers as $row){
            
    // //      $userd = User::find($row->follower_id);
    // //      $json_data =[
    // //                 "to" => $userd->fcm_token,
    // //                 "notification" => [
    // //                     "body" => "test test",
    // //                     "title" =>$user->name." added new product",
    // //                     "type"=>"ORDER"
    // //                     // "icon" => "ic_launcher"
    // //                 ]
    // //             ];
                    

    // //       $data = json_encode($json_data);
    // //       $url = 'https://fcm.googleapis.com/fcm/send';
    // //       //header with content_type api key
    // //       $headers = array(
    // //             'Content-Type:application/json',
    // //             'Authorization:key=AAAARvckVyM:APA91bH0MG3_sAHNxiGXN5VYRm3prNjnm4fvxzSI5WnCh2E8aD9XsUn8IptEpmtgeJCUywIzsu-Ww1Xa-nYMhtga60kh59Qebk4-7JLPvYig0x9JgRtKk-SCo42ZvnqTWoXUCgk-XdF2'
    // //         );
    // //         //CURL request to route notification to FCM connection server (provided by Google)
    // //         $ch = curl_init();
    // //         curl_setopt($ch, CURLOPT_URL, $url);
    // //         curl_setopt($ch, CURLOPT_POST, true);
    // //         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    // //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // //         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    // //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // //         curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    // //         $result = curl_exec($ch);
    // //         if ($result === FALSE) {
    // //             die('Oops! FCM Send Error: ' . curl_error($ch));
    // //         }
    // //         // print_r($result);
    // //         curl_close($ch);
            
    // //     }
        
        
        
        
    //   $data1['status']  = true;
    //   $data1['message'] = 'Item updated successfully.';
    //   return response()->json($data1);
       
       
       
    // }
    
    
    
    
    
    
    
    
    
    
    
    
    
     public function update(Request $request)
     {
        //  print_r($request->all()); die;
            // echo "<pre>";
        //  return $request->all(); 
            
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);
        if(empty($request->product_id)) return response()->json(['status'=>false,'message'=>'product id is required.']);

        // $user = User::where('id',$request->user_id)->where('auth_token',$request->token)->where('role',2)->first();
        // if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
        
        
         
          $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',2)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        

          

        $product                       = Product::find($request->product_id);
        $product->title                = $request->title;
        $product->description          = $request->description;
        $product->price                = $request->price;
        $product->response_data        = $request->img1;
        // $file1 = $request->image[0];
        // $filename1 = time().'1.'.$file1->getClientOriginalExtension();
        // $file1->move('public/images/product', $filename1);
        // $product->img1                 = '/public/images/product/'.$filename1;

        // if(isset($request->image[1])){
        // $file2 = $request->image[1];
        // $filename2 = time().'2.'.$file2->getClientOriginalExtension();
        // $file2->move('public/images/product', $filename2);  
        // $product->img2                 = '/public/images/product/'.$filename2;
        // }
        
        
        // if(isset($request->image[2])){
        // $file3 = $request->image[2];
        // $filename3 = time().'3.'.$file3->getClientOriginalExtension();
        // $file3->move('public/images/product', $filename3);
        // $product->img3                 = '/public/images/product/'.$filename3;
        // }
       
        
        // if(isset($request->image[3])){
        // $file4 = $request->image[3];
        // $filename4 = time().'4.'.$file4->getClientOriginalExtension();
        // $file4->move('public/images/product', $filename4);
        // $product->img4                 = '/public/images/product/'.$filename4;
        // }
        
        
        if(isset($request->img1) && !empty($request->img1)){
            $file1 = $request->img1;
            $filename1 = time().'1.'.$file1->getClientOriginalExtension();
            $file1->move('public/images/product', $filename1);
            $product->img1                 = '/public/images/product/'.$filename1;
        
         }else{
             $product->img1  = $product->img1;
         }

        if(isset($request->img2) && !empty($request->img2)){
        $file2 = $request->img2;
        $filename2 = time().'2.'.$file2->getClientOriginalExtension();
        $file2->move('public/images/product', $filename2);  
        $product->img2                 = '/public/images/product/'.$filename2;
        }else{
             $product->img2  = $product->img2;
        }
        
        
        if(isset($request->img3) && !empty($request->img3)){
        $file3 = $request->img3;
        $filename3 = time().'3.'.$file3->getClientOriginalExtension();
        $file3->move('public/images/product', $filename3);
        $product->img3                 = '/public/images/product/'.$filename3;
        }else{
             $product->img3  = $product->img3;
        }
       
        
        if(isset($request->img4) && !empty($request->img4)){
        $file4 = $request->img4;
        $filename4 = time().'4.'.$file4->getClientOriginalExtension();
        $file4->move('public/images/product', $filename4);
        $product->img4                 = '/public/images/product/'.$filename4;
        }else{
             $product->img4  = $product->img4;
        }
        
        
        
 
        // $product->vendor_id            = $user->id;
        // $product->type_id              = $request->type_id;
        // $product->sub_subcategory_id   = $request->sub_subcategory_id;
        // $product->subcategory_id       = $request->subcategory_id;
        // $product->category_id          = $user->category_id;
        $product->is_summer_collection = $request->is_summer_collection;
        $product->save();

        if(!empty($request->size)){
            
            
            foreach (explode(",", $request->size) as $key => $value) {
                # code...
                $prodSize =  Product_size::where('product_id',$product->id)->where('size_id',$value)->where('is_deleted',0)->first();
                if(empty($prodSize)){
                    $p_size = new Product_size;
                    $p_size->size_id    = $value;
                    $p_size->product_id = $product->id;
                    $p_size->save();      
                }
                
            }
        }
         
        
         if(isset($request->color) &&  count($request->color) > 0 ){
             
            //   Product_color::where('product_id',$product->id)->update(['is_deleted'=>1]);
              
         foreach ($request->color as $key => $value) {
           
           foreach($value as $key1 => $rows){
               
                $p_size1 =  Product_color::where('product_id',$product->id)->where('color_id',$key1)->where('size_id',$key)->where('is_deleted',0)->first();
                
                if(empty($p_size1)){
                    
                    $p_size1 = new Product_color;
                    $p_size1->color_id    = $key1;
                    $p_size1->size_id     = $key;
                    $p_size1->product_id  = $product->id;
                }
               
                
                // if(isset($rows['img1'])){
                //     $file5 = $rows['img1'];
                //     $filename5 = time().$key1.'1.'.$file5->getClientOriginalExtension();
                //     $file5->move('public/images/product', $filename5);
                //     $p_size1->img1        = '/public/images/product/'.$filename5;
                // }else{
                //     $p_size1->img1 = ($p_size1->img1) ? $p_size1->img1:0;
                // }

                // if(isset($rows['img2'])){
                // $file6 = $rows['img2'];
                // $filename6 = time().$key1.'2.'.$file6->getClientOriginalExtension();
                // $file6->move('public/images/product', $filename6);
                // $p_size1->img2        = '/public/images/product/'.$filename6;
                // }else{
                //     $p_size1->img2 = ($p_size1->img2) ? $p_size1->img2:0;
                // }

                // if(isset($rows['img3'])){
                // $file7 = $rows['img3'];
                // $filename7 = time().$key1.'3.'.$file7->getClientOriginalExtension();
                // $file7->move('public/images/product', $filename7);
                //  $p_size1->img3        = '/public/images/product/'.$filename7;
                // }else{
                   
                //     $p_size1->img3 = ($p_size1->img3) ? $p_size1->img3:0;
                // }


                // if(isset($rows['img4'])){
                // $file8 = $rows['img4'];
                // $filename8 = time().$key1.'4.'.$file8->getClientOriginalExtension();
                // $file8->move('public/images/product', $filename8);
                // $p_size1->img4        = '/public/images/product/'.$filename8;
                // }else{
                //   $p_size1->img4 = ($p_size1->img4)? $p_size1->img4:0;
                // }
                
                
                
               if(isset($rows[0])){
                   
                    $file5 = $rows[0];
                    $filename5 = time().$key.'1'.$file5->getClientOriginalName();
                    $file5->move('public/images/product', $filename5);
                    
                    if($p_size1->img1=="0"){
                       
                         if($p_size1->img2!="0")
                          {
                              $p_size1->img1        = '/public/images/product/'.$filename5; 
                              
                          }elseif($p_size1->img3!="0"){
                              
                              $p_size1->img1        = '/public/images/product/'.$filename5;
                              
                          }elseif($p_size1->img4!="0"){
                              
                               $p_size1->img1        = '/public/images/product/'.$filename5;
                               
                          }else{
                              
                              $p_size1->img1        = '/public/images/product/'.$filename5;
                          }
                         
                          
                    }elseif($p_size1->img2=="0"){
                        
                         if($p_size1->img1!="0")
                          {
                             $p_size1->img2        = '/public/images/product/'.$filename5; 
                          }elseif($p_size1->img3!="0"){
                             $p_size1->img2        = '/public/images/product/'.$filename5; 
                          }elseif($p_size1->img4!="0"){
                             $p_size1->img2        = '/public/images/product/'.$filename5; 
                          }else{
                              $p_size1->img1        = '/public/images/product/'.$filename5;
                          }
                         
                          
                    }elseif($p_size1->img3=="0"){
                       
                        if($p_size1->img1!="0")
                          {
                             $p_size1->img3        = '/public/images/product/'.$filename5; 
                          }elseif($p_size1->img2!="0"){
                              
                                 $p_size1->img3        = '/public/images/product/'.$filename5; 
                          }elseif($p_size1->img4!="0"){
                              
                                 $p_size1->img3        = '/public/images/product/'.$filename5; 
                          }else{
                             $p_size1->img1        = '/public/images/product/'.$filename5;
                          }
                          
                          
                    }elseif($p_size1->img4=="0"){
                          
                          if($p_size1->img1!="0")
                          {
                             $p_size1->img4        = '/public/images/product/'.$filename5; 
                          }elseif($p_size1->img2!="0"){
                              $p_size1->img4        = '/public/images/product/'.$filename5;
                          }elseif($p_size1->img3!="0"){
                              $p_size1->img4        = '/public/images/product/'.$filename5;
                          }else{
                             $p_size1->img1        = '/public/images/product/'.$filename5;
                          }
                         
                    }
                    
                }

                if(isset($rows[1])){
                    
                $file6 = $rows[1];
                $filename6 = time().$key.'2'.$file6->getClientOriginalName();
                $file6->move('public/images/product', $filename6);
              
                
                 if($p_size1->img1=="0"){
                      
                        if($p_size1->img2!="0")
                          {
                             $p_size1->img1 = '/public/images/product/'.$filename6; 
                          }elseif($p_size1->img3!="0"){
                             $p_size1->img1 = '/public/images/product/'.$filename6;   
                          }elseif($p_size1->img4!="0"){
                              $p_size1->img1 = '/public/images/product/'.$filename6;  
                          }else{
                             $p_size1->img2 = '/public/images/product/'.$filename6;
                         }
                          
                           
                            
                 }elseif($p_size1->img2=="0"){
                       
                       
                        if($p_size1->img1!="0")
                          {
                             $p_size1->img2 = '/public/images/product/'.$filename6; 
                          }elseif($p_size1->img3!="0"){
                             $p_size1->img2 = '/public/images/product/'.$filename6;  
                          }elseif($p_size1->img4!="0"){
                             $p_size1->img2 = '/public/images/product/'.$filename6;   
                          }else{
                             $p_size1->img2      = '/public/images/product/'.$filename6;
                         }
                     
                       
                 }elseif($p_size1->img3=="0"){
                     
                     
                      if($p_size1->img1!="0")
                          {
                               $p_size1->img3 = '/public/images/product/'.$filename6; 
                          }elseif($p_size1->img2!="0"){
                               $p_size1->img3 = '/public/images/product/'.$filename6; 
                          }elseif($p_size1->img4!="0"){
                               $p_size1->img3 = '/public/images/product/'.$filename6; 
                          }else{
                               $p_size1->img2      = '/public/images/product/'.$filename6;
                         }
                    //   $p_size1->img2      = '/public/images/product/'.$filename6;
                       
                 }elseif($p_size1->img4=="0"){
                     
                      if($p_size1->img1!="0")
                          {
                             $p_size1->img4 = '/public/images/product/'.$filename6; 
                          }elseif($p_size1->img2!="0"){
                             $p_size1->img4 = '/public/images/product/'.$filename6;  
                          }elseif($p_size1->img3!="0"){
                             $p_size1->img4 = '/public/images/product/'.$filename6;  
                          }else{
                             $p_size1->img2      = '/public/images/product/'.$filename6;
                         }
                         
                         
                        //  $p_size1->img2   = '/public/images/product/'.$filename6;
                        
                 }
                    
                    
                
                }

                if(isset($rows[2])){
                    
                $file7 = $rows[2];
                $filename7 = time().$key.'3'.$file7->getClientOriginalName();
                $file7->move('public/images/product', $filename7);
                
                 
                 if($p_size1->img1=="0"){
                     
                      if($p_size1->img2!="0")
                          {
                               $p_size1->img1        = '/public/images/product/'.$filename7;
                          }elseif($p_size1->img3!="0"){
                              
                               $p_size1->img1        = '/public/images/product/'.$filename7;  
                          }elseif($p_size1->img4!="0"){
                              
                               $p_size1->img1        = '/public/images/product/'.$filename7; 
                          }else{
                              
                             $p_size1->img3        = '/public/images/product/'.$filename7;
                         }
                         
                        //  $p_size1->img3        = '/public/images/product/'.$filename7;
                       
                           
                 }elseif($p_size1->img2=="0"){
                     
                      if($p_size1->img1!="0")
                          {
                             $p_size1->img2        = '/public/images/product/'.$filename7;
                          }elseif($p_size1->img3!="0"){
                              $p_size1->img2        = '/public/images/product/'.$filename7;
                          }elseif($p_size1->img4!="0"){
                             $p_size1->img2        = '/public/images/product/'.$filename7; 
                          }else{
                             $p_size1->img3        = '/public/images/product/'.$filename7;
                         }
                    //  $p_size1->img3        = '/public/images/product/'.$filename7;
                       
                 }elseif($p_size1->img3=="0"){
                     
                     
                         if($p_size1->img1!="0")
                        {
                             $p_size1->img3        = '/public/images/product/'.$filename7;
                        }elseif($p_size1->img2!="0"){
                              $p_size1->img3        = '/public/images/product/'.$filename7;
                                  
                        }elseif($p_size1->img4!="0"){
                              $p_size1->img3        = '/public/images/product/'.$filename7;
                        }else{
                             $p_size1->img3        = '/public/images/product/'.$filename7;
                         }
                      
                        // $p_size1->img3        = '/public/images/product/'.$filename7;
                 }elseif($p_size1->img4=="0"){
                     
                     
                     
                      if($p_size1->img1!="0")
                          {
                             $p_size1->img4        = '/public/images/product/'.$filename7;
                          }elseif($p_size1->img2!="0"){
                              $p_size1->img4        = '/public/images/product/'.$filename7;
                          }elseif($p_size1->img3!="0"){
                              $p_size1->img4        = '/public/images/product/'.$filename7;
                          }else{
                             $p_size1->img3        = '/public/images/product/'.$filename7;
                         }
                    //   $p_size1->img3        = '/public/images/product/'.$filename7;
                 }
                 
                 
                 
                }


                if(isset($rows[3])){
                    
                $file8 = $rows[3];
                $filename8 = time().$key.'4'.$file8->getClientOriginalName();
                $file8->move('public/images/product', $filename8);
               
                
                 if($p_size1->img1=="0"){
                     
                     
                      if($p_size1->img2!="0")
                          {
                             $p_size1->img1        = '/public/images/product/'.$filename8;
                          }elseif($p_size1->img3!="0"){
                              $p_size1->img1        = '/public/images/product/'.$filename8;  
                          }elseif($p_size1->img4!="0"){
                               $p_size1->img1        = '/public/images/product/'.$filename8; 
                          }else{
                             $p_size1->img4        = '/public/images/product/'.$filename8;
                         }
                         
                        //  $p_size1->img4        = '/public/images/product/'.$filename8;
                    
                            
                 }else if($p_size1->img2=="0"){
                     
                     
                      if($p_size1->img1!="0")
                          {
                             $p_size1->img2        = '/public/images/product/'.$filename8;
                          }elseif($p_size1->img3!="0"){
                               $p_size1->img2        = '/public/images/product/'.$filename8;
                          }elseif($p_size1->img4!="0"){
                               $p_size1->img2        = '/public/images/product/'.$filename8;
                          }else{
                             $p_size1->img4        = '/public/images/product/'.$filename8;
                         }
                         
                        //  $p_size1->img4        = '/public/images/product/'.$filename8;
                      
                       
                 }else if($p_size1->img3=="0"){
                     
                     
                     
                      if($p_size1->img1!="0")
                          {
                             $p_size1->img3        = '/public/images/product/'.$filename8;
                          }elseif($p_size1->img2!="0"){
                              $p_size1->img3        = '/public/images/product/'.$filename8;
                          }elseif($p_size1->img4!="0"){
                             $p_size1->img3        = '/public/images/product/'.$filename8; 
                          }else{
                             $p_size1->img4        = '/public/images/product/'.$filename8;
                         }
                         
                         
                        //  $p_size1->img4        = '/public/images/product/'.$filename8;
                      
                         
                 }else if($p_size1->img4=="0"){
                     
                     
                     
                      if($p_size1->img1!="0")
                          {
                             $p_size1->img4        = '/public/images/product/'.$filename8;
                          }elseif($p_size1->img2!="0"){
                               $p_size1->img4        = '/public/images/product/'.$filename8;
                          }elseif($p_size1->img3!="0"){
                              $p_size1->img4        = '/public/images/product/'.$filename8; 
                          }else{
                             $p_size1->img4        = '/public/images/product/'.$filename8;
                         }
                      
                    //   $p_size1->img4        = '/public/images/product/'.$filename8;
                 }
                 
                
                }
                
                
                $p_size1->save(); 
               
           }
              
           
          
          
        
            }

        }
        
        
        
        
        
        
       $data1['status']  = true;
       $data1['message'] = 'Item updated successfully.';
       return response()->json($data1);
       
       
       
    }
    
    
    
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function testproduct(Request $request)
    {
        //
        echo "<pre>";
        print_r($request->all());
    }
    
    
    
    public function remove_size_color(Request $request)
    {
        
       if(empty($request->token))      return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
       if(empty($request->user_id))    return response()->json(['status'=>false,'message'=>'User is required.']);
       if(empty($request->id))         return response()->json(['status'=>false,'message'=>'Product is required.']);
          
        //   echo  $request->id; die;
          $productsize =  Product_color::find($request->id);
          $productsize->is_deleted = 1;
          $productsize->save();
        
        return response()->json(['status'=>true,'message'=>'color removed from size']);
        
       
    }
    
    
    public function remove_size_color_image(Request $request){
        
       if(empty($request->token))      return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
       if(empty($request->user_id))    return response()->json(['status'=>false,'message'=>'User is required.']);
       if(empty($request->size_color_id))         return response()->json(['status'=>false,'message'=>'Size color id is required.']);
       
       $productsize =  Product_color::find($request->size_color_id);
      if(empty($productsize))   return response()->json(['status'=>false,'message'=>'Product color  not found.']);
       if(!empty($request->img1)){
           $productsize->img1 = 0;
       }
       
       if(!empty($request->img2)){
            $productsize->img2 = 0;
       }
       
       
       if(!empty($request->img3)){
            $productsize->img3 = 0;
        }
       
       if(!empty($request->img4)){
            $productsize->img4 = 0;
       }
       $productsize->save();
       
       
      return response()->json(['status'=>true,'message'=>'image removed successfully.']);
       
       
       
    }
    
    
    public function delete_size(Request $request){
        
       if(empty($request->token))      return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
       if(empty($request->user_id))    return response()->json(['status'=>false,'message'=>'User is required.']);
       if(empty($request->size_id))         return response()->json(['status'=>false,'message'=>'size id color id is required.']);
       
      $pz =  Product_size::find($request->size_id);
      if(empty($pz))   return response()->json(['status'=>false,'message'=>'size not found.']);
      $pz->is_deleted = 1;
      $pz->save();
        return response()->json(['status'=>true,'message'=>'size deleted  successfully.']);
      
      
    }
    
    
    
    
     public function delete_product_image(Request $request){
        
       if(empty($request->token))      return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
       if(empty($request->user_id))    return response()->json(['status'=>false,'message'=>'User is required.']);
       if(empty($request->product_id))         return response()->json(['status'=>false,'message'=>'Product id is required.']);
       
       $productsize =  Product::find($request->product_id);
       if(empty($productsize))   return response()->json(['status'=>false,'message'=>'Product not found.']);
       if(!empty($request->img1)){
           $productsize->img1 = 0;
       }
       
       if(!empty($request->img2)){
            $productsize->img2 = 0;
       }
       
       
       if(!empty($request->img3)){
            $productsize->img3 = 0;
        }
       
       if(!empty($request->img4)){
            $productsize->img4 = 0;
       }
       $productsize->save();
       
       
      return response()->json(['status'=>true,'message'=>'image removed successfully.']);
       
       
       
    }
    
    
}
