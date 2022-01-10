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
                <!--<a href="{{url('admin/driver-create')}}" class="btn btn-primary pull-right">Add</a>-->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Sno</th>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Phone</th>
                    <th>Vehicle</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                   <?php $i=1;?>
                   @if(isset($driver) && count($driver) > 0)
                   @foreach($driver as $row)
                   <tr>
                   <td>{{ $i++ }}</td>
                   <td>{{ $row->firstname }}</td>
                   <td>{{ $row->lastname }}</td>
                   <td>+{{ $row->country_code }}{{ $row->phone }}</td>
                   <td>{{ $row->vehicle->name }}</td>
                   <td class="text-{{  ($row->admin_approved =='Pending') ? 'danger': ($row->admin_approved =='Approved' ? 'success':'danger')}}">
                       {{ $row->admin_approved }}
                   </td>
                   <td>
                       <a class="fa fa-eye text-success" href="{{ url('admin/driver-details/'.base64_encode($row->id))}}"></a>
                       <?php echo str_repeat("&nbsp;",4)?>
                       <!--<a class="fa fa-edit text-info" href="{{ url('admin/driver-edit/'.base64_encode($row->id)) }}"></a>-->
                       <?php echo str_repeat("&nbsp;",4)?>
                       <!--<a class="fa fa-trash text-danger" href="{{ url('admin/driver-delete/'.base64_encode($row->id)) }}" onclick="return confirm('Are you really want to delete.')"></a>-->
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
