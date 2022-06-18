<?php
    session_start();
    require 'function.php';

    if (!isset($_SESSION["signin"])) {
        header("Location: login.php");
        exit;
    }

    $id = $_GET["id"];
    $qque = mysqli_query($knk, "SELECT * FROM evacuation WHERE id = '$id'");
    while($dt = mysqli_fetch_assoc($qque)){
        $va[] = [
            "id" => $dt["id"],
            "insiden" => $dt["id_disaster"],
            "address" => $dt["location"],
            "district" => $dt["district"],
            "desa" => getDes($dt["district"]),
            "kecamatan" => getKec($dt["district"]),
            "kabten" => getKab($dt["district"]),
            "vinsi" => getVinsi($dt["district"]),
            "idkec" => getIDKec($dt["district"]),
            "idkab" => getIDKab($dt["district"]),
            "idvin" => getIDVinsi($dt["district"]),
            "titu" => $dt["latitude"],
            "ngitu" => $dt["longitude"],
            "laki" => $dt["laki"],
            "wanita" => $dt["wanita"],
            "anak" => $dt["anak"],
            "ortu" => $dt["ortu"],
            "buham" => $dt["bumil"],
            "jumlah" => $dt["jumlah"],
            "tgl" => $dt["tgl"]
        ];
    }
    
    // koneksi ke database dan ambil data
    $query = mysqli_query($knk, "SELECT id, incident as bencana, district as daerah, latitude as titu, longitude as ngitu, meninggal, luka, hilang, bangunan, rumah, kerugian, tgl FROM disaster ORDER BY tgl ASC");
    $data = array();
    $x = 0;

    // masukan data ke sebuah array, tambah data titik yang sudah dibulatkan nilainya
    while($rw = mysqli_fetch_assoc($query)) {
        if ($rw["titu"] != null || $rw["ngitu"] != null) {
            $data[$x] = [
                "id" => $rw["id"],
                "bencana" => $rw["bencana"],
                "daerah" => $rw["daerah"],
                "tgl" => $rw["tgl"]
            ];
            $x++;
        }
    }

    if (isset($_POST["submit"])) {
        if (ubahEvac($_POST) > 0) {
            echo "<script>
                    alert('Anda telah Berhasil mengubbah data evakuasi!');
                    document.location.href = 'data_incident.php';
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

        <title>Report Evacuation</title>
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="favicon.ico">
        <link rel="canonical" href="https://getbootstrap.com/docs/3.3/examples/jumbotron-narrow/">

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
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="card col-md-6">
                    <div class="card-body">
                        <h2 class="card-title mb-3"><b>EVACUATION FORM</b></h2>
                        <h5 class="card-subtitle mb-5 text-muted">Isilah semua data berikut!</h5>
                        <form action="" method="post">
                            <h5 class="mt-3">Data bencana:</h5>
                            <hr>
                            <label for="insiden">Insiden</label>
                            <select class="form-select" aria-label="Default select example" id="insiden" name="insiden">
                                <option selected>Pilih insiden</option>
                                <?php for ($a=0; $a < count($data); $a++) : ?>
                                    <option value="<?= $data[$a]["id"] ?>"><?= $data[$a]["bencana"] . " | " . $data[$a]["daerah"] . " (" . $data[$a]["tgl"] . ")" ?></option>
                                <?php endfor; ?>
                            </select>
                            <h5>Lokasi Evakuasi:</h5>
                            <span class=" text-muted">Alamat sebelumnya: <?= $va["address"] . ", " . $va["desa"] . ", " . $va["kecamatan"] . ", " . $va["kabten"] . ", " . $va["vinsi"] ?></span>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="provinsi">Provinsi</label>
                                    <select class="form-select" aria-label="Default select example" id="provinsi" name="provinsi">
                                        <!-- <option selected>Pilih Provinsi</option> -->
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="kabupaten">Kabupaten/Kota</label>
                                    <select class="form-select" aria-label="Default select example" id="kabupaten" name="kabupaten">
                                    <option value="<?= $va["idkab"] ?>" selected><?= $va["kabten"] ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="kecamatan">Kecamatan</label>
                                    <select class="form-select" aria-label="Default select example" id="kecamatan" name="kecamatan">
                                    <option value="<?= $va["idkec"] ?>" selected><?= $va["kecamatan"] ?></option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="desa">Desa</label>
                                    <select class="form-select" aria-label="Default select example" id="desa" name="desa">
                                    <option value="<?= $va["district"] ?>" selected><?= $va["desa"] ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md">
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Kampung/Jalan">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="rt" name="rt" placeholder="RT">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="rw" name="rw" placeholder="RW">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <span class="text-muted">Koordinat (Klik tombol di bawah atau pilih di peta)</span>
                                <div class="col-4">
                                    <button class="btn btn-primary" onclick="getLokasi()">Lokasi saat ini</button>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="titu" name="titu" placeholder="Latitude">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="ngitu" name="ngitu" placeholder="Longitude">
                                </div>
                            </div>
                            <h5>Data Evakuasi:</h5>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <input type="text" name="laki" id="laki" class="form-control" placeholder="Banyak Laki-laki">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="waniita" id="waniita" class="form-control" placeholder="Banyak Perempuan">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="ortua" id="ortua" class="form-control" placeholder="Banyak Orang Tua">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="anak" id="anak" class="form-control" placeholder="Banyak Anak-anak">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="buham" id="buham" class="form-control" placeholder="Banyak Ibu Hamil">
                                </div>
                            </div>
                            <input type="hidden" class="form-control" id="id" name="id" value="<?= $va["id"] ?>">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                                <a class="btn btn-primary me-md-2" href="data_evacuation.php">Cancel</a>
                                <button class="btn btn-primary" type="submit" name="submit">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- bootstrap js -->
        <script src="http://localhost/gis_bencana/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        
        <script type="text/javascript">
            $(document).ready(function(){
                $.ajax({
                    type: 'POST',
                    url: "get_provinsi.php",
                    cache: false,
                    success: function(msg){
                        $("#provinsi").html(msg);
                    }
                });
                $("#provinsi").change(function(){
                    var provinsi = $("#provinsi").val();
                    $.ajax({
                        type: 'POST',
                        url: "get_kabupaten.php",
                        data: {provinsi: provinsi},
                        cache: false,
                        success: function(msg){
                            $("#kabupaten").html(msg);
                        }
                    });
                });
                $("#kabupaten").change(function(){
                    var kabupaten = $("#kabupaten").val();
                    $.ajax({
                        type: 'POST',
                        url: "get_kecamatan.php",
                        data: {kabupaten: kabupaten},
                        cache: false,
                        success: function(msg){
                            $("#kecamatan").html(msg);
                        }
                    });
                });
                $("#kecamatan").change(function(){
                    var kecamatan = $("#kecamatan").val();
                    $.ajax({
                        type: 'POST',
                        url: "get_desa.php",
                        data: {kecamatan: kecamatan},
                        cache: false,
                        success: function(msg){
                            $("#desa").html(msg);
                        }
                    });
                });

            });
        </script>
    </body>
</html>