<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Hash;
use Str;
use Auth;
use Validator;
use Exception;

use App\Models\User;
use App\Models\Auth_token;
use App\Models\Subcategory;



class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function databaseclear(){
        Subcategory::truncate();
        return "ok";
    }
    
    public function index(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                'country_code'=>'required|numeric',
                'phone'=>'required|numeric',
                'role' =>'required|in:user,provider',
                'language' =>'required|in:en,ar,kur'
            ]);
            
            if($validator->fails()){
                return response()->json(['status'=>false,'message'=>$validator->errors()->first()]);
            }
            $user = User::where('country_code',$request->country_code)->where('phone',$request->phone)->first();
            if(empty($user)){
                $user = new User;
                $user->country_code = $request->country_code;
                $user->phone        = $request->phone;
                $user->role         = $request->role;
                $user->language     = $request->language;
                $user->save();
            }
            return response()->json(['status'=>true,'message'=>'OTP has sent to your number']);
        }catch(Exception $e){
           return response()->json(['status'=>false,'message'=>$e->getMessage()]);
        }
       


      
    }

  
    public function destroy($id)
    {
        //
    }
}
