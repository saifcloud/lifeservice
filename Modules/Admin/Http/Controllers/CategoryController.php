<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $page_title = "Category";
        $category = Category::where('is_deleted',0)->latest()->get();
        return view('admin::category.index',compact('page_title','category'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request)
    {
        $page_title = "Category create";
        
        if($request->submit){
            // echo "<pre>";
            // print_r($request->all()); die;
             $request->validate([
                'en_category'  =>'required',
                'ar_category'  =>'required', 
                'kur_category' =>'required',
            ]);
            
            $category = new Category;
            if(!empty($request->image)){
                $file = $request->image;
                $filename = time().'.'.$file->getClientOriginalExtension();
                $file->move('public/images/category',$filename);
                $category->image            = '/category/'.$filename;
            }
            $category->en_category      = $request->en_category;
            $category->ar_category      = $request->ar_category;
            $category->kur_category     = $request->kur_category;
            $category->status           = 1;
            $category->save();
            return  redirect('admin/category')->with('success','Category added successfully.');
        
        }
        return view('admin::category.create',compact('page_title'));
    }

   

  
    public function edit(Request $request,$id)
    {
        $page_title = "Category edit";
        $category = Category::find(base64_decode($id));
        
         if($request->submit){
            
            
             $request->validate([
                'en_category'  =>'required',
                'ar_category'  =>'required', 
                'kur_category' =>'required',
            ]);
            
            if($request->image){
            $file = $request->image;
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move('public/images/category',$filename);
            $vehicle->image            = '/category/'.$filename;
            }
            $category->en_category      = $request->en_category;
            $category->ar_category      = $request->ar_category;
            $category->kur_category     = $request->kur_category;
            $category->save();
            return  redirect('admin/category')->with('success','Category added successfully.');
        
        }
        return view('admin::category.create',compact('page_title','category'));
    }



    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
         $subcategory = Category::find(base64_decode($id));
         $subcategory->is_deleted = 1;
         $subcategory->save();

         return back()->with('success','Category deleted successfully.');
    }
}
