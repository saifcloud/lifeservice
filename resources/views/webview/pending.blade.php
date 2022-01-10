<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="{{ url('public/webview/style.css') }}">
    <title>UI</title>
</head>

<body>
    <div class="box-size text-center">
        <nav class="navbar navbar-expand-lg bg-light border-bottom p-3">
            <ul class="navbar-nav m-auto ">
                <li class="nav-item">
                    <a class="nav-link" href="#">Pending Approval</a>
                </li>
            </ul>
        </nav>

        <div class="clock-img">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>

        <div class="mt-3 content">
            <h4>We're evaluating your profile</h4>
            <p class="mt-2 p-3">In order to make sure your community holds up to standard, we stringently moderate all individual profiles and content uploaded to the platform and, by necessity, operate strict guidelines to ensure they comply with our User Agreement and
                App Use Policy. </p>
        </div>
        <div>
            @if($document_status==1)
              <button class="btn border sub-btn ">Check Status</button>
            @else
              <a href="{{ url('webview/rejected-document?driver_id='.$_GET['driver_id'])}}" class="btn border sub-btn ">Check Status</a>
            @endif
          
        </div>
    </div>
</body>

</html>