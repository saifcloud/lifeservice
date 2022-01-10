@extends('admin::layouts.master')
@section('content')


<style>
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
}

input[type=number] {
  -moz-appearance: textfield;
}
</style>
<div id="wrapper">

  
  @include('admin::partials.navbar')
  @include('admin::partials.sidebar')

    <div id="main-content">
        <div class="block-header">
            <div class="row clearfix">
                <div class="col-md-6 col-sm-12">
                    <h2>{{ isset($page_title) ? $page_title:''}}</h2>
                </div>            
                <div class="col-md-6 col-sm-12 text-right">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard')}}"><i class="icon-home"></i></a></li>
                        <!-- <li class="breadcrumb-item active">Dashboard</li> -->
                    </ul>
                   <!--  <a href="javascript:void(0);" class="btn btn-sm btn-primary" title="">Create New</a> -->
                </div>
            </div>
        </div>

        <div class="container-fluid">

           <div class="row clearfix">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2></h2>
                        </div>
                        <div class="body">
                            <form id="basic-form" method="post"  action="" enctype="multipart/form-data">
                                @csrf
                               
                               
                                <div class="row">

                                    <div class="form-group col-sm-12">
                        
                                        <div class="d-flex justify-content-center">
                                            <img src="{{ url('backendApi'.$driver->image) }}" height="120" width="120"/>
                                        </div>
                                        
                                    </div>
                                    

                        
                                </div>
                                
                                <div class="row">

                                    <div class="form-group col-sm-6">
                                        <label>Image</label>
                                        <input type="file" class="form-control" name="icon">
                                       
                                         <p class="text-danger">{{ $errors->first('icon') }}</p>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label>Name</label>
                                        <input type="text" class="form-control" name="name" value="{{ $driver->name }}" required>
                                        <p class="text-danger">{{ $errors->first('name') }}</p>
                                    </div>

                        
                                </div>


                                 <div class="row">

                                    <div class="form-group col-sm-6">
                                        <label>Base fair</label>
                                        <input type="number" class="form-control" name="base_fair" value="{{ $driver->base_fair }}" required>
                                         <p class="text-danger">{{ $errors->first('base_fair') }}</p>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label>Per km charges</label>
                                        <input type="number" class="form-control" name="per_km_charges" value="{{ $driver->per_km_charges }}" required>
                                        <p class="text-danger">{{ $errors->first('per_km_charges') }}</p>
                                    </div>
                               

                                </div>
                                
                                
                                
                                   <div class="row">

                                    <div class="form-group col-sm-4">
                                        <label>Per min</label>
                                        <input type="text" class="form-control" name="per_min" value="{{ $driver->per_min }}" required>
                                         <p class="text-danger">{{ $errors->first('per_min') }}</p>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label>Certain time</label>
                                        <input type="text" class="form-control" name="certain_time" value="{{ $driver->certain_time }}" required>
                                        <p class="text-danger">{{ $errors->first('certain_time') }}</p>
                                    </div>
                                     <div class="form-group col-sm-4">
                                        <label>Waiting charges</label>
                                        <input type="number" class="form-control" name="waiting_charges"  value="{{ $driver->waiting_charges }}"  required>
                                        <p class="text-danger">{{ $errors->first('waiting_charges') }}</p>
                                    </div>
                               

                                </div>

                               <br> 
                                <input type="submit" class="btn btn-primary" name="submit" value="Update">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>


@endsection