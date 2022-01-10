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
    <link rel="stylesheet" href="{{ url('webview/style.css') }}">
    <title>DOCUMENTS</title>
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
        <div class="data">
            <ul>
                <li for="adhar">
                    <i class="fa-solid fa-circle-up ml-2"> </i>
                    <span class="mr-auto ml-2">Aadhar Card <small>(mandatory)</small> </span>
                    <i class="fa-solid fa-chevron-right mr-2"></i>
                    <input type="file" id="adhar"/>
                </li>
                <li>
                    <i class="fa-solid fa-circle-up ml-2"> </i>
                    <span class="mr-auto ml-2">Insurance Doc <small>(mandatory)</small> </span>
                    <i class="fa-solid fa-chevron-right mr-2"></i>
                </li>
                <li>
                    <i class="fa-solid fa-circle-up ml-2"> </i>
                    <span class="mr-auto ml-2">RC <small>(mandatory)</small> </span>
                    <i class="fa-solid fa-chevron-right mr-2"></i>
                </li>
                <li>
                    <i class="fa-solid fa-circle-up ml-2"> </i>
                    <span class="mr-auto ml-2"> Driver Photo <small>(mandatory)</small> </span>
                    <i class="fa-solid fa-chevron-right mr-2"></i>
                </li>
                <li>
                    <i class="fa-solid fa-circle-up ml-2"> </i>
                    <span class="mr-auto ml-2">Permit <small>(mandatory)</small> </span>
                    <i class="fa-solid fa-chevron-right mr-2"></i>
                </li>
                <li>
                    <i class="fa-solid fa-circle-up ml-2"> </i>
                    <span class="mr-auto ml-2">Pollution <small>(mandatory)</small> </span>
                    <i class="fa-solid fa-chevron-right mr-2"></i>
                </li>
                <li>
                    <i class="fa-solid fa-circle-up ml-2"> </i>
                    <span class="mr-auto ml-2">Driving Licence <small>(mandatory)</small> </span>
                    <i class="fa-solid fa-chevron-right mr-2"></i>
                </li>
            </ul>
        </div>
        <div class="text-center">
            <button class="btn border sub-btn">SUBMIT</button>
        </div>
    </div>
</body>



</html>