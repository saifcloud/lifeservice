<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ url('public/assets/css/bootstrap.min.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
   <script src="{{ url('public/webview/popper.min.js')}}"></script>
    <script src="{{ url('public/webview/bootstrap.min.js')}}"></script>
   <link rel="stylesheet" href="{{ url('public/assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/webview/style.css') }}">
    <title>UI</title>
</head>


<body>
    <div class="box-size">
        <nav class="navbar navbar-expand-lg bg-light border-bottom p-3">
            <a class="navbar-brand" href="#"><i class="fa-solid fa-chevron-left"></i></a>
            <ul class="navbar-nav m-auto ">
                <li class="nav-item">
                    <a class="nav-link" href="#">DOCUMENTS</a>
                    
                </li>
            </ul>
        </nav>
        
        <form method="post" id="documentForm" action="" enctype="multipart/form-data">
            @csrf
        <div class="data">
            <ul>
                
                @if(isset($get_document_options) && count($get_document_options) > 0 )
                @foreach($get_document_options as $row)
                <li>
                  
                    <i class="fa-solid fa-circle-up ml-2"> </i>
                    <label for="{{$row->id}}">
                    <span class="mr-auto ml-2">{{ $row->name }}<small>(mandatory)</small> </span>
                    <small class="text-danger">Rejected, upload again</small>
                    <i class="fa-solid fa-chevron-right mr-2"></i>
                    <input type="file" id="{{$row->id}}" name="{{$row->id}}" class="filesdata" style="display:none"/>
                </li>
                @endforeach
                @endif
                
            </ul>
        </div>
        <p class="text-danger text-center mt-3" id="error_message"></p>
        <div class="text-center">
            <input type="submit" style="display:none;" class="btn border sub-btn" id="subbtn" name="submit" value="SUBMIT">
            <button type="button"  class="btn border sub-btn" onclick="return validation();">SUBMIT</button> 
        </div>
        </form>
    </div>
 
    
    <script>
        function validation(){
      
            var validFields = $('input[type="file"]').map(function() {
                                    if ($(this).val() != "") {
                                        return $(this);
                                    }
                             }).get(); 
                
            if(validFields.length==0){
                $("#error_message").text("Upload rejected documents.");
            }else{
            
               $("#subbtn").click();
            }
            
        }
    </script>
</body>

</html>