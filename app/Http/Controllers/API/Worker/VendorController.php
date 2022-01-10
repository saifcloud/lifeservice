<?php

namespace App\Http\Controllers\API\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Subsubcategory;
use App\Models\Type;
use App\Models\User;
use App\Models\Size;
use App\Models\Color;
use App\Models\Product;
use App\Models\Slider_image;
use App\Models\Notification;
use Str;
use Carbon\Carbon;
use App\Models\Auth_token;



class VendorController extends Controller
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


    //   $user = User::where('id',$request->user_id)
    //   ->where('auth_token',$request->token)
    //   ->where('status',1)
    //   ->where('is_deleted',0)
    //   ->where('role',2)
    //   ->first();
       
    //   if(empty($user)) return response()->json(['status'=>false,'message'=>'User not registered.']);
    
    
    
     
         $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',2)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
        
        
       $basicinfo = [
        'id'=>$user->id,
        'image'=>$user->image,
        'name'=>$user->name,
        'en_category'=>$user->category->en_category,
        'ar_category'=>$user->category->ar_category,
        'bio'=>($user->bio) ? $user->bio:"",
        'followers'=>$user->follower->count(),
        'following'=>$user->following->count(),
        'rating'=>round($user->reviews->avg('rating'))
       ];
       
       $categoryRaw = [];
       foreach ($user->vendor_subcategory as $key => $value) {
           # code...
        $categoryRaw[] = [
            'id' =>$value->subcategory->id,
            'en_subcategory' =>$value->subcategory->en_subcategory,
            'ar_subcategory' =>$value->subcategory->ar_subcategory,
            'status' =>(isset($request->subcategory_id) && ($value->subcategory->id == $request->subcategory_id) ? 1:0),
            
        ];
       }
        
        // $subcategory_id = $user->vendor_subcategory->first()->subcategory->id;
        $productRawdata = Product::where('vendor_id',$user->id)
                                                        ->where('status',1)
                                                        ->where('is_deleted',0);
        if(!empty($request->subcategory_id)){
            $productRawdata = $productRawdata->where('subcategory_id',$request->subcategory_id);
        }
                                                        
        $product = $productRawdata->get(); 

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
                'img1'=>$value2->img1,
                'img2'=>$value2->img2,
                'img3'=>$value2->img3,
                'img4'=>$value2->img4,
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
                'product_color'     =>$rawColor
            ];
        }


       $data['status']  = true;
       $data['data']    = [
        'basicinfo'=>$basicinfo,
        'subcategory'=>$categoryRaw,
        'product'=>$productRaw];
       $data['message'] = 'Home page data.';
       return response()->json($data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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
        
        
        
        

         $size = Size::where('subsubcategory_id',$request->sub_subcategory_id)
        ->where('subcategory_id',$request->subcategory_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->get();

         $color = Color::where('subsubcategory_id',$request->sub_subcategory_id)
        ->where('subcategory_id',$request->subcategory_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->get();


       $data['status']  = true;
       $data['data']    = ['sizes'=>$size,'colors'=>$color];
       $data['message'] = 'Selected category and subcategory attributes.';
       return response()->json($data);



    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add_account(Request $request)
    {
        //
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User id is required.']);
        
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
          
        if(empty($request->name)) return response()->json(['status'=>false,'message'=>'Name is required.']);

        if(empty($request->device_token)) return response()->json(['status'=>false,'message'=>'Device token is required.']);

        if(empty($request->fcm_token)) return response()->json(['status'=>false,'message'=>'Fcm token is required.']);

        if(empty($request->category_id)) return response()->json(['status'=>false,'message'=>'Category is required.']);
           
      
      
        // $ckuser = User::where('id',$request->user_id)->where('auth_token',$request->token)->first();
        // if(empty($ckuser)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
        
        
         $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',2)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
        
        
        $user = new User();
        if($request->has('image')){
        $file = $request->image;
        $filename = time().'.'.$file->getClientOriginalExtension();
        $file->move('public/images/user',$filename);
        $user->image               = '/public/images/user/'.$filename;
        }
        $user->name                = $request->name;
        $user->email               = $ckuser->email;
        $user->password            = "no password";
        $user->country_code        = $ckuser->country_code;
        $user->phone               = $ckuser->phone;
        $user->commercial_reg_num  = $ckuser->commercial_reg_num;
        $user->category_id         = $request->category_id;
        $user->fcm_token           = $request->fcm_token;
        $user->device_token        = $request->device_token;
        $user->role                = $ckuser->role;
        $user->owner_id            = $ckuser->id;
        $user->auth_token          =  Str::random(36);
        $user->status              =  1;
        if($user->save()){
          

         if($request->role==2){
            
             foreach (explode(',',$request->subcategory) as  $value) {
                 # code...
                 $vdsc = new Vendor_subcategory;
                 $vdsc->vendor_id        = $user->id;
                 $vdsc->subcategory_id = $value;
                 $vdsc->category_id    = $request->category_id;
                 $vdsc->save();
             }
             
         }

         
         $data['status'] = true;
         $data['data'] = ['user_id'=>$user->id,'token'=>$user->auth_token,'role'=>$user->role]; 
         $data['message'] = 'Add added successfully.';
         return response()->json($data);
        }
        return response()->json(['status'=>false,'message'=>'Registration failed,try again.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function my_accounts(Request $request)
    {
        //
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User id is required.']);
        
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);




        // $ckuser = User::where('id',$request->user_id)->where('auth_token',$request->token)->where('is_deleted',0)->first();
        // if(empty($ckuser)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
        
        
         $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',2)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
        
        
        
        
        
        $accounts = User::where('owner_id',$ckuser->id)->where('status',1)->where('is_deleted',0)->get();
    
        $arrdata = [];
        
        if(count($accounts) > 0){
            
            foreach($accounts as $row){
               $arrdata[] = ['id'=>$row->id,'image'=>$row->image,'name'=>$row->name,'ar_category'=>$row->category->ar_category,'en_category'=>$row->category->en_category]; 
            }
            
        }else{
            
             $main = User::where('id',$ckuser->owner_id)->where('status',1)->where('is_deleted',0)->first();
             $arrdata[] = ['id'=>$main->id,'image'=>$main->image,'name'=>$main->name,'ar_category'=>$main->category->ar_category,'en_category'=>$main->category->en_category]; 
             
             $accounts = User::where('id','!=',$ckuser->id)->where('owner_id',$ckuser->owner_id)->where('status',1)->where('is_deleted',0)->get();
            
             foreach($accounts as $row){
             $arrdata[] = ['id'=>$row->id,'image'=>$row->image,'name'=>$row->name,'ar_category'=>$row->category->ar_category,'en_category'=>$row->category->en_category]; 
             }
        
            $acdata = $accounts;
        }
        
       
     
        
        
         $data['status'] = true;
         $data['data'] = $arrdata;
         $data['message'] = 'Account list.';
         return response()->json($data);
        
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function switch_to_account(Request $request)
    {
        //
        
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'Login by is required.']);
         
         
        if(empty($request->device_token)) return response()->json(['status'=>false,'message'=>'Device token is required.']);

        if(empty($request->fcm_token)) return response()->json(['status'=>false,'message'=>'Fcm token is required.']);
        
        
        
         $user = User::where('id',$request->user_id)->where('status',1)->where('is_deleted',0)->first();
        
         if(empty($user)) return response()->json(['status'=>false,'message'=>'Account not found.']); 
         
         
         
         

        //  if(Hash::check($request->password,$user->password)){
            
            $check_subaccounts = User::where('owner_id',$user->id)->where('status',1)->where('is_deleted',0)->get();
            if(count($check_subaccounts)> 0){
              User::where('owner_id',$user->id)->where('status',1)->where('is_deleted',0)->update(['device_token'=>0,'fcm_token'=>0]);
            }else{
              User::where('id',$user->owner_id)->where('status',1)->where('is_deleted',0)->update(['device_token'=>0,'fcm_token'=>0]);
              User::where('owner_id',$user->owner_id)->where('status',1)->where('is_deleted',0)->update(['device_token'=>0,'fcm_token'=>0]);
            }
           
            $generateToken = Str::random(36);
            $user->fcm_token           = $request->fcm_token;
            $user->device_token        = $request->device_token;
            $user->auth_token          = $generateToken;
            $user->save();
            
            
           $ckdevice = Auth_token::where('device_token',$request->device_token)->where('user_id',$user->id)->first();
            if(!empty($ckdevice)){
                $ckdevice->fcm_token = $request->fcm_token;
                $ckdevice->save();
            }else{
            
            $auth = new Auth_token;
            $auth->fcm_token           = $request->fcm_token;
            $auth->device_token        = $request->device_token;
            $auth->auth_token          = $tkon;
            $user->device_type         = ($request->device_type) ?  $request->device_type:'';
            $auth->user_id             = $user->id;
            $auth->save();
            
            }

            
            $data['status'] = true;
            $data['data'] = ['user_id'=>$user->id,'country_code'=>$user->country_code,'phone'=>$user->phone,'token'=>$user->auth_token,'role'=>$user->role];
            $data['message'] ='Switched to account successfully.';

           return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function upload_slider_images(Request $request)
    {
        //
        
        // print_r($request->all()); die;
        
     if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
     if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);



    //  $user = User::where('id',$request->user_id)->where('auth_token',$request->token)->where('is_deleted',0)->first();
    //  if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
    
    
    
        $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',2)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
     
     
     
     
    
      if(!$request->has('images')) return response()->json(['status'=>false,'message'=>'At least one image is required.']);
      
        foreach($request->images as $file){
            
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move('public/images/slider',$filename);
            
            $slider = new Slider_image;
            $slider->image     = '/public/images/slider/'.$filename;
            $slider->vendor_id = $user->id;
            $slider->save();
        
        }
        
          $data['status'] = true;
          $data['message'] ='Slider images uploaded successfully.';
          return response()->json($data);
        
        
    }
    
    public function get_slider_images(Request $request)
    {
        
     if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
     if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);

    //  $user = User::where('id',$request->user_id)->where('auth_token',$request->token)->where('is_deleted',0)->first();
    //  if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
    
    
       $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',2)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
     
      $data['status'] = true;
      $data['data'] =['slider'=>$user->slider_images];
      $data['message'] ='Slider images uploaded successfully.';
      return response()->json($data);
     
     
    }




     public function delete_slider_images(Request $request)
    {
        //
     if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
     if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);




    //  $user = User::where('id',$request->user_id)->where('auth_token',$request->token)->where('is_deleted',0)->first();
    //  if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
    
    
     $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',2)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
     
     
     
     
     
     $slider = Slider_image::find($request->slider_id);
     $slider->is_deleted = 1;
     $slider->save();
     
     $data['status'] = true;
     $data['message'] ='Slider images deleted successfully.';
        return response()->json($data);
     
     
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
     if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
     if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);

    //  $user = User::where('id',$request->user_id)->where('auth_token',$request->token)->where('is_deleted',0)->first();
    //  if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
    
     $user = User::where('id',$request->user_id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->where('role',2)
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
        ->where('role',2)
        // ->where('auth_token',$request->token)
        ->first();
       
        if(empty($user)) return response()->json(['status'=>false,'message'=>'User not found.']); 
         
        $ckdevice = Auth_token::where('auth_token',$request->token)->where('user_id',$user->id)->first();
      
        if(empty($ckdevice)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']); 
        
        
        
     
     $notificationRaw = Notification::where('n_type',2)->where('vendor_id',$user->id)->get();
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
