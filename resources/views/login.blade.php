<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <style type="text/css">
        html, body {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
        }
        .image-panel {
            background-size: cover;
            background-image: url("https://source.unsplash.com/collection/190727/1600x900");
            height: 100vh;
        }

        .login-panel {
            height: 100vh;
            background-color: white;
        }
    </style>

    <title>Anombox: Login</title>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="image-panel d-none d-md-block col-md-6 col-lg-8"></div>

        <div class="login-panel col-sm-12 col-md-6 col-lg-4 row">
            <div class="col-12 align-self-center">
                <h1>Login</h1>
                <form method="post" action="/anombox/v1/login">
                    <div class="form-group">
                        <input type="text" class="form-control" id="username" placeholder="Username">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="password" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script defer type="text/javascript">
    $("document").ready(function(){
        $("form").submit(function(e){
            e.preventDefault();

            var username = $(this).find("#username").val();
            var password = $(this).find("#password").val();
            var action = $(this).attr("action");

            $.ajax({
                url: action,
                method: "POST",
                data: {
                    username,
                    password
                },
                xhrFields: {
                    withCredentials: true
                }
            }).done(function(data){
                console.log(data);
                localStorage.token = data.token;
                $(window).attr('location', '/anombox/v1/view');
            });

            return false;
        });
    });
</script>
</body>
</html>
