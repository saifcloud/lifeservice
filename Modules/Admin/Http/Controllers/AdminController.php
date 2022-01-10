<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Auth;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Subcategory;

use Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
      
        $page_title     = "Dashboard";
        $category = Category::where('is_deleted',0)->count();
        $subcategory  = Subcategory::where('status',1)->where('is_deleted',0)->count();
        return view('admin::index',compact('page_title','category','subcategory'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function profile()
    {
        $page_title ="Dashboard";
        return view('admin::profile',compact('page_title'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function profile_post(Request $request)
    {
        //
        if($request->old_password || $request->new_password){
            $request->validate([
               'name'=>'required',
               'old_password'=>'required',
               'new_password'=>'required'
            ]);
            
            $admin = Admin::find(Auth::guard('admin')->id());
             
            if(Hash::check($request->old_password,$admin->password)){
            
                $admin->name     = $request->name;
                if($request->image){
                    $file  = $request->image;
                    $filename = time().'.'.$file->getClientOriginalExtension();
                    $file->move('public/vehicle',$filename);
                    $admin->image = $filename;
                }
                $admin->password = Hash::make($request->new_password);
                $admin->save();
            
                   return back()->with('success','Profile updated successfully.');
            }else{
                   return back()->with('old_password_error','Old password did not match')->withInput();
            }
        
        
        
        
        }else{
           $request->validate([
               'name'=>'required',
            ]); 
            
            
             $admin = Admin::find(Auth::guard('admin')->id());
             
           
            
                $admin->name     = $request->name;
                if($request->image){
                    $file  = $request->image;
                    $filename = time().'.'.$file->getClientOriginalExtension();
                    $file->move('public/images',$filename);
                    $admin->image = $filename;
                }
                $admin->save();
            
                   return back()->with('success','Profile updated successfully.');
           
            
        }
         
        
       
      
        
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function document_setting(Request $request)
    {
        $page_title = "document setting";
        $document_setting = Document_option::where('is_deleted',0)->get();
                    // print_r($request->all()); die;
        if($request->document_id){

           $getdocument =  Document_option::find($request->document_id);
           $getdocument->status = $request->document_status;
           $getdocument->save();
           return back()->with('success','Document has been updated successfully.');
        }
        return view('admin::document_setting',compact('page_title','document_setting'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function reject_document(Request $request)
    {
       
     
        
        foreach($request->documents as $row){
           $getdocument =  Driver_document::find($row);
           $getdocument->status = 'Rejected';
           $getdocument->save();
        }
          
      return response()->json(['status'=>true,'message'=>'Document has been updated successfully.']);
    
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function approved_document(Request $request)
    {
        
           $driver = Driver::find($request->driver_id);
           $driver->admin_approved ="Approved";
           $driver->save();
           
           $getdocument =  Driver_document::where("driver_id",$request->driver_id)->where("status","!=","Rejected")->update(["status"=>"Approved"]);
           return response()->json(['status'=>true,'message'=>'Document has been updated successfully.']);
    }
    
    
    
     public function action_on_driver(Request $request)
    {
        
           $driver = Driver::find($request->driver_id);
           $driver->is_blocked =$request->block_status;
           $driver->save();
           
           return back()->with('success','Profile updated successfully.');
            
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy()
    {
        //
        Auth::guard('admin')->logout();
        return redirect('admin');
    }
}
