<?php

namespace App\Http\Controllers\API\Shopper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\Color;
use App\Models\Size;
use Carbon\Carbon;
use App\Models\Auth_token;


class CartController extends Controller
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
        
        
        


        $cartRaw = Cart::where('user_id',$user->id)->where('status',1)->where('is_deleted',0)->get();
        
        $cartRaw2 =[];
        $cart =[];

        if(count($cartRaw) > 0){
        foreach ($cartRaw as $key => $value) {
            # code...
            $cartRaw2[$value->vendor_id][] = [
                'cart_id'=>$value->id,
                'product_id'=>$value->product->id,
                'image'=>$value->product->img1,
                'product_title'=>$value->product->title,
                'price'=>round($value->product->price,2),
                'qty'=>$value->qty,
                'color'=>($value->color_id) ? $value->color_id:"",
                'size'=>($value->size_id) ? $value->size_id:""
            ]; 
        }



          // 
         
        foreach ($cartRaw2 as $key => $value) {
           
            $prod = [];
            $totalAmount = [];
            foreach ($value as $key1 => $value1) {
                # code...
               $size  =  Size::where('id',$value1['size'])->where('status',1)->where('is_deleted',0)->first();
               $color = Color::where('id',$value1['color'])->where('status',1)->where('is_deleted',0)->first();
                $prod[] = [
                    'cart_id'=>$value1['cart_id'],
                    'product_id'=>$value1['product_id'],
                    'image'=>$value1['image'],
                    'title'=>$value1['product_title'],
                    'price'=> round($value1['price'],2),
                    'qty'=>$value1['qty'],
                    'color_id'=>($value1['color']) ? $value1['color']:"",
                    'color_code'=>isset($color->name) ? $color->name:"",
                    'size_id'=>isset($value1['size']) ? $value1['size']:"",
                    'size'=>isset($size->name) ? $size->name:"",
                ];
                // echo $value1['price']; die;
                $totalAmount[] = $value1['price'] * $value1['qty'];


            }

            $vendor = User::find($key);
            $cart[] =  [
                'user_id' => $user->id,
                'shopper_name' => $user->name,
                'vendor_id' => $vendor->id,
                'name' => $vendor->name,
                'total_amount' =>round(array_sum($totalAmount),2),
                'products'=>$prod
            ];
        }

      }

        // return $cart;

        $tracking = [];
        $order =  Order::where('user_id',$user->id)
        ->where('status',1)
        ->where('is_deleted',0)
        ->get();
        
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
                ];
             }
             
             

             $tracking[] = [
                        'shoppper_name' =>$user->name,
                        'vendor_name' =>$value->vendor->name,
                        'order_id'=>$value->order_id,
                        'items'=> $items,
                        'subtotal'=> round($value->total,2),
                        'shopping_fee'=> 2,
                        'VAT'=> 3,
                        'total'=>round($value->total,2),
                        'order_successfully'=>Carbon::parse($value->created_at)->format('d-m-Y'),
                        'package_date'=>($value->package_date) ?Carbon::parse($value->package_date)->format('d-m-Y'):'',
                        'transporting_date'=>($value->transporting_date) ? Carbon::parse($value->transporting_date)->format('d-m-Y'):''
             ];
             
        }
       // print_r($tracking); die;

        $previous = [];
        $previousRaw =   Order::where('user_id',$user->id)->where('status',2)->where('is_deleted',0)->get();
        // ->where('delivery_status',1)
        

        foreach ($previousRaw as $key => $value) {
            
             $items1 = [];
             $totalAmount = [];
             foreach ($value->order_details as $key1 => $value1) {
                 $totalAmount[] = round($value1->amount * $value1->qty,2);
                $items1[] = [
                           'vendor_name'=>$value1->product->user->name,
                           'product_id'=>$value1->product->id,
                           'image'=>$value1->product->img1,
                           'title'=>$value1->product->title,
                           'qty'=>$value1->qty,
                           'color'=>($value1->color) ? $value1->color->name:'',
                           'size'=>($value1->size) ? $value1->size->name:'',
                           'amount'=>round($value1->amount * $value1->qty,2),
                ];
             }

             $previous[] = [
                        'user_id' => $user->id,
                        'shopper_name' => $user->name,
                        'vendor_id' => $value->vendor->id,
                        'vendor_name' => $value->vendor->name,
                        'order_id'=>$value->order_id,
                        'items'=> $items1,
                        'subtotal'=> round($value->total,2),
                        'shopping_fee'=> 2,
                        'VAT'=> 3,
                        'total'=>round($value->total,2)
             ];
             
        }

        $data['status'] = true;
        $data['data']   = [

            'current'=>$cart,
            'tracking'=>$tracking,
            'previous'=>$previous
        ];

        $data['message']= "Cart data";
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
        
        
        
        

        if(empty($request->product_id)) return response()->json(['status'=>false,'message'=>'Product id is required.']);

        // if(empty($request->color_id)) return response()->json(['status'=>false,'message'=>'Color id is required.']);

        // if(empty($request->size_id)) return response()->json(['status'=>false,'message'=>'SIze id is required.']);
       
        $product = Product::find($request->product_id);
        
        $checkcart = Cart::where('product_id',$product->id)
                                         ->where('user_id',$user->id)
                                         ->where('vendor_id',$product->vendor_id);
        if($request->color_id){
            $checkcart = $checkcart->where('color_id',$request->color_id);
        }
        if($request->color_id){
            $checkcart = $checkcart->where('size_id',$request->size_id);
        }
        
         $checkcart = $checkcart->where('is_deleted',0)->first();

        if(!empty($checkcart)){
             $checkcart->qty =  $checkcart->qty +1;
             $checkcart->save();
        }else{
            $cart = new Cart;
            $cart->product_id = $product->id;
            $cart->user_id    = $user->id;
            $cart->vendor_id  = $product->vendor_id;
            $cart->qty        = 1;
            $cart->color_id   = $request->color_id;
            $cart->size_id    = $request->size_id;
            $cart->save();  
        }


      

        $data['status'] = true;
        $data['message'] = "Product added in cart successfully.";
        return response()->json($data);



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function remove_product(Request $request)
    {
        //
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);
        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);


        // $user = User::where('id',$request->user_id)
        //                                     ->where('role',1)
        //                                     ->where('status',1)
        //                                     ->where('is_deleted',0)
        //                                     ->where('auth_token',$request->token)
        //                                     ->first();
        
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

        if(empty($request->cart_id)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);


        $cart = Cart::where('user_id',$user->id)
        ->where('id',$request->cart_id)
        ->where('is_deleted',0)
        ->delete();
       
        if(!empty($cart)){
            $data['status'] = true;
            $data['message'] = "Product removed from cart successfully.";
            
        }else{
            $data['status'] = false;
            $data['message'] = "Product not found in cart.";
        }
           
        return response()->json($data);


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function manager_qty(Request $request)
    {
        //
        if(empty($request->token)) return response()->json(['status'=>false,'message'=>'Authorization token is required.']);

        if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User is required.']);


        // $user = User::where('id',$request->user_id)
        //                                     ->where('role',1)
        //                                     ->where('status',1)
        //                                     ->where('is_deleted',0)
        //                                     ->where('auth_token',$request->token)
        //                                     ->first();
        
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
        
        

        if(empty($request->cart_id)) return response()->json(['status'=>false,'message'=>'Unauthorize user.']);



         $cart = Cart::where('user_id',$user->id)
        ->where('id',$request->cart_id)
        ->where('is_deleted',0)
        ->first();

        $cart->qty = $cart->qty-1;
        $cart->save();
       
        if(!empty($cart)){
            $data['status'] = true;
            $data['message'] = "Product quantity has decreased by 1.";
            
        }else{
            $data['status'] = false;
            $data['message'] = "Product not found in cart.";
        }
           
        return response()->json($data);
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
