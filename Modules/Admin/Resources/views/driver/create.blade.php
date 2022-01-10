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
                      <label for="exampleInputEmail1">First Name</label>
                      <input type="text" class="form-control" id="firstname" placeholder="Firstname" name="firstname" value="{{ !empty($driver->firstname) ? $driver->firstname:"" }}" required="">
                      <p class="text-danger">{{ $errors->first('firstname') }}</p>
                    </div>
                    
                     <div class="form-group col-sm-6">
                      <label for="exampleInputEmail1">Last Name</label>
                      <input type="number" class="form-control" id="lastname" placeholder="Lastname" name="lastname" value="{{ !empty($driver->lastname) ? $driver->lastname:"" }}" required="">
                      <p class="text-danger">{{ $errors->first('lastname') }}</p>
                    </div>
                </div>
                
                
                 <div class="row">
                     
                  <div class="form-group col-sm-6">
                  <label for="exampleInputEmail1" class="">Phone</label>
                  
                  <div class="row">
                  <div class="col-sm-3">
                  <select class="form-control" name="country_code">
                      <option value="">select</option>
                      @if(isset($country) && count($country) > 0 )
                      @foreach($country as $row)
                      <option value="{{ $row->phonecode }}">{{ $row->phonecode }}</option>
                      @endforeach
                      @endif
                  </select>
                      
                  </div>
                  
                   <div class="col-sm-9">
                  <input type="number" class="form-control" id="phone" placeholder="Phone" name="phone" value="{{ !empty($driver->phone) ? $driver->phone:"" }}" required="">
                  </div>
                   </div>
                  
                  <p class="text-danger">{{ $errors->first('phone') }}</p>
                </div>
                
                
                 <div class="form-group col-sm-6">
                  <label for="exampleInputEmail1">Email</label>
                  <input type="number" class="form-control" id="email" placeholder="Email" name="email" value="{{ !empty($driver->email) ? $driver->email:"" }}" required="">
                  <p class="text-danger">{{ $errors->first('email') }}</p>
                </div>
                </div>
                
                
                
               <div class="row">
                  <div class="form-group col-sm-6">
                  <label for="exampleInputEmail1">Vehicle</label>
                  <!--<input type="number" class="form-control" id="vehicle" placeholder="Vehicle" name="vehicle" value="{{ !empty($vehicle->vehicle) ? $vehicle->vehicle:"" }}" required="">-->
                  <select class="form-control" name="vehicle">
                      <option value="">select</option>
                      @if(isset($vehicle) && count($vehicle) > 0 )
                      @foreach($vehicle as $row)
                      <option value="{{ $row->id }}">{{ $row->name }}</option>
                      @endforeach
                      @endif
                  </select>
                  <p class="text-danger">{{ $errors->first('vehicle') }}</p>
                </div>
                
                
                 <div class="form-group col-sm-6">
                  <label for="exampleInputEmail1">City</label>
                  <!--<input type="number" class="form-control" id="city" placeholder="City" name="city" value="{{ !empty($vehicle->city) ? $vehicle->city:"" }}" required="">-->
                  <select class="form-control" name="vehicle_id">
                      <option value="">select</option>
                      @if(isset($city) && count($city) > 0 )
                      @foreach($city as $row)
                      <option value="{{ $row->id }}">{{ $row->name }}</option>
                      @endforeach
                      @endif
                  </select>
                  <p class="text-danger">{{ $errors->first('city') }}</p>
                </div>
                </div>
              
              
                 
               <!--<div class="row">-->
               <!-- <div class="form-group col-sm-6">-->
               <!--   <label for="exampleInputFile">File input</label>-->
               <!--   <input type="file" id="icon" name="icon">-->
               <!--   <p class="text-danger">{{ $errors->first('icon') }}</p>-->
               <!-- </div>-->
               <!-- </div>-->
                
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<scritp>
    
</scritp>
@endsection
 
