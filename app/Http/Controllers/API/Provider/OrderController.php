<?php

namespace App\Http\Controllers\API\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Order_details;
use App\Models\User;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Notification;
use App\Models\Auth_token;

use Carbon\Carbon;
use DB;

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
          //
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);

        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);

        // $user = User::where('id',$request->user_id)
        //                                 ->where('is_deleted',0)
        //                                 ->where('role',2)
        //                                 ->where('auth_token',$request->token)
        //                                 ->first();
                                        
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
        
        
        


        $order_list = [];
        $order =  Order::where('vendor_id',$user->id)->where('is_deleted',0)->where('delivery_status',0);

        if($request->filter_type=="CURRENT"){
           $order = $order->where('status',1);
        }
        if($request->filter_type=="COMPLETED"){
           $order = $order->where('status',2);
        }
        if($request->filter_type=="CANCELED"){
           $order = $order->where('status',3);
        }
         $order = $order->get();
        
        

        if(count($order)==0) return response()
                                             ->json(['status'=>false,'message'=>'No any order.']);

        foreach ($order as $key => $value) {
            
             $items = [];
             foreach ($value->order_details as $key1 => $value1) {
                $items[] = [
                           'product_id'=>$value1->product->id,
                           'image'=>$value1->product->img1,
                           'title'=>$value1->product->title,
                           'qty'=>$value1->qty,
                           'color'=>($value1->color) ? $value1->color->name:'',
                           'size'=>($value1->size) ? $value1->size->name:'',
                           'amount'=>round($value1->amount *$value1->qty,2),
                           'time'=>Carbon::parse($value->created_at)->diffForHumans(),
                ];
             }



            if($value->status==1){
                $orderst = "CURRENT";
            }
            if($value->status==2){
                $orderst = "COMPLETED";
            }
            if($value->status==3){
                $orderst = "CANCELED";
            }



             $order_list[] = [
                 
                        'shopper'  =>$value->user->name,
                        'order_id' =>$value->order_id,
                        'items'    =>$items,
                        'location' =>$value->shipping_address,
                        'lat'      =>$value->lat,
                        'long'     =>$value->long,
                        'subtotal' =>round($value->total,2),
                        'shopping_fee'=> 2,
                        'VAT'      => 3,
                        'total'    =>round($value->total,2),
                        'status'   =>$orderst
                       
                      
                        
             ];
             
        }
        
        $data['status']  = true;
        $data['data']    = ['orders'=>$order_list];
        $data['message'] = "Order lists";
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
        //

        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);

        // $user = User::where('id',$request->user_id)
        //                                 ->where('is_deleted',0)
        //                                 ->where('role',2)
        //                                 ->where('auth_token',$request->token)
        //                                 ->first();
                                        
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
        
        

        if(empty($request->order_id)) return response()->json(['status'=>false,'message'=>'Order id is required.']);

        if(empty($request->status)) return response()->json(['status'=>false,'message'=>'Status is required.']);

        $order =  Order::where('order_id',$request->order_id)
                                                    ->where('vendor_id',$user->id)
                                                    ->where('status',1)
                                                    ->where('is_deleted',0)
                                                    ->where('delivery_status',0)
                                                    ->first();
                                                    
        

        if(empty($order)) return response()->json(['status'=>false,'message'=>'Order not found check order id.']);
        
        $msg ='';
        $title ="";
        
        if($request->status=="CURRENT"){
           $order->status = 1;
           
           $title ="";
           $msg="";
           
           Order_details::where('order_id',$request->order_id)
                                                    ->where('vendor_id',$user->id)
                                                    ->update(['status'=>1]);
                                                    
        }
        if($request->status=="COMPLETED"){
           $order->status = 2;
           
           $title ="ORDER DELIVERD";
           $msg="Your order has been deliverd ";
           
            Order_details::where('order_id',$request->order_id)
                                                    ->where('vendor_id',$user->id)
                                                    ->update(['status'=>2]);
        }
        if($request->status=="CANCELED"){
           $order->status = 3;
           
           $title ="ORDER CANCELED";
           $msg="Your order has been canceled";
           
           
            Order_details::where('order_id',$request->order_id)
                                                    ->where('vendor_id',$user->id)
                                                    ->update(['status'=>3]);
        }
        
        
        
        
        if($user->notification==1){
            
        //  $check_userd = User::find($order->user_id);
         
         $userd = Auth_token::where('user_id',$order->user_id)->where('fcm_token','!=','')->get();
         
          $notification = new Notification();   
          $notification->user_id   = $order->user_id;
          $notification->vendor_id = $user->id;
          $notification->title     = $title;
          $notification->body      = $msg;
          $notification->type      = "ORDER";
          $notification->n_type    = 1;
          $notification->save();
          
          
         foreach($userd as $row1){
         $json_data =[
                    "to" => $row1->fcm_token,
                    "notification" => [
                        "body" => $msg,
                        "title" =>$title,
                        "type"=>"ORDER"
                        // "icon" => "ic_launcher"
                    ]
                ];
                
        
        //   $notification = new Notification();   
        //   $notification->user_id   = $userd->id;
        //   $notification->vendor_id = $user->id;
        //   $notification->title     = $title;
        //   $notification->body      = $msg;
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
        
        
        
        
        
        if($order->save()){
            $data1['status']  = true;
            $data1['message'] = "Status has been changed";
        
        }else{
            $data1['status']  = false;
            $data1['message'] = "Status cannot changed please check, status key"; 
        }
        return response()->json($data1); 

       



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function sale(Request $request)
    // {
    //     //

    //     if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
    //     if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);

    //     $user = User::where('id',$request->user_id)
    //                                     ->where('is_deleted',0)
    //                                     ->where('role',2)
    //                                     ->where('auth_token',$request->token)
    //                                     ->first();
                                        
    //     if(empty($user)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);
       
        

    //     $saleTotal=  Order::where('vendor_id',$user->id)
    //                                                 ->where('status','!=',3)
    //                                                 ->where('is_deleted',0);

    //     // if($request->filter_type=="DAY"){
    //     //   $saleTotal = $saleTotal->select(DB::raw("total as amount"),DB::raw("DATE_FORMAT(created_at,'%h:%i %p') as name"))->whereDate('created_at',Carbon::today());
    //     // }

    //     if($request->filter_type =="WEEK"){

    //       $saleTotal = $saleTotal->select(DB::raw("(SUM(total)) as amount"),DB::raw("DAYNAME(created_at) as name"))->whereBetween('created_at',
    //         [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]
    //       )->groupBy('name');

    //       $stotal=  Order::where('vendor_id',$user->id)->whereBetween('created_at',
    //                                                     [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]
    //                                                   )
    //                                                 ->where('status','!=',3)
    //                                                 ->where('is_deleted',0)
    //                                                 ->sum('total');


    //     }else if($request->filter_type=="MONTH"){


    //       $saleTotal = $saleTotal->select(DB::raw("(SUM(total)) as amount"),DB::raw("DATE_FORMAT(created_at,'%d-%b-%Y') as name"))
    //       ->whereMonth('created_at',date('m'))
    //       ->whereYear('created_at',date('Y'))
    //       ->groupBy('name');


    //       $stotal=  Order::where('vendor_id',$user->id)->whereMonth('created_at',date('m'))
    //                                                 ->whereYear('created_at',date('Y'))
    //                                                 ->where('status','!=',3)
    //                                                 ->where('is_deleted',0)
    //                                                 ->sum('total');



    //     }else if($request->filter_type=="YEAR"){

    //       $saleTotal = $saleTotal->select(DB::raw("(SUM(total)) as amount"),DB::raw("MONTHNAME(created_at) as name"))
    //       ->whereYear('created_at', date('Y'))
    //       ->groupBy('name');

    //       $stotal=  Order::where('vendor_id',$user->id)->whereYear('created_at',date('Y'))
    //                                                 ->where('status','!=',3)
    //                                                 ->where('is_deleted',0)
    //                                                 ->sum('total');


    //     }else{
           
    //         $saleTotal = $saleTotal->select(DB::raw("total as amount"),DB::raw("DATE_FORMAT(created_at,'%h:%i %p') as name"))->whereDate('created_at',Carbon::today());

    //         $stotal=  Order::where('vendor_id',$user->id)->whereDate('created_at',Carbon::today())
    //                                                 ->where('status','!=',3)
    //                                                 ->where('is_deleted',0)
    //                                                 ->sum('total');
    //     }

    //      $total_sale_data = $saleTotal->get();

    //      $total_sale = ['total'=>$stotal,'graph_data'=>$total_sale_data];

       




    //     $productRaw = Product::where('vendor_id',$user->id)->where('status',1)->where('is_deleted',0)->get();
    //     $total_amount =['total'=>$stotal];


    //     $data['status']  = true;
    //     $data['data']    = [
    //         'total_sale'=>$total_sale,
    //         'total_amount'=>$total_amount
    //     ];
    //     $data['message'] = "Sale"; 
        
    //     return response()->json($data); 


    // }





    
    
     public function sale(Request $request)
    {
        //

        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);

        // $user = User::where('id',$request->user_id)
        //                                 ->where('is_deleted',0)
        //                                 ->where('role',2)
        //                                 ->where('auth_token',$request->token)
        //                                 ->first();
                                        
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
        
        
       
        

        $saleTotal=  Order::where('vendor_id',$user->id)
                                                    ->where('status','!=',3)
                                                    ->where('is_deleted',0);

        // if($request->filter_type=="DAY"){
        //   $saleTotal = $saleTotal->select(DB::raw("total as amount"),DB::raw("DATE_FORMAT(created_at,'%h:%i %p') as name"))->whereDate('created_at',Carbon::today());
        // }

        if($request->filter_type =="WEEK"){

          $saleTotal = $saleTotal->select(DB::raw("(SUM(total_product)) as product"),DB::raw("DAYNAME(created_at) as name"))->whereBetween('created_at',
            [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]
          )->groupBy('name');

          $stotal=  Order::where('vendor_id',$user->id)->whereBetween('created_at',
                                                        [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]
                                                      )
                                                    ->where('status','!=',3)
                                                    ->where('is_deleted',0)
                                                    ->sum('total_product');


        }else if($request->filter_type=="MONTH"){


          $saleTotal = $saleTotal->select(DB::raw("(COUNT(total_product)) as product"),DB::raw("DATE_FORMAT(created_at,'%d-%b-%Y') as name"))
          ->whereMonth('created_at',date('m'))
          ->whereYear('created_at',date('Y'))
          ->groupBy('name');


          $stotal=  Order::where('vendor_id',$user->id)->whereMonth('created_at',date('m'))
                                                    ->whereYear('created_at',date('Y'))
                                                    ->where('status','!=',3)
                                                    ->where('is_deleted',0)
                                                    ->sum('total_product');



        }else if($request->filter_type=="YEAR"){

          $saleTotal = $saleTotal->select(DB::raw("(COUNT(total_product)) as product"),DB::raw("MONTHNAME(created_at) as name"))
          ->whereYear('created_at', date('Y'))
          ->groupBy('name');

          $stotal=  Order::where('vendor_id',$user->id)->whereYear('created_at',date('Y'))
                                                    ->where('status','!=',3)
                                                    ->where('is_deleted',0)
                                                    ->sum('total_product');


        }else{
           
            $saleTotal = $saleTotal->select(DB::raw("total_product as product"),DB::raw("DATE_FORMAT(created_at,'%h:%i %p') as name"))->whereDate('created_at',Carbon::today());

            $stotal=  Order::where('vendor_id',$user->id)->whereDate('created_at',Carbon::today())
                                                    ->where('status','!=',3)
                                                    ->where('is_deleted',0)
                                                    ->sum('total_product');
        }

         $total_sale_data = $saleTotal->get();

        $total_sale = ['total'=>$stotal,'graph_data'=>$total_sale_data];

       
       
       
       
       
       
       
       
       



          //total amount   
           $ta_orders =  Order_details::where('vendor_id',$user->id)->where('status','!=',3)->where('is_deleted',0);
        
        
        
         if($request->ta_filter_type =="WEEK"){

          $ta_orders = $ta_orders->select('*',DB::raw("DAYNAME(created_at) as name"))->whereBetween('created_at',
            [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]
          );

        }
        
        else if($request->ta_filter_type=="MONTH"){


          $ta_orders = $ta_orders->select('*',DB::raw("DATE_FORMAT(created_at,'%d-%b-%Y') as name"))
          ->whereMonth('created_at',date('m'))
          ->whereYear('created_at',date('Y'));
        //   ->groupBy('name');


        }else if($request->ta_filter_type=="YEAR"){

          $ta_orders = $ta_orders->select('*',DB::raw("MONTHNAME(created_at) as name"))
          ->whereYear('created_at', date('Y'));
        //   ->groupBy('name');


        }else{
           
            $ta_orders = $ta_orders->select('*',DB::raw("DATE_FORMAT(created_at,'%h:%i %p') as name"))->whereDate('created_at',Carbon::today());
         
        }

        $total_sale_data1 = $ta_orders->get();
        
        $result = array();
        foreach ($total_sale_data1 as $element) {
            $result[$element['subcategory_id']][] = ['id'=>$element->id,'amount'=>$element->amount*$element->qty,'subcategory_id'=>$element->subcategory_id,'filter_type'=>$element->name];
        }

     
        
        $arr_data =[];
        $tot_amountt =[];
        foreach($result as $key=> $row){
            $amountt = [];
           
            
            foreach($row as $row1){
                $amountt[] = $row1['amount'];
                $tot_amountt[] =$row1['amount'];
            }
            $subcat = Subcategory::find($key);
            $arr_data[] = [
            'ar_subcategory'=>($subcat) ? $subcat->ar_subcategory:'',
            'en_subcategory'=>($subcat) ? $subcat->en_subcategory:'',
            'amount'=>round(array_sum($amountt),2)
            ];
                        
          
        }
        
        // return array_sum($tot_amountt); die;
        $total_amount =['total'=>round(array_sum($tot_amountt),2),'graph_data'=>$arr_data];

        //total order
        $totalOrder = Order::where('vendor_id',$user->id)->count();
        $totalClose = Order::where('vendor_id',$user->id)->where('status',2)->count();
        $totalCanceled = Order::where('vendor_id',$user->id)->where('status',3)->count();
        
        $data['status']  = true;
        $data['data']    = [
            'total_sale'=>$total_sale,
            'total_amount'=>$total_amount,
            'total_orders'=>$totalOrder,
            'total_completed'=>$totalClose,
            'total_canceled'=>$totalCanceled
        ];
        $data['message'] = "Sale"; 
        
        return response()->json($data); 


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
