<?php

namespace App\Http\Controllers\API\Shopper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Order_details;

use App\Models\Retweet;
use App\Models\Notification;
use App\Models\Auth_token;

use Carbon\Carbon;
use Hash;    

class ProfileController extends Controller
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
        
        
        
         
         $basicdetails = [
         'id'=>$user->id,
         'image'=>$user->image,
         'name'=>$user->name,
         'email'=>$user->email,
         'phone'=>$user->phone,
         'bio'=>($user->bio) ? $user->bio:'',
         'followers'=>$user->follower->count(),
         'following'=>$user->following->count()
         ];
         
         $productRaw = [];
         
         foreach($user->user_reviews as $value){
             
             
            $rawSize  =[];
            foreach ($value->product->product_size as $key1 => $value1) {
                
               
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
               
               
               
           // }

            
             //color
            $rawColor = [];
            foreach ($value->product->product_color as $key2 => $value2) {

              $fav = Like::where('product_id',$value->id)
                              ->where('user_id',$user->id)
                              ->first();


               $rawColor[] = [
                'id'  =>$value2->color->id,
                'name'=>$value2->color->name,
                'img1'=>($value2->img1) ? $value2->img1 :$value->product->img1,
                'img2'=>($value2->img2) ? $value2->img2:'',
                'img3'=>($value2->img3) ? $value2->img3:'',
                'img4'=>($value2->img4) ? $value2->img4:'',
               ]; 
            }


             //rewiew
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
                'id'=>$value2->user->id,
                'name'=>$value2->user->name,
                'comment'=>$value2->comment,
                'review_images'=>$reviewImage,
                'date'=>Carbon::parse($value2->created_at)->format('d F Y ')
               ];
            }
            
            
            $checkable = Order_details::where('user_id',$user->id)->where('product_id',$value->product->id)->first();
            $checkfollow = Follow::where('user_id',$value->product->vendor_id)->where('follower_id',$user->id)->first();

            $productRaw[] = [
                'vendor_name'       =>$value->product->user->name,
                'vendor_image'      =>$value->product->user->image,
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
                'like_count'        =>$value->product->like->count(),
                'comments_count'    =>count($rawRewiew),
                'review_status'     =>($checkable) ? 1:0,
                'follows_status'    =>($checkfollow) ? 1:0,
                'liked'             =>(!empty($fav) ? 1:0),
                'reviews'           =>$rawRewiew,
                 'web_url'=>'http://cuma.co/'

            ];
            
            
         }
         
         
         
      
         
        $rtw_product = Retweet::where('user_id',$user->id)->where('is_deleted',0)->get();
         
         
        $RproductRaw = [];    
        foreach ($rtw_product as $key => $value) {

            $rrawSize  =[];
            foreach ($value->product->product_size as $key1 => $value1) {
                
                
                
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
               
               
               $rrawSize[] = [
                'id'=>$value1->size->id,
                'name'=>$value1->size->name,
                'size_color'=>$cofsize
               ];
            }
            
            
            

            
             //color
            $rrawColor = [];
            
            foreach ($value->product->product_color as $key2 => $value2) {

              $rfav = Like::where('product_id',$value->id)
                              ->where('user_id',$user->id)
                              ->first();


               $rrawColor[] = [
                'id'=>$value2->color->id,
                'name'=>$value2->color->name,
                'img1'=>($value2->img1) ? $value2->img1 :$value->img1,
                'img2'=>($value2->img2) ? $value2->img2:'',
                'img3'=>($value2->img3) ? $value2->img3:'',
                'img4'=>($value2->img4) ? $value2->img4:'',
               ]; 
            }

          
          
          
             //rewiew
            $rrawRewiew  =[];
            foreach ($value->product->review as $key2 => $value2) {
                
                
                        
            $rreviewImage =[];
                
                if(!empty($value2->img1)){
                      $rreviewImage[0] = $value2->img1;
                }
                 if(!empty($value2->img2)){
                      $rreviewImage[1] = $value2->img2;
                }
                 if(!empty($value2->img3)){
                      $rreviewImage[2] = $value2->img3;
                }
                 if(!empty($value2->img4)){
                      $rreviewImage[3] = $value2->img4;
                }
                
                
               $rrawRewiew[] = [
                'id'=>$value2->user->id,
                'name'=>$value2->user->name,
                'comment'=>$value2->comment,
                'review_images' =>$rreviewImage,
                'date'=>Carbon::parse($value2->created_at)->format('d F Y ')
               ];
            }
            
            
            $rcheckable = Order_details::where('user_id',$user->id)->where('product_id',$value->product->id)->first();
            $rcheckfollow = Follow::where('user_id',$value->product->vendor_id)->where('follower_id',$user->id)->first();
            
            $RproductRaw[] = [
                'vendor_name'       =>$value->product->user->name,
                'vendor_image'      =>$value->product->user->image,
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
                'product_size'      =>$rrawSize,
                'product_color'     =>$rrawColor,
                'like_count'        =>$value->product->like->count(),
                'comments_count'    =>count($rrawRewiew),
                'liked'             =>(!empty($rfav) ? 1:0),
                'review_status'     =>($rcheckable) ? 1:0,
                'follows_status'    =>($rcheckfollow) ? 1:0,
                'reviews'           =>$rrawRewiew,
                'web_url'=>'http://cuma.co/'

            ];
        }
         
         
        // return count($RproductRaw);
         $data['status'] = true;
         $data['data'] = ['basic_details'=>$basicdetails,'repost'=>$RproductRaw,'user_review'=>$productRaw];
         $data['message'] = "User profile.";
         return response()->json($data);



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function follow(Request $request)
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
        
        

        if(empty($request->vendor_id)) return response()->json([
            'status'=>false,
            'message'=>'Vendor id is required.'
        ]);
        
         $followcheck =Follow::where('user_id',$request->vendor_id)
                                 ->where('follower_id',$user->id)
                                 ->first();
        if(!empty($followcheck)){
         $followcheck->delete();

         $data['status'] = true;
         $data['message'] = "Unfollowed successfully.";

        }else{
        $follow = new Follow;
        $follow->user_id     = $request->vendor_id;
        $follow->follower_id = $user->id;  
        $follow->save();

         $data['status'] = true;
         $data['message'] = "Followed successfully..";

        }

        return response()->json($data);
        



    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
        
        
        

        if(empty($request->name)) return response()->json(['status'=>false,'message'=>'Name is required.']);

         $user->name = $request->name;
         if($request->has('image')){
            $file = $request->image;
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move('public/images/user',$filename);
            $user->image               = '/public/images/user/'.$filename;
         }
          if($request->has('password')){
            $user->password = Hash::make($request->password);
          }
         $user->bio = $request->bio;
         $user->save();

        $data['status'] = true;
        $data['message'] = "Profile updated successfully.";
        return response()->json($data);
         


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     //

    // }

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
