<?php

namespace App\Http\Controllers\API\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Hash;

use App\Models\User;
use App\Models\Auth_token;

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
        
        



        
        $profile_details = [
        'image'=> $user->image,
        'name' => $user->name,
        'email'=> $user->email,
        'phone'=> $user->phone,
        'country_code'=>$user->country_code,
        'bio'  => $user->bio,
        'en_category'=> $user->category->en_category,
        'ar_category'=> $user->category->ar_category,
        'commercial_reg_num'=>$user->commercial_reg_num
        ];

        $data['status'] = true;
        $data['data']   = ['profile_details'=>$profile_details];
        $data['message']= "Profile details";
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
        
        


        if(empty($request->name)) return response()->json(['status'=>false,'message'=>'Name is required.']);
       
       // if(empty($request->password)) return response()->json(['status'=>false,'message'=>'Password is required.']);

      
       if(empty($request->commercial_reg_num)) return response()->json(['status'=>false,'message'=>'Commercial registration number is required.']);

       $user->name = $request->name;
       $user->commercial_reg_num = $request->commercial_reg_num;
       $user->bio = $request->bio;
       if(!empty($request->password)){
         $user->password = Hash::make($request->password);
       }
       if($request->has('image')){
        $file = $request->image;
        $filename = time().'.'.$file->getClientOriginalExtension();
        $file->move('public/images/user',$filename);
        $user->image = '/public/images/user/'.$filename;
       }
       $user->save();

       return response()->json(['status'=>true,'message'=>'Profile updated successfully.']);












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
