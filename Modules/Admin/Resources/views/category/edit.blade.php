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
    <!--<section class="content-header">-->
    <!--  <h1>-->
    <!--    General Form Elements-->
    <!--    <small>Preview</small>-->
    <!--  </h1>-->
    <!--  <ol class="breadcrumb">-->
    <!--    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>-->
    <!--    <li><a href="#">Forms</a></li>-->
    <!--    <li class="active">General Elements</li>-->
    <!--  </ol>-->
    <!--</section>-->

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Quick Example</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
           
            <form role="form" method="post"  action="" enctype="multipart/form-data">
              <div class="box-body">
                
                <div class="row">
                    <div class="form-group col-sm-6">
                      <label for="exampleInputEmail1">Name</label>
                      <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Name" name="name">
                      <p class="text-danger">{{ $errors->first('name') }}</p>
                    </div>
                    
                     <div class="form-group col-sm-6">
                      <label for="exampleInputEmail1">Base Fair</label>
                      <input type="number" class="form-control" id="exampleInputEmail1" placeholder="Base Fair" name="base_fair">
                      <p class="text-danger">{{ $errors->first('base_fair') }}</p>
                    </div>
                </div>
                
                
                 <div class="row">
                  <div class="form-group col-sm-6">
                  <label for="exampleInputEmail1">Per Km Charges</label>
                  <input type="number" class="form-control" id="exampleInputEmail1" placeholder="Per Km Charges" name="per_km_charges">
                  <p class="text-danger">{{ $errors->first('per_km_charges') }}</p>
                </div>
                
                
                 <div class="form-group col-sm-6">
                  <label for="exampleInputEmail1">Per Min</label>
                  <input type="number" class="form-control" id="exampleInputEmail1" placeholder="Per Min" name="per_min">
                  <p class="text-danger">{{ $errors->first('per_min') }}</p>
                </div>
                </div>
                
                
                
               <div class="row">
                  <div class="form-group col-sm-6">
                  <label for="exampleInputEmail1">Certain Time</label>
                  <input type="number" class="form-control" id="exampleInputEmail1" placeholder="Certain Time" name="certain_time">
                  <p class="text-danger">{{ $errors->first('certain_time') }}</p>
                </div>
                
                
                <div class="form-group col-sm-6">
                  <label for="exampleInputFile">File input</label>
                  <input type="file" id="exampleInputFile" name="icon">
                  <p class="text-danger">{{ $errors->first('icon') }}</p>
                </div>
                </div>
              </div>
              

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
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
 
