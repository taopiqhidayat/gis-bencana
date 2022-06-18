<?php
session_start();
require 'function.php';

if (isset($_SESSION["signin"])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST["submit"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // cek username
    $cek = mysqli_query($knk, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) === 1) {

        // cek password
        $get = mysqli_fetch_assoc($cek);
        if (password_verify($password, $get["password"])) {
            // buat session
            $_SESSION["signin"] = true;
            if ($get["is_admin"]) {
                $_SESSION["user"] = 1;
            } elseif ($get["is_bnpb"]) {
                $_SESSION["user"] = 2;
            } elseif ($get["is_volunter"]) {
                $_SESSION["user"] = 3;
            } elseif ($get["is_peoples"]) {
                $_SESSION["user"] = 4;
            }
            $_SESSION["id_user"] = $get["id"];

            header("Location: index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Login Page</title>
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="favicon.ico">
        <link rel="canonical" href="https://getbootstrap.com/docs/3.3/examples/jumbotron-narrow/">

        <!-- Bootstrap core CSS -->
        <link href="http://localhost/gis_bencana/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

        <!-- Desain/Styling web -->
        <link rel="stylesheet" href="desain.css">

        <!-- js -->
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="card col-md-6">
                    <div class="card-body">
                        <h4 class="card-title mb-3">SILAKAN LOGIN</h4>
                        <form action="" method="post">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username">
                            </div>
                            <div class="input-group mb-3">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                            </div>
                            <p class="card-text mt-3">Anda belum memiliki akun silhkan <a href="http://localhost/gis_bencana/register.php">registrasi disini!</a></p>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a class="btn btn-primary me-md-2" href="http://localhost/gis_bencana/">Cancel</a>
                                <button class="btn btn-primary" type="submit" name="submit">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- bootstrap js -->
        <script src="http://localhost/gis_bencana/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    </body>
</html>