<?php

namespace App\Http\Controllers\API\Shopper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Vendor_subcategory;
use App\Models\Like;
use App\Models\Review;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\Follow;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\Auth_token;


class ProductController extends Controller
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
        
        
        
        

        $category = Category::where('status',1)
                                ->where('is_deleted',0)
                                ->latest()
                                ->get();

        $product = Product::where('status',1)->where('is_deleted',0);

        //search by category
        if(count($request->category_id) > 0){
             $product = $product->whereIn('category_id',$request->category_id);
        }
        
        //search by keywords
        if(!empty($request->search)){
             $product = $product->where('title','LIKE',"%".$request->search."%");
        }
        
        //   price range
        if(!empty($request->min) && !empty($request->max)){
             $product = $product->whereBetween('price',[$request->min,$request->max]);
        }
        
          //   newest
        if(!empty($request->newest==1)){
             $product = $product->orderby('created_at','desc');
        }
        
       
       
                               
        $product = $product->latest()->skip($sk)->limit(15)->get();



       
        //size
        $productRaw = [];    
        foreach ($product as $key => $value) {

            $rawSize  =[];
            foreach ($value->product_size as $key1 => $value1) {
                
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
            
            
            //   $rawSize[] = [
            //     'id'=>$value1->size->id,
            //     'name'=>$value1->size->name
            //   ];
            //}

            
             //color
            $rawColor = [];
            foreach ($value->product_color as $key2 => $value2) {

              $fav = Like::where('product_id',$value->id)
                              ->where('user_id',$user->id)
                              ->first();


               $rawColor[] = [
                'id'=>$value2->color->id,
                'name'=>$value2->color->name,
                'img1'=>($value2->img1) ? $value2->img1 :$value->img1,
                'img2'=>($value2->img2) ? $value2->img2:'',
                'img3'=>($value2->img3) ? $value2->img3:'',
                'img4'=>($value2->img4) ? $value2->img4:'',
               ]; 
            }

          
          
          
             //rewiew
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
                'id'=>$value2->user->id,
                'name'=>$value2->user->name,
                'comment'=>$value2->comment,
                'review_images' =>$reviewImage,
                'date'=>Carbon::parse($value2->created_at)->format('d F Y ')
               ];
            }
            
            
            $checkable = Order_details::where('user_id',$user->id)->where('product_id',$value->id)->first();
            $checkfollow = Follow::where('user_id',$value->vendor_id)->where('follower_id',$user->id)->first();
            
            $productRaw[] = [
                'vendor_name'       =>$value->user->name,
                'vendor_image'      =>$value->user->image,
                'id'                =>$value->id, 
                'title'             =>$value->title, 
                'description'       =>$value->description, 
                'img1'              =>$value->img1,
                'img2'              =>($value->img2) ? $value->img2:'',
                'img3'              =>($value->img3) ? $value->img3:'',
                'img4'              =>($value->img4) ? $value->img4:'', 
                'sub_subcategory_id'=>$value->sub_subcategory_id, 
                'subcategory_id'    =>$value->subcategory_id, 
                'category_id'       =>$value->category_id,
                'price'             =>round($value->price,2),
                'product_size'      =>$rawSize,
                'product_color'     =>$rawColor,
                'like_count'        =>$value->like->count(),
                'comments_count'    =>count($rawRewiew),
                'liked'             =>(!empty($fav) ? 1:0),
                'review_status'     =>($checkable) ? 1:0,
                'follows_status'    =>($checkfollow) ? 1:0,
                'reviews'           =>$rawRewiew,
                'bestmatch_total'   =>$value->like->count() + $value->retweets->count(),
                'web_url'           =>'http://cuma.co/'

            ];
        }

       if($request->bestmatches==1){
        $srd =  array_column($productRaw,'bestmatch_total');  
         array_multisort($srd, SORT_DESC, $productRaw);
       }
       
       
       $data['status']  = true;
       $data['data']    = [
        'category'=>$category,
        'selected_category'=>isset($request->category_id) ?$request->category_id:"",
        'product'=>$productRaw,
        
       ];
       $data['message'] = 'Explorer data.';
       return response()->json($data);




    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store_products(Request $request)
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



        $subcategoryRaw = Vendor_subcategory::where('vendor_id',$request->vendor_id)
                                ->where('status',1)
                                ->where('is_deleted',0)
                                ->latest()
                                ->get();

        
        $subcategory =[];
        foreach ($subcategoryRaw as $key => $value) {
                   # code...
            $subcategory[] = [
                'id'=>$value->subcategory->id,
                'image'=>$value->subcategory->image,
                'en_subcategory'=>$value->subcategory->en_subcategory,
                'ar_subcategory'=>$value->subcategory->ar_subcategory,
                'category_id'=>$value->subcategory->category_id
            ];
        }       

        $product = Product::where('vendor_id',$request->vendor_id)
        ->where('status',1)
        ->where('is_deleted',0);

        //search by category
        if(!empty($request->subcategory_id)){

             $product = $product->where('subcategory_id',$request->subcategory_id);
        }
        
        //search by keywords
        if(!empty($request->search)){
             $product = $product->where('title','LIKE',"%".$request->search."%");
        }
                               
        $product = $product->latest()->get();




        $productRaw = [];    
        foreach ($product as $key => $value) {

            $rawSize  =[];
            foreach ($value->product_size as $key1 => $value1) {
                
                
                
                 $rawSize  =[];
            foreach ($value->product_size as $key1 => $value1) {
                
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
            
            
            
            
            
            //   $rawSize[] = [
            //     'id'=>$value1->size->id,
            //     'name'=>$value1->size->name
            //   ];
            }


            $rawColor = [];
            foreach ($value->product_color as $key2 => $value2) {

              $fav = Like::where('product_id',$value->id)
                              ->where('user_id',$user->id)
                              ->first();

            $rawColor[] = [
                'id'=>$value2->color->id,
                'name'=>$value2->color->name,
                'img1'=>($value2->img1) ? $value2->img1 :$value->img1,
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
                'id'=>$value2->id,
                'user_id'=>$value2->user->id,
                'name'=>$value2->user->name,
                'comment'=>$value2->comment,
                'review_images' =>$reviewImage,
                'date'=>Carbon::parse($value2->created_at)->format('d F Y ')
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
                'vendor_id'         =>$value->vendor_id,
                'liked'             =>(!empty($fav) ? 1:0),
                'reviews'           =>$rawRewiew,
                'web_url'           =>'http://cuma.co/'

            ];
        }


       $data['status']  = true;
       $data['data']    = [
        'subcategory'=>$subcategory,
        'selected_subcategory'=>isset($request->subcategory_id) ? $request->subcategory_id:"",
        'product'=>$productRaw,
        
       ];
       $data['message'] = 'Store products.';
       return response()->json($data);






    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function review(Request $request)
    {
        //
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);


        // $user = User::where('id',$request->user_id)
        // // ->where('role',1)
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
        
        
        

        if(empty($request->product_id)) return response()->json(['status'=>false,'message'=>'Product id is required.']);

        if(empty($request->rating)) return response()->json(['status'=>false,'message'=>'Rating is required.']);

        if($user->role == 1){
            
        
        $product = Product::find($request->product_id);

        $review = new Review;
        $review->product_id = $product->id;
        $review->vendor_id  = $product->vendor_id;
        $review->user_id    = $user->id;
        $review->rating     = $request->rating;
        $review->comment    = $request->comment;
        $review->type       = 1;
        if(isset($request->image[0])){
        
         $file1 = $request->image[0];
         $filename1 = time().'1.'.$file1->getClientOriginalExtension();
         $file1->move('public/images/reviews',$filename1);
         $review->img1    = '/public/images/reviews/'.$filename1;
        }
        
        if(isset($request->image[1])){
        
         $file2 = $request->image[1];
         $filename2 = time().'2.'.$file2->getClientOriginalExtension();
         $file2->move('public/images/reviews',$filename2);
         $review->img2    = '/public/images/reviews/'.$filename2;
        }
        
         if(isset($request->image[2])){
            
         $file3 = $request->image[2];
         $filename3 = time().'3.'.$file3->getClientOriginalExtension();
         $file3->move('public/images/reviews',$filename3);
         $review->img3    = '/public/images/reviews/'.$filename3;
        }
        
         if(isset($request->image[3])){
            
         $file4 = $request->image[3];
         $filename4 = time().'4.'.$file4->getClientOriginalExtension();
         $file4->move('public/images/reviews',$filename4);
         $review->img4    = '/public/images/reviews/'.$filename4;
        }
       
       $review->save();
        
        
        
        
        
        
        
        // PUSH NOTIFICATION

        //  $vndr = User::find($product->vendor_id);
         
         $getvendorAuth = Auth_token::where('user_id',$product->vendor_id)->where('fcm_token','!=','')->get();
          
          $notification = new Notification();   
          $notification->user_id   = $user->id;
          $notification->vendor_id = $product->vendor_id;
          $notification->title     = "User reviewed on product";
          $notification->body      = "test test";
          $notification->type      = "ORDER";
          $notification->n_type    = 2;
          $notification->save();
          
          
         foreach($getvendorAuth as $row1)
          
         $json_data =[
                    "to" => $row1->fcm_token,
                    "notification" => [
                        "body" => "test test",
                        "title" =>"User reviewed on product",
                        "type"=>"ORDER"
                        // "icon" => "ic_launcher"
                    ]
                ];
        
        //   $notification = new Notification();   
        //   $notification->user_id   = $user->id;
        //   $notification->vendor_id = $vndr->id;
        //   $notification->title     = "User reviewed on product";
        //   $notification->body      = "test test";
        //   $notification->type      = "ORDER";
        //   $notification->n_type    = 2;
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
            
        }else{
           
           
           
        $product = Product::find($request->product_id);

        $review = new Review;
        $review->product_id = $product->id;
        $review->vendor_id  = $product->vendor_id;
        $review->user_id    = $user->id;
        $review->rating     = $request->rating;
        $review->comment    = $request->comment;
        $review->type       = 2;
        
        if(isset($request->image[0])){
        
         $file1 = $request->image[0];
         $filename1 = time().'1.'.$file1->getClientOriginalExtension();
         $file1->move('public/images/reviews',$filename1);
         $review->img1    = '/public/images/reviews/'.$filename1;
        }
        
        if(isset($request->image[1])){
        
         $file2 = $request->image[1];
         $filename2 = time().'2.'.$file2->getClientOriginalExtension();
         $file2->move('public/images/reviews',$filename2);
         $review->img2    = '/public/images/reviews/'.$filename2;
        }
        
         if(isset($request->image[2])){
            
         $file3 = $request->image[2];
         $filename3 = time().'3.'.$file3->getClientOriginalExtension();
         $file3->move('public/images/reviews',$filename3);
         $review->img3    = '/public/images/reviews/'.$filename3;
        }
        
         if(isset($request->image[3])){
            
         $file4 = $request->image[3];
         $filename4 = time().'4.'.$file4->getClientOriginalExtension();
         $file4->move('public/images/reviews',$filename4);
         $review->img4    = '/public/images/reviews/'.$filename4;
        }
       
       $review->save();
       
        }
            

        $data1['status']  = true;
        $data1['message'] = "Review has been submitted successfully.";
        return response()->json($data1);



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function remove_image(Request $request)
    {
        
       if(empty($request->token))   return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
       if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);
       
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
