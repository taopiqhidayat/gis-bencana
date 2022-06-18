<?php
    session_start();
    require 'function.php';

    if (!isset($_SESSION["signin"])) {
        header("Location: login.php");
        exit;
    }
    if ($_SESSION["user"] !== 1) {
        header("Location: index.php");
        exit;
    }

    // koneksi ke database dan ambil data
    $mas = mysqli_query($knk, "SELECT id, nama, nik, no_hp, email, username, active FROM users WHERE is_peoples = 1 ORDER BY created_at ASC");
    $res = mysqli_query($knk, "SELECT id, nama, nik, no_hp, email, username, active FROM users WHERE is_bnpb = 1 ORDER BY created_at ASC");
    $rew = mysqli_query($knk, "SELECT id, nama, nik, no_hp, email, username, active FROM users WHERE is_volunter = 1 ORDER BY created_at ASC");
    $masyarakat = array();
    $resque = array();
    $relawan = array();
    $x = 0;
    $y = 0;
    $z = 0;

    while ($my = mysqli_fetch_assoc($mas)) {
        $masyarakat[$x] = [
            "id" => $my["id"],
            "nama" => $my["nama"],
            "nik" => $my["nik"],
            "nohp" => $my["no_hp"],
            "email" => $my["email"],
            "username" => $my["username"],
            "status" => $my["active"]
        ];
        $x++;
    }
    while ($bn = mysqli_fetch_assoc($res)) {
        $resque[$y] = [
            "id" => $bn["id"],
            "nama" => $bn["nama"],
            "nik" => $bn["nik"],
            "nohp" => $bn["no_hp"],
            "email" => $bn["email"],
            "username" => $bn["username"],
            "status" => $bn["active"]
        ];
        $y++;
    }
    while ($rw = mysqli_fetch_assoc($rew)) {
        $relawan[$z] = [
            "id" => $rw["id"],
            "nama" => $rw["nama"],
            "nik" => $rw["nik"],
            "nohp" => $rw["no_hp"],
            "email" => $rw["email"],
            "username" => $rw["username"],
            "status" => $rw["active"]
        ];
        $z++;
    }

    if (isset($_POST["aktif"])) {
        if (setAktif($_POST)) {
            echo "<script>
                alert('Anda telah Berhasil mengaktifkan user!');
                
            </script>";
        } else {
            echo mysqli_error($knk);
        }
    }
    if (isset($_POST["blokir"])) {
        if (setBlokir($_POST)) {
            echo "<script>
                alert('Anda telah Berhasil memblokir user!');
            </script>";
        } else {
            echo mysqli_error($knk);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <meta http-equiv="refresh" content="5">
        <link rel="icon" href="favicon.ico">
        <link rel="canonical" href="https://getbootstrap.com/docs/3.3/examples/jumbotron-narrow/">

        <title>Management User</title>

        <!-- Bootstrap core CSS -->
        <link href="http://localhost/gis_bencana/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
        <link href="https://cdn.datatables.net/1.12.0/css/dataTables.bootstrap5.min.css">

        <!-- Desain/Styling web -->
        <link rel="stylesheet" href="desain.css">

        
    </head>
    <body>
        <div class="container">
            <!-- Header -->
            <div class="header clearfix mb-5">
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="index.php"><b>SPL GIS</b></a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                                </li>
                                <?php if(isset($_SESSION["signin"])) : ?>
                                <?php if($_SESSION["user"] === 1) : ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="management_user.php">Management User</a>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="data_incident.php">Data Bencana</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="data_evacuation.php">Data Evakuasi</a>
                                </li>
                                <?php endif ; ?>
                            </ul>
                            <div class="d-flex">
                                <?php if (isset($_SESSION["signin"])) : ?>
                                    <a class="btn btn-danger" href="logout.php">Logout</a>
                                <?php else : ?>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a class="btn btn-primary" href="register.php">Register</a>
                                    <a class="btn btn-success" href="login.php">Login</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
            
            <h3>Daftar User</h3>

            <a href="create_user.php" class="btn btn-primary my-3">Tambah User</a>

            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            User Masyarakat
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                                <table id="tbmas" class="table table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Username</th>
                                            <th>Nama</th>
                                            <th>NIK</th>
                                            <th>No HP</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for($a = 0; $a < count($masyarakat); $a++) : ?>
                                        <tr>
                                            <td><?= $a+1; ?></td>
                                            <td><?= $masyarakat[$a]["username"] ?></td>
                                            <td><?= $masyarakat[$a]["nama"] ?></td>
                                            <td><?= $masyarakat[$a]["nik"] ?></td>
                                            <td><?= $masyarakat[$a]["nohp"] ?></td>
                                            <td><?= $masyarakat[$a]["email"] ?></td>
                                            <td>
                                                <?php if($masyarakat[$a]["status"] == 1) : ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else : ?>
                                                        <span class="badge bg-danger">Blocked</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($masyarakat[$a]["status"] == 1) : ?>
                                                    <form action="" method="post">
                                                        <input type="hidden" name="id" value="<?= $masyarakat[$a]["id"] ?>">
                                                        <button type="submit" name="blokir" class="btn btn-danger">Blokir</button>
                                                    </form>
                                                <?php else : ?>
                                                    <form action="" method="post">
                                                        <input type="hidden" name="id" value="<?= $masyarakat[$a]["id"] ?>">
                                                        <button type="submit" name="aktif" class="btn btn-success">Aktifkan</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            User Pemerintah/BNPB/Basarnas
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <table id="tbmas" class="table table-striped" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Username</th>
                                        <th>Nama</th>
                                        <th>NIK</th>
                                        <th>No HP</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for($i = 0; $i < count($resque); $i++) : ?>
                                    <tr>
                                        <td><?= $i+1; ?></td>
                                        <td><?= $resque[$i]["username"] ?></td>
                                        <td><?= $resque[$i]["nama"] ?></td>
                                        <td><?= $resque[$i]["nik"] ?></td>
                                        <td><?= $resque[$i]["nohp"] ?></td>
                                        <td><?= $resque[$i]["email"] ?></td>
                                        <td>
                                            <?php if($resque[$i]["status"] == 1) : ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else : ?>
                                                    <span class="badge bg-danger">Blocked</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($resque[$i]["status"] == 1) : ?>
                                                <form action="" method="post">
                                                    <input type="hidden" name="id" value="<?= $resque[$i]["id"] ?>">
                                                    <button type="submit" name="blokir" class="btn btn-danger">Blokir</button>
                                                </form>
                                            <?php else : ?>
                                                <form action="" method="post">
                                                    <input type="hidden" name="id" value="<?= $resque[$i]["id"] ?>">
                                                    <button type="submit" name="aktif" class="btn btn-success">Aktifkan</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            User Relawan
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <table id="tbmas" class="table table-striped" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Username</th>
                                        <th>Nama</th>
                                        <th>NIK</th>
                                        <th>No HP</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for($u = 0; $u < count($relawan); $u++) : ?>
                                    <tr>
                                        <td><?= $u+1; ?></td>
                                        <td><?= $relawan[$u]["username"] ?></td>
                                        <td><?= $relawan[$u]["nama"] ?></td>
                                        <td><?= $relawan[$u]["nik"] ?></td>
                                        <td><?= $relawan[$u]["nohp"] ?></td>
                                        <td><?= $relawan[$u]["email"] ?></td>
                                        <td>
                                            <?php if($relawan[$u]["status"] == 1) : ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else : ?>
                                                    <span class="badge bg-danger">Blocked</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($relawan[$u]["status"] == 1) : ?>
                                                <form action="" method="post">
                                                    <input type="hidden" name="id" value="<?= $relawan[$u]["id"] ?>">
                                                    <button type="submit" name="blokir" class="btn btn-danger">Blokir</button>
                                                </form>
                                            <?php else : ?>
                                                <form action="" method="post">
                                                    <input type="hidden" name="id" value="<?= $relawan[$u]["id"] ?>">
                                                    <button type="submit" name="aktif" class="btn btn-success">Aktifkan</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
                <span class="text-muted">Hak Cipta &copy 2022</span>
            </div>
        </div>

        <!-- js -->
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.12.0/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.12.0/js/dataTables.bootstrap5.min.js"></script>

        <!-- bootstrap js -->
        <script src="http://localhost/gis_bencana/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    </body>
</html>