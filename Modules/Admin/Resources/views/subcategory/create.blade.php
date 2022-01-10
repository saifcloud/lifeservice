@extends("admin::layouts.master")
@section("content")
<style>
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
}

input[type=number] {
  -moz-appearance: textfield;
}
</style>
<div class="wrapper">
@include("admin::partials.sidebar")
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       {{ isset($page_title) ? $page_title:''}}
      </h1>
      <ol class="breadcrumb">
         <li><a href="{{ url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="javascript:;">{{ isset($page_title) ? $page_title:''}}</a></li>
       
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <!--<h3 class="box-title">Quick Example</h3>-->
            </div>
            <!-- /.box-header -->
            <!-- form start -->
           
            <form role="form" method="post"  action="" enctype="multipart/form-data">
                @csrf
              <div class="box-body">
                
                <div class="row">

                <div class="form-group col-sm-4">
                      <label for="exampleInputEmail1">Category</label>
                      <select type="text" class="form-control" id="category"  name="category"  required="">
                      <option value="">select</option>
                      @if(isset($category) && count($category) > 0)
                      @foreach($category as $row)
                      <option {{ !empty($subcategory) && ($subcategory->category_id==$row->id) ? "selected":"" }} value="{{$row->id}}">{{$row->en_category}}</option>
                      @endforeach
                      @endif
                      </select>
                      <p class="text-danger">{{ $errors->first('category') }}</p>
                    </div>

                    <div class="form-group col-sm-4">
                      <label for="exampleInputEmail1">English name</label>
                      <input type="text" class="form-control" id="en_subcategory" placeholder="English subcategory" name="en_subcategory" value="{{ !empty($subcategory->en_subcategory) ? $subcategory->en_subcategory:"" }}" required="">
                      <p class="text-danger">{{ $errors->first('en_subcategory') }}</p>
                    </div>
                    
                     <div class="form-group col-sm-4">
                      <label for="exampleInputEmail1">Arabic name</label>
                      <input type="text" class="form-control" id="ar_subcategory" placeholder="Arabic subcategory" name="ar_subcategory" value="{{ !empty($subcategory->ar_subcategory) ? $subcategory->ar_subcategory:"" }}" required="">
                      <p class="text-danger">{{ $errors->first('ar_subcategory') }}</p>
                    </div>

                  
                </div>
                
                
              <div class="row">
              <div class="form-group col-sm-4">
                      <label for="exampleInputEmail1">Kurdish name</label>
                      <input type="text" class="form-control" id="kur_subcategory" placeholder="Kurdish subcategory" name="kur_subcategory" value="{{ !empty($subcategory->kur_subcategory) ? $subcategory->kur_subcategory:"" }}" required="">
                      <p class="text-danger">{{ $errors->first('kur_subcategory') }}</p>
                </div>
                
               <div class="row">
                <div class="form-group col-sm-6">
                  <label for="exampleInputFile">Image</label>
                  <input type="file" id="icon" name="image" require="">
                  <p class="text-danger">{{ $errors->first('image') }}</p>
                </div>
                @if(!empty($subcategory))
                <img src="{{ url($subcategory->image)}}" style="width:120px; height:120px;"/>
                @endif
                </div>
                
              </div>
              

              <div class="box-footer">
                <input type="submit" value="Submit" name="submit" class="btn btn-primary">
              </div>
            </form>
          </div>
        

        </div>
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
 <div class="control-sidebar-bg"></div>
</div>
@endsection
 
