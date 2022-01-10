<?php

namespace App\Http\Controllers\API\Shopper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\Product;
use App\Models\Tracking;
use App\Models\Notification;
use App\Models\Auth_token;

class OrderController extends Controller
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
        
        
        
        

        if(empty($request->location)) return response()->json(['status'=>false,'message'=>'location required.']);

        if(empty($request->lat)) return response()->json(['status'=>false,'message'=>'Latitude is required.']);


        if(empty($request->long)) return response()->json(['status'=>false,'message'=>'Longitude user.']);

        
        $cart = Cart::where('vendor_id',$request->vendor_id)
                                                    ->where('user_id',$user->id)
                                                    ->where('is_deleted',0)
                                                    ->get();
       
        if(count($cart)==0) return response()->json(['status'=>false,'message'=>'Please add some product in your cart.']);
        $order_id =  'ORDER'.$user->id.mt_rand();

        
         $tamount = [];
         $total_product = [];
         
        foreach ($cart as $key => $value) {

            $product = Product::find($value->product_id);

            $orderdetails = new Order_details;
            $orderdetails->order_id  = $order_id;
            $orderdetails->product_id= $value->product_id;
            $orderdetails->size_id   = $value->size_id;
            $orderdetails->color_id  = $value->color_id;
            $orderdetails->qty       = $value->qty;
            $orderdetails->amount    = $product->price;
            $orderdetails->subcategory_id = $product->subcategory_id;
            $orderdetails->user_id   = $value->user_id;
            $orderdetails->vendor_id = $value->vendor_id;
            $orderdetails->save();

            $tamount[] = $product->price * $value->qty;
            $total_product[] = $value->qty;

             Cart::where('vendor_id',$request->vendor_id)
                                                    ->where('user_id',$user->id)
                                                    ->where('product_id',$value->product_id)
                                                    ->where('is_deleted',0)
                                                    ->update(['is_deleted'=>1]);
        }

        $order = new Order;
        $order->order_id = $order_id;
        $order->user_id  = $user->id;
        $order->vendor_id = $request->vendor_id;
        $order->total    = array_sum($tamount);
        $order->total_product = array_sum($total_product);
        $order->shipping_address = $request->location;
        $order->lat      = $request->lat;
        $order->long     = $request->long;
        $order->save();
        


        // PUSH NOTIFICATION
        
        //  $vndr = User::find($request->vendor_id);
          $getvendorAuth = Auth_token::where('fcm_token','!=','')->where('user_id',$request->vendor_id)->get();
          
          
          $notification = new Notification();   
          $notification->user_id   = $user->id;
          $notification->vendor_id = $request->vendor_id;
          $notification->title     = "ORDER PLACED SUCCESSFULLY";
          $notification->body      = "test test";
          $notification->type      = "ORDER";
          $notification->n_type    = 2;
          $notification->save();
         
         
         foreach($getvendorAuth as $row){
          
         $json_data =[
                    "to" => $vndr->fcm_token,
                    "notification" => [
                        "body" => "test test",
                        "title" =>"ORDER PLACED SUCCESSFULLY",
                        "type"=>"ORDER"
                        // "icon" => "ic_launcher"
                    ]
                ];
          
          
        //   $notification = new Notification();   
        //   $notification->user_id   = $user->id;
        //   $notification->vendor_id = $vndr->id;
        //   $notification->title     = "ORDER PLACED SUCCESSFULLY";
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
         }
            
        
      
       

        $data1['status'] = true;
        $data1['data'] = ['order_id'=>$order_id];
        $data1['message'] = "Order placed successfully.";
        return response()->json($data1);

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
