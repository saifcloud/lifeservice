@extends("admin::layouts.master")
@section("content")
<div class="wrapper">

@include("admin::partials.sidebar")

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       {{ isset($page_title) ? $page_title:''}}
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="javascript:;">{{ isset($page_title) ? $page_title:''}}</a></li>
        <!--<li class="active">Data tables</li>-->
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">{{ isset($page_title) ? $page_title:''}}</h3>
                <!--<a href="{{url('admin/driver-create')}}" class="btn btn-primary pull-right">Add</a>-->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                @if(session()->has('success'))
                <div class="alert alert-success">
                    <p class="text-danger">{{ session()->get('success')}}</p>
                </div>
                @endif
              <table id="example1" class="table table-bordered table-striped">
             
              
                                        <tr>
                                            <th>Firstname</th>
                                            <td>{{ $driver->firstname}}</td>
                                        </tr>
                                        
                                         <tr>
                                            <th>Lastname</th>
                                            <td>{{ $driver->lastname}}</td>
                                        </tr>
                                         <tr>
                                            <th>Email</th>
                                            <td>{{ $driver->email}}</td>
                                        </tr>
                                         <tr>
                                            <th>phone</th>
                                            <td>+{{ $driver->country_code}}{{ $driver->phone}}</td>
                                        </tr>
                                         <tr>
                                            <th>City</th>
                                            <td>{{ $driver->city->name}}</td>
                                        </tr>
                                         <tr>
                                            <th>Vehicle</th>
                                            <td>{{ $driver->vehicle->name}}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td style="
    margin-left: 8px;
" class="btn btn-danger bg-{{ ($driver->admin_approved=='pending') ? 'danger':(($driver->admin_approved=='approved') ? 'success':'danger')}} text-white">{{ $driver->admin_approved }}</td>
                                        </tr>
                                        
                                         <tr>
                                            <th>Block</th>
                                            <td class="">
                                                 <form method="post" action="{{url('admin/action-on-driver')}}">
                                                     @csrf
                                                 <input type="hidden"  name="driver_id" value="{{$driver->id}}"/>
                                                 <select class="form-control col-sm-3" name="block_status" id="block_status" onchange="this.form.submit();" style="width: 80px;">
                                                     <option value="Yes" {{ $driver->is_blocked=="Yes" ? "selected":""}}>Yes</option>
                                                     <option value="No" {{ $driver->is_blocked=="No" ? "selected":""}}>No</option>
                                                 </select>
                                                 </form>
                                            </td>
                                        </tr>
                                        
                                    
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          
            <div class="box col-xs-6">
            <div class="box-header">
              <h3 class="box-title">Documents</h3>
                <!--<a href="{{url('admin/driver-create')}}" class="btn btn-primary pull-right">Add</a>-->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                
              <table id="example1" class="table table-bordered table-striped">
                
                
                  <tr>
                       <td style="width: 165px;">Document Name</td>
                       <td style="width: 165px;">Document</td>
                       <td style="width: 100px;">Action</td>
                  </tr>
                  @if(isset($driver->documents) && count($driver->documents) > 0)
                  @foreach($driver->documents as $row)
                    <tr>
                    <td style="width: 165px;">{{ $row->document_option->name }}</td>
                
    
                 
                    <td>
                        <a href="{{ url('backendApi/'.$row->document)}}" target="_blank"><img src="{{ url('backendApi/'.$row->document)}}" style="width:150px; width:200px;"/></a>
                   </td>
                   
                    <td>
                         @if($row->status=="Pending")
                         <div class="form-group">
                         <input type="checkbox" class="flat-red" value="{{$row->id}}"  name="documents">
                         </div>
                         @endif
                         
                         @if($row->status=="Approved")
                         <button class="btn btn-success">Approved</button>
                         @endif
                        
                         @if($row->status=="Rejected")
                         <button class="btn btn-danger">Rejected</button>
                         @endif
                         
                        <!--@if($row->status=="Pending")-->
                        <!-- <form method="post" action="">-->
                        <!--     @csrf-->
                        <!-- <input type="hidden" name="document_id" value="{{$row->id}}"/>-->
                        <!-- <select class="form-control" name="document_status" onchange="this.form.submit()">-->
                        <!--     <option value="Pending" {{ $row->status=="Pending" ? "selected":"" }}>Pending</option>-->
                        <!--     <option value="Approved" {{ $row->status=="Approved" ? "selected":"" }}>Accept</option>-->
                        <!--     <option value="Rejected" {{ $row->status=="Rejected" ? "selected":"" }}>Reject</option>-->
                        <!-- </select>-->
                        <!-- </form>-->
                        <!-- @endif-->
                         
                        <!-- @if($row->status=="Approved")-->
                        <!-- <button class="btn btn-success btn-block">Approved</button> -->
                        <!-- @endif-->
                         
                        <!-- @if($row->status=="Rejected")-->
                        <!-- <button class="btn btn-danger btn-block">Rejected</button> -->
                        <!-- @endif-->
                   </td>
                   </tr>
                
                  @endforeach
                  @endif
                
             </table>
             <p class="validation_status h4"></p>
             <div class="col-sm-3">
                 
                 <label>Action</label>
                      @if($driver->admin_approved == "Pending")
                         <form method="post" action="">
                             @csrf
                         <input type="hidden"  name="document_id" value="{{$row->id}}"/>
                         <select class="form-control" name="document_status" id="document_status">
                             <option value="">Pending</option>
                             <option value="Approved">Accept</option>
                             <option value="Rejected">Reject</option>
                         </select>
                         </form>
                         @endif
                         
                         @if($driver->admin_approved=="Approved")
                         <button class="btn btn-success btn-block">Approved</button> 
                         @endif
                         
                        <!-- @if($row=="Rejected")-->
                        <!-- <button class="btn btn-danger btn-block">Rejected</button> -->
                        <!--@endif-->
             </div>
             
            </div>
            
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
          
          
          
          
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script>
      $(document).ready(function(){
          $("#document_status").change(function(){
            //   alert($(this).val())
            if($(this).val()=="Rejected"){
                    var a = $('input[name=documents]:checked').map(function(){
                      return $(this).val()
                    }).get()
                    
                    if(a==""){
                        $(".validation_status").text('please select which you want reject documents.');
                        $(".validation_status").addClass("text-danger");
                    }else{
                        $.ajaxSetup({
                                   headers: {
                                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                   }
                                });

                       $.ajax({
                              url:"{{ url('admin/reject-document')}}",
                              method:"POST",
                              data:{driver_id:"{{$driver->id}}",documents:a},
                              success:function(response){
                                 console.log(response); 
                                 if(response.status==true){
                                     $(".validation_status").text(response.message);
                                     $(".validation_status").addClass("text-success");
                                     window.location.reload();
                                 }
                              }
                       })
                    }
                    

            }
            
            if($(this).val()=="Approved"){
                
                $.ajaxSetup({
                                   headers: {
                                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                   }
                                });

                       $.ajax({
                              url:"{{ url('admin/approved-document')}}",
                              method:"POST",
                              data:{driver_id:"{{$driver->id}}"},
                              success:function(response){
                                 console.log(response); 
                                 if(response.status==true){
                                     $(".validation_status").text(response.message);
                                     $(".validation_status").addClass("text-success");
                                      window.location.reload();
                                 }
                              }
                       })
                       
            }
          })
      })
  </script>
  
  @endsection
