<!DOCTYPE html>
<html>
<head>
    <title>Crud Generator</title>
    <meta charset="UTF-8" name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8" name="site-url" content="{{ url('/') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css">
    <style type="text/css">
        body{
            background: #f9f9f9;
        }
        .card {
          background-color: #ffffff;
          -webkit-box-shadow: 1px 5px 24px 0 rgba(68, 102, 242, 0.05);
                  box-shadow: 1px 5px 24px 0 rgba(68, 102, 242, 0.05);
          margin-bottom: 1.875rem;
          transition: all .5s ease-in-out;
          position: relative;
          border: 0px solid transparent;
          border-radius: 0.25rem;
        }
        .card .card-header {
            background-color: #ffffff;
            border-radius: 0;
            margin: 25px;
            padding: 0;
            border-color: #eaeaea;
            position: relative;
            background: transparent;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 0px;
        }
        ul{
            list-style:none;
        }
        ul li .btn{
            border-radius:0;
        }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Crud Generator</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="ml-auto navbar-nav">                    
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        @yield('content')
    </div>


    <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var token = jQuery('meta[name="csrf-token"]').attr('content');
            var site_url = jQuery('meta[name="site-url"]').attr('content');
            $(".gambar").attr(
                "src",
                "http://localhost:8000/assets/admin/img/150x75.jpg"
             );
            var $uploadCrop, tempFilename, rawImg, imageId, imageClicked;
            function readFile(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $("#upload-demo").addClass("ready");
                        $("#cropImagePop").modal("show");
                        rawImg = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $uploadCrop = $("#upload-demo").croppie({
                viewport: {
                    width: 220,
                    height: 220
                },
                boundary: {
                    width: 250,
                    height: 250
                },
                // enforceBoundary: false,
                enableExif: true
            });

            $("#cropImagePop").on("shown.bs.modal", function () {
                $uploadCrop.croppie("bind",
                {
                    url: rawImg
                })
                .then(function () {
                    // console.log("jQuery bind complete");
                });
            });

            $(".get-upload-img").on("change", function () {
                imageClicked = this;
                imageId = $(this).data("id");
                tempFilename = $(this).val();
                $("#cancelCropBtn").data("id", imageId);
                readFile(this);
            });
            
            $("#cropImageBtn").on("click", function (ev) {
                $uploadCrop.croppie("result", {
                    type: "base64",
                    format: "jpeg",
                    size: { width: 250, height: 250 }
                })
                .then(function (response) {
                    $.ajax({
                        url: site_url+"/crud-file-upload/",
                        type: "POST",
                        data: {"image":response, _token:token},
                        success: function (data) {
                            console.log(data);
                            $(imageClicked).parents('.upload-image-div').find('.upload-img-output').attr('src', response)
                            $(imageClicked).parents('.upload-image-div').find('.update-upload-img').attr('value', data.path)
                        }
                    });
                });
            });
        });
    </script>
    @yield('script-last')
</body>
</html>
