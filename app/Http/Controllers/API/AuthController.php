<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Hash;
use Str;
use Auth;

use App\Models\User;
use App\Models\Vendor_subcategory;
use App\Models\Auth_token;
use App\Models\Document_option;
use App\Models\Driver_document;
use App\Models\Driver;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
       // $dataArray =[
       //      'userid'=>'p_smartvisitclc',
       //      'PW'=>'Practice00!',
       //      'hdnBusiness'=>'3004413307777777',
       //      'apiLogin'=>true,
       //      'target'=>'jsp/lab/person/PersonDemographics.jsp',
       //      'actionCommand'=>'loadPMSData',
       //      'P_ACT'=>'877447474',
       //      'P_LNM'=>'DOE',
       //      'P_FNM'=>'JOHN',
       //      'P_DOB'=>'01/01/1996',
       //      'P_SEX'=>'M'
       // ];

        $ch = curl_init();
        // $data = http_build_query($dataArray);
        $getUrl = "https://cli-cert.changehealthcare.com/servlet/DxLogin?userid=p_smartvisitclc&PW=Practice00!&hdnBusiness=3004413307&apiLogin=true&target=jsp/lab/person/PersonDemographics.jsp&actionCommand=loadPMSData&P_ACT=4500000&P_LNM=Rao&P_FNM=saif&P_DOB=03/10/1980&P_SEX=M";
        // return $getUrl;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);

        $response = curl_exec($ch);

        if(curl_error($ch)){
            echo 'Request Error:' . curl_error($ch);
        }
        else
        {
            echo $response;
        }

        curl_close($ch);


      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create(Request $request)
    // {
    //     //
    //       if(empty($request->user_id)) return response()->json(['status'=>false,'message'=>'User id is required.']);
    //       $user =  User::find($request->user_id);
    //       $user->status=1; 
    //       $user->save();
          
    //       return response()->json(['status'=>true,'data'=>['user_id'=>$user->id,'token'=>$user->auth_token,'role'=>$user->role],'message'=>'Verification successfully.']);
        
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        
        // return Hash::make($request->password);
        if(empty($request->email)) 
            return response()->json(['status'=>false,'message'=>'Email is required.']);

        if(empty($request->password)) 
            return response()->json(['status'=>false,'message'=>'Password is required.']);
         
         
        if(empty($request->device_token)) 
            return response()->json(['status'=>false,'message'=>'Device token is required.']);

        if(empty($request->fcm_token)) 
            return response()->json(['status'=>false,'message'=>'Fcm token is required.']);
        
        
        $credentials = request(['email', 'password']);

         if(!Auth::attempt($credentials))
            return response()->json(['status'=>false,'message'=>'Please check your email or password.']);
         
          $user = $request->user();

            
           
         
            
            $data['status'] = true;
            $data['data'] = ['token'=>$user->createToken('MyApp')->accessToken,'role'=>$user->role];
            $data['message'] ='Login successfully.';

           return response()->json($data);
        //  }
        //  return response()->json(['status'=>false,'message'=>'Check your email or password.']);
            
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function upload_document(Request $request)
    {
        if(empty($request->driver_id)){
          echo "unauthorize access";   
          die;
        }
        
        $check_driver = Driver::find($request->driver_id);
        if(empty($check_driver)){
            echo "unauthorize access";
            die;
        }
        
        
        $get_document_options = Document_option::where('status',1)->where('is_deleted',0)->get();
        if($request->submit){
            // echo "<pre>";
            // print_r($request->except(['submit','_token']));
            // die;
            
            foreach($request->except(['submit','_token','driver_id']) as $key =>$row){
                
                $file = $row;
                $filename = time().$key.'.'.$file->getClientOriginalExtension();
                $file->move("backendApi/public/driver_documents",$filename);
            
                $driver_document = new Driver_document;
                $driver_document->driver_id = $check_driver->id;
                $driver_document->document_type_id = $key;
                $driver_document->document         = '/driver_documents/'.$filename;
                $driver_document->save();
                
            }
            return redirect("webview/document-pending?driver_id=".$request->driver_id);
            
           
        }
     return view("webview.upload_document",compact('get_document_options'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function document_pending(Request $request)
    {
          $document_status = 0;
          $driver_document = Driver_document::where("driver_id",$request->driver_id)->where("status","Rejected")->get();
          if(isset($driver_document) && count($driver_document) > 0){
            $document_status = 0;
          }else{
            $document_status = 1;
          }
         return view("webview.pending",compact("document_status"));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rejected_document(Request $request)
    {
        //
        
        if(empty($request->driver_id)){
          echo "unauthorize access";   
          die;
        }
        
        $check_driver = Driver::find($request->driver_id);
        if(empty($check_driver)){
            echo "unauthorize access";
            die;
        }
        
        $driver_rejected_document = Driver_document::where("driver_id",$request->driver_id)->where("status","Rejected")->pluck('document_type_id');
        $get_document_options = Document_option::whereIn('id',$driver_rejected_document)->where('status',1)->where('is_deleted',0)->get();
        
        
      
        
        // $get_document_options = Document_option::where('status',1)->where('is_deleted',0)->get();
        if($request->submit){
            // echo "<pre>";
            // print_r($request->except(['submit','_token']));
            // die;
            Driver_document::where("driver_id",$request->driver_id)->where("status","Rejected")->update(['is_deleted'=>1]);
            foreach($request->except(['submit','_token','driver_id']) as $key =>$row){
                
                $file = $row;
                $filename = time().$key.'.'.$file->getClientOriginalExtension();
                $file->move("backendApi/public/driver_documents",$filename);
            
                $driver_document = new Driver_document;
                $driver_document->driver_id = $check_driver->id;
                $driver_document->document_type_id = $key;
                $driver_document->document         = '/driver_documents/'.$filename;
                $driver_document->save();
                
            }
            return redirect("webview/document-pending?driver_id=".$request->driver_id);
            
           
        }
        
        
        return view("webview.rejected_document",compact('get_document_options'));
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
