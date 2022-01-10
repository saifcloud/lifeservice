<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Category;
use App\Models\Subcategory;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $page_title = "Subcategory";
        $subcategory = Subcategory::where('status',1)->where('is_deleted',0)->latest()->get();
        return view('admin::subcategory.index',compact('page_title','subcategory'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request)
    {
        $page_title = "Subcategory create";
        $category = Category::where('status',1)->where('is_deleted',0)->get();
        if($request->submit){
            // echo "<pre>";
            //print_r($request->all()); die;
             $request->validate([
                'category'        =>'required',
                'en_subcategory'  =>'required',
                'ar_subcategory'  =>'required', 
                'kur_subcategory' =>'required',
            ]);
            
            $subcategory = new Subcategory;
            if(!empty($request->image)){
                $file = $request->image;
                $filename = time().'.'.$file->getClientOriginalExtension();
                $file->move('public/images/subcategory',$filename);
                $category->image            = '/subcategory/'.$filename;
          
            }
            $subcategory->category_id         = $request->category;
            $subcategory->en_subcategory      = $request->en_subcategory;
            $subcategory->ar_subcategory      = $request->ar_subcategory;
            $subcategory->kur_subcategory     = $request->kur_subcategory;
            $subcategory->status              = 1;
            $subcategory->save();
            return  redirect('admin/subcategory')->with('success','Subcategory added successfully.');
        
        }
        return view('admin::subcategory.create',compact('page_title','category'));
    }

   

  
    public function edit(Request $request,$id)
    {
        $page_title = "Subcategory edit";
        $category = Category::where('status',1)->where('is_deleted',0)->get();
        $subcategory = Subcategory::find(base64_decode($id));
        
         if($request->submit){    
             $request->validate([
                'category'        =>'required',
                'en_subcategory'  =>'required',
                'ar_subcategory'  =>'required', 
                'kur_subcategory' =>'required',
            ]);
            
            if($request->image){
            $file = $request->image;
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move('public/images/subcategory',$filename);
            $subcategory->image            = '/subcategory/'.$filename;
            }
            $subcategory->category_id         = $request->category;
            $subcategory->en_subcategory      = $request->en_subcategory;
            $subcategory->ar_subcategory      = $request->ar_subcategory;
            $subcategory->kur_subcategory     = $request->kur_subcategory;
            $subcategory->save();
            return  redirect('admin/subcategory')->with('success','Subcategory added successfully.');
        
        }
        return view('admin::subcategory.create',compact('page_title','category','subcategory'));
    }



    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
         $subcategory = Subcategory::find(base64_decode($id));
         $subcategory->is_deleted = 1;
         $subcategory->save();

         return back()->with('success','Subcategory deleted successfully.');
    }
}
