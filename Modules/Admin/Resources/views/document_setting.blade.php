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
              <!--<h3 class="box-title">{{ isset($page_title) ? $page_title:''}}</h3>-->
                <!--<a href="{{url('admin/vehicle-create')}}" class="btn btn-primary pull-right">Add</a>-->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                @if(session()->has('success'))
                <div class="alert alert-success">
                  <p class="text-center">{{session()->get('success')}}</p>  
                </div>
                @endif
              <table id="example1" class="table table-bordered table-striped">
                  <tr>
                 <th>Document Name</th>
                 <th>Action</th>
                </tr>
               @if(isset($document_setting) && count($document_setting) >0)
               @foreach($document_setting as $row)
                <tr>
                 <td>{{$row->name}}</td>
                 <td>
                     <form method="post" action="">
                         @csrf
                     <input type="hidden" name="document_id" value="{{ $row->id }}"/>
                     <select class="form-control" name="document_status"  style="width: 94px" onchange="this.form.submit()">
                         <option value="0"  {{$row->status==0 ? "selected":""}}>Inactive</option>
                         <option value="1"  {{$row->status==1 ? "selected":""}}>Active</option>
                     </select>
                     </form>
                    
                 </td>
                </tr>
               @endforeach
               @endif
              </table>
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
  
  @endsection
