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
                    <div class="form-group col-sm-6">
                      <label for="exampleInputEmail1">English name</label>
                      <input type="text" class="form-control" id="en_category" placeholder="English name" name="en_category" value="{{ !empty($category->en_category) ? $category->en_category:"" }}" required="">
                      <p class="text-danger">{{ $errors->first('en_category') }}</p>
                    </div>
                    
                     <div class="form-group col-sm-6">
                      <label for="exampleInputEmail1">Arabic name</label>
                      <input type="text" class="form-control" id="ar_category" placeholder="Arabic name" name="ar_category" value="{{ !empty($category->ar_category) ? $category->ar_category:"" }}" required="">
                      <p class="text-danger">{{ $errors->first('ar_category') }}</p>
                    </div>
                </div>
                
                
                 <div class="row">
                  <div class="form-group col-sm-6">
                  <label for="exampleInputEmail1">Kurdish name</label>
                  <input type="text" class="form-control" id="kur_category" placeholder="Kurdish category" name="kur_category" value="{{ !empty($category->kur_category) ? $category->kur_category:"" }}" required="">
                  <p class="text-danger">{{ $errors->first('kur_category') }}</p>
                </div>
                
                
                 <!-- <div class="form-group col-sm-6">
                  <label for="exampleInputEmail1">Per Min</label>
                  <input type="number" class="form-control" id="per_min" placeholder="Per Min" name="per_min" value="{{ !empty($vehicle->per_min) ? $vehicle->per_min:"" }}" required="">
                  <p class="text-danger">{{ $errors->first('per_min') }}</p>
                </div>
                </div>
                
                
                
               <div class="row">
                  <div class="form-group col-sm-6">
                  <label for="exampleInputEmail1">Certain Time</label>
                  <input type="number" class="form-control" id="certain_time" placeholder="Certain Time" name="certain_time" value="{{ !empty($vehicle->certain_time) ? $vehicle->certain_time:"" }}" required="">
                  <p class="text-danger">{{ $errors->first('certain_time') }}</p>
                </div>
                
                
                 <div class="form-group col-sm-6">
                  <label for="exampleInputEmail1">Waiting Charges</label>
                  <input type="number" class="form-control" id="waiting_charges" placeholder="Waiting Charges" name="waiting_charges" value="{{ !empty($vehicle->waiting_charges) ? $vehicle->waiting_charges:"" }}" required="">
                  <p class="text-danger">{{ $errors->first('waiting_charges') }}</p>
                </div>
                </div> -->
              
              
                 
               <div class="row">
                <div class="form-group col-sm-6">
                  <label for="exampleInputFile">Image</label>
                  <input type="file" id="icon" name="image" require="">
                  <p class="text-danger">{{ $errors->first('image') }}</p>
                </div>
                @if(!empty($category))
                <img src="{{ url($category->image)}}" style="width:120px; height:120px;"/>
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
 
