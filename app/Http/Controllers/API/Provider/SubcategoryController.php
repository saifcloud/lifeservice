<?php

namespace App\Http\Controllers\API\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subsubcategory;
use App\Models\Subcategory;
use App\Models\Category;
use App\Models\User;
use App\Models\Auth_token;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
     $category = Subcategory::where('category_id')
                                                 ->where('status',1)
                                                 ->where('is_deleted',0)
                                                 ->get();

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    // get vendor subcategory
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
        
        
           

        $rawdata =[];
        foreach ($user->vendor_subcategory as $key => $value) {
 
            $rawdata[] =  [
                'id' =>$value->subcategory->id,
                'image' => $value->subcategory->category->image,
                'en_subcategory' => $value->subcategory->en_subcategory,
                'ar_subcategory' => $value->subcategory->ar_subcategory,
                'sub_subcategory'=> $value->sub_subcategory
            ];
        }
        
       $data['status'] = true;
       $data['data'] = ['category'=>$rawdata];
       $data['message'] = "Vendor subcategory tree.";
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
