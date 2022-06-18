<?php
    session_start();
    require 'function.php';

    if (!isset($_SESSION["signin"])) {
        header("Location: login.php");
        exit;
    }

    // koneksi ke database dan ambil data
    $query = mysqli_query($knk, "SELECT * FROM evacuation ORDER BY tgl ASC");
    $data = array();
    $x = 0;

    // masukan data ke sebuah array, tambah data titik yang sudah dibulatkan nilainya
    while($rw = mysqli_fetch_assoc($query)) {
        if ($rw["titu"] != null || $rw["ngitu"] != null) {
            $data[$x] = [
                "id" => $rw["id"],
                "id_incident" => $rw["id_incident"],
                "daerah" => $rw["district"],
                "titu" => $rw["latitude"],
                "ngitu" => $rw["longitude"],
                "keterangan" => "Laki-laki: " . $rw["laki_laki"] . 
                                " Wanita:" . $rw["wanita"] . 
                                " Anak-anak: " . $rw["anak_anak"] . 
                                " Orang Tua: " . $rw["orang_tua"] . 
                                " Ibu Hamil:" . $rw["ibu_hamil"] . 
                                " Jumlah:" . $rw["jumlah"],
                "tgl" => $rw["tgl"]
            ];
            $x++;
        }
    }
    for ($u=0; $u < count($data); $u++) { 
        $data[$u]["daerah"] = getKab($data[$u]["daerah"]);
    }
    
    $query = mysqli_query($knk, "SELECT * FROM reports_evacuation ORDER BY tanggal ASC");
    $datare = array();
    $y = 0;

    // masukan data ke sebuah array, tambah data titik yang sudah dibulatkan nilainya
    while($re = mysqli_fetch_assoc($query)) {
        $datare[$y] = [
            "id" => $re["id"],
            "id_user" => $re["id_user"],
            "id_disaster" => $re["id_disaster"],
            "address" => $re["address"],
            "titu" => $re["latitude"],
            "ngitu" => $re["longitude"],
            "tanggal" => $re["tanggal"],
            "keterangan" => "Laki-laki: " . $re["laki_laki"] . 
                            " Wanita:" . $re["wanita"] . 
                            " Anak-anak: " . $re["anak_anak"] . 
                            " Orang Tua: " . $re["orang_tua"] . 
                            " Ibu Hamil:" . $re["ibu_hamil"] . 
                            " Jumlah:" . $re["jumlah"],
            "status" => $re["status_cnf"]
        ];
        $y++;
    }
    for ($e=0; $e < count($datare); $e++) { 
        $datare[$e]["daerah"] = getKab($datare[$e]["daerah"]);
    }

    if (isset($_POST["terima"])) {
        if (terReEvac($_POST) > 0) {
            echo "<script>
                    alert('Anda telah Berhasil menerima laporan evakuasi!');
                </script>";
        } else {
            echo mysqli_error($knk);
        }
    }
    if (isset($_POST["tolak"])) {
        if (denyReEvac($_POST) > 0) {
            echo "<script>
                    alert('Anda telah Berhasil menerima laporan evakuasi!');
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
        <link rel="icon" href="favicon.ico">
        <link rel="canonical" href="https://getbootstrap.com/docs/3.3/examples/jumbotron-narrow/">

        <title>Data Evakuasi</title>

        <!-- Bootstrap core CSS -->
        <link href="http://localhost/gis_bencana/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
        <link href="https://cdn.datatables.net/1.12.0/css/dataTables.bootstrap5.min.css">

        <!-- Desain/Styling web -->
        <link rel="stylesheet" href="desain.css">

        <!-- js -->
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.12.0/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.12.0/js/dataTables.bootstrap5.min.js"></script>
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

            <?php if($_SESSION["user"] == 1 || $_SESSION["user"] == 2): ?>
            <h2>Laporan Evakuasi</h2>

            <div class="card mt-3 mb-5">
                <div class="card-body">
                    <table id="tabreevac" class="table table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pelapor</th>
                                <th>NIK Pelapor</th>
                                <th>No HP Pelapor</th>
                                <th>Insiden</th>
                                <th>Alamat</th>
                                <th>Koordinat</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for($e = 0; $e < count($datare); $e++) : ?>
                            <?php $nama = getNama($datare[$e]["id_user"]); ?>
                            <?php $nik = getNIK($datare[$e]["id_user"]); ?>
                            <?php $nohp = getNohp($datare[$e]["id_user"]); ?>
                            <?php $insiden = getBencana($datare[$e]["id_disaster"]); ?>
                            <tr>
                                <td><?= $e+1 ?></td>
                                <td><?= $nama ?></td>
                                <td><?= $nik ?></td>
                                <td><?= $nohp ?></td>
                                <td><?= $insiden ?></td>
                                <td><?= $datare[$e]["address"] ?></td>
                                <td><?= $datare[$e]["titu"] . ", " . $datare[$e]["ngitu"] ?></td>
                                <td><?= $datare[$e]["tanggal"] ?></td>
                                <td><?= $datare[$e]["keterangan"] ?></td>
                                <td>
                                    <?php if($datare[$e]["status"] == 1) : ?>
                                        <span class="badge bg-success">Diterima</span>
                                    <?php elseif ($datare[$e]["status"] == 0) : ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Belum Ditanggapi</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="" method="post">
                                        <input type="hidden" name="id" value="<?= $datare[$e]["id"] ?>">
                                        <?php if($datare[$e]["status"] == 1) : ?>
                                            <button type="submit" name="tolak" class="btn btn-danger">Tolak</button>
                                        <?php elseif ($datare[$e]["status"] == 0) : ?>
                                            <button type="submit" name="terima" class="btn btn-success">Terima</button>
                                        <?php else: ?>
                                            <button type="submit" name="terima" class="btn btn-success">Terima</button>
                                            <button type="submit" name="tolak" class="btn btn-danger">Tolak</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php endif ; ?>

            <h3>Data Evakuasi</h3>

            <a href="create_evacuation.php" class="btn btn-primary mt-3">Tambah Data</a>

            <div class="card mt-3">
                <div class="card-body">
                    <table id="tabevac" class="table table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bencana</th>
                                <th>Alamat</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <?php if ($_SESSION["user"] === 1) : ?>
                                    <th>Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for($a = 0; $a < count($data); $a++) : ?>
                            <tr>
                                <td><?= $a+1 ?></td>
                                <td><?= $data[$a]["bencana"] ?></td>
                                <td><?= $data[$a]["daerah"] ?></td>
                                <td><?= $data[$a]["tgl"] ?></td>
                                <td><?= $data[$a]["keterangan"] ?></td>
                                <?php if ($_SESSION["user"] === 1) : ?>
                                <td>
                                    <a href="update_incident.php" class="btn btn-warning">Ubah</a>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
                <span class="text-muted">Hak Cipta &copy 2022</span>
            </div>
        </div>

        <script>
        $(document).ready(function () {
            $('#tabevac').DataTable();
            $('#tabreevac').DataTable();
        });
        </script>

        <!-- bootstrap js -->
        <script src="http://localhost/gis_bencana/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    </body>
</html>