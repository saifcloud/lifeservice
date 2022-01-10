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
                <a href="{{url('admin/category-create')}}" class="btn btn-primary pull-right">Add</a>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Image</th>
                  <th>English category</th>
                  <th>Arabic category</th>
                  <th>Kurdish category</th>
                  <th>Created At</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                   <?php $i=1;?>
                   @if(isset($category) && count($category) > 0)
                   @foreach($category as $row)
                   <tr>
                   <td>{{$i++}}</td>
                   <td><img src="{{url($row->image)}}" style="width:60px; height:60px;"></td>
                   <td>{{$row->en_category}}</td>
                   <td>{{$row->ar_category}}</td>
                   <td>{{$row->kur_category}}</td>
                   <td>{{$row->created_at}}</td>
                   <td>
                       <a class="fa fa-edit text-info" href="{{ url('admin/category-edit/'.base64_encode($row->id)) }}"></a>
                       <?php echo str_repeat("&nbsp;",4)?>
                       <a class="fa fa-trash text-danger" href="{{ url('admin/category-delete/'.base64_encode($row->id)) }}" onclick="return confirm('Are you really want to delete.')"></a>
                  </td>
                   </tr>
                   @endforeach
                   @endif
               
               
                </tfoot>
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
