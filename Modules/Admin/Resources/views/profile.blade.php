@extends('admin::layouts.master')
@section('content')

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
                        <!-- <li class="breadcrumb-item active"></li> -->
                    </ul>
                    <!--<a href="{{ url('admin/size-create')}}" class="btn btn-sm btn-primary" title="">Create New</a>-->
                </div>
            </div>
        </div>

        <div class="container-fluid">

            <div class="row clearfix">
                <div class="col-12">
                    <div class="card top_report">
                        @if(session()->has('success'))
                        <div class="alert alert-success">
                            <p class="text-center">{{ session()->get('success')}}</p>
                        </div>
                        @endif
                        
                        
                    <!--<div class="card">-->
                        <div class="header">
                            <h2></h2>
                        </div>

                        <div class="col-sm-12">
                             @if(Session()->has('failed'))
    	                     <p class="alert alert-danger text-center">{{ Session()->get('failed')}}</p>
    	                     @endif
                        </div>


                        <div class="body">
                            <form id="basic-form" method="post" action="{{ url('admin/profile')}}"  enctype="multipart/form-data">
                               @csrf

                              
                              
                               <div class="row col-sm-12">
                                    <div class="form-group col-sm-4">
                                   
                                       <img src="">
                                    </div>
                                </div>
                                
                                
                                
                               <div class="row col-sm-12">

                 
            
                                    <div class="form-group col-sm-4">
                                        
                                        <label>Image</label>
                                        <input type="file" class="form-control" name="image">
                                        @if($errors->has('image'))
                                        <p class="text-danger">{{ $errors->first('image') }}</p>
                                        @endif
                                    </div>
                                    
                                    
                                    

                                    <!--<div class="form-group col-sm-4">-->
                                    <!--    <label>Email</label>-->
                                    <!--    <input type="text" class="form-control" name="email" value="{{ Auth::guard('admin')->user()->email }}">-->
                                    <!--    @if($errors->has('email'))-->
                                    <!--    <p class="text-danger">{{ $errors->first('email') }}</p>-->
                                    <!--    @endif-->
                                    <!--</div>-->


                                </div>
                                
                                
                                <div class="row col-sm-12">

                                    <div class="form-group col-sm-4">
                                        <label>Name</label>
                                        <input type="text" class="form-control" name="name" value="{{ Auth::guard('admin')->user()->name}}">
                                        @if($errors->has('name'))
                                        <p class="text-danger">{{ $errors->first('name') }}</p>
                                        @endif
                                    </div>

                                    <div class="form-group col-sm-4">
                                        <label>Email</label>
                                        <input type="text" class="form-control" name="email" value="{{ Auth::guard('admin')->user()->email }}" readonly="">
                                        @if($errors->has('email'))
                                        <p class="text-danger">{{ $errors->first('email') }}</p>
                                        @endif
                                    </div>


                                </div>
                                
                                  <div class="row col-sm-12">

                                    <div class="form-group col-sm-4">
                                        <label>Old password</label>
                                        <input type="password" class="form-control" name="old_password">
                                        @if($errors->has('old_password'))
                                        <p class="text-danger">{{ $errors->first('old_password') }}</p>
                                        @endif
                                        
                                         @if(session()->has('old_password_error'))
                                        <!--<div class="alert alert-success">-->
                                            <p class="text-danger">{{ session()->get('old_password_error')}}</p>
                                        <!--</div>-->
                                        @endif
                                    </div>

                                    <div class="form-group col-sm-4">
                                        <label>New Password</label>
                                        <input type="password" class="form-control" name="new_password">
                                        @if($errors->has('new_password'))
                                        <p class="text-danger">{{ $errors->first('new_password') }}</p>
                                        @endif
                                    </div>


                                </div>
                               
                                

                               
                               
                              
                               
                               
                                <br>

                                <button type="submit" class="btn btn-primary ml-3">Update</button>
                            </form>
                        </div>
                    <!--</div>-->
                    
                    
                    </div>
                </div>
            </div>

        </div>
    </div>
    
</div>


@endsection