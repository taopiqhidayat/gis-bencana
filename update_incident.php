<?php
    session_start();
    require 'function.php';

    if (!isset($_SESSION["signin"])) {
        header("Location: login.php");
        exit;
    }

    $id = $_GET["id"];
    $qque = mysqli_query($knk, "SELECT * FROM disaster WHERE id = '$id'");
    while($dt = mysqli_fetch_assoc($qque)){
        $va = [
            "id" => $dt["id"],
            "insiden" => $dt["incident"],
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
            "wafat" => $dt["meninggal"],
            "luka" => $dt["luka"],
            "hilang" => $dt["hilang"],
            "bangunan" => $dt["bangunan"],
            "rumah" => $dt["rumah"],
            "lahan" => $dt["lahan"],
            "kerugian" => $dt["kerugian"],
            "tgl" => $dt["tgl"]
        ];
    }
    
    // koneksi ke database dan ambil data
    $query = mysqli_query($knk, "SELECT kata as bencana FROM stopwords_bencana ORDER BY kata ASC");
    $data = array();
    $x = 0;
    
    // masukan data ke sebuah array, tambah data titik yang sudah dibulatkan nilainya
    while($rw = mysqli_fetch_assoc($query)) {
        $data[$x] = [
            "bencana" => $rw["bencana"]
        ];
        $x++;
    }

    if (isset($_POST["ubah"])) {
        if (ubahIncident($_POST) > 0) {
            echo "<script>
                    alert('Anda telah Berhasil mengubah data bencana!');
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
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="favicon.ico">
        <link rel="canonical" href="https://getbootstrap.com/docs/3.3/examples/jumbotron-narrow/">

        <title>Update Incident</title>

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

        <!-- Leaflet css -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
        integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
        crossorigin=""/>
        
        
        <!-- Leaflet js -->
        <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
        integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
        crossorigin=""></script>
    </head>
    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="card col-md-6">
                    <div class="card-body">
                        <h2 class="card-title mb-3"><b>INCIDENT FORM</b></h2>
                        <h5 class="card-subtitle mb-5 text-muted">Isilah semua data berikut!</h5>
                        <form action="" method="post">
                            <h5 class="mt-3">Data bencana:</h5>
                            <hr>
                            <label for="insiden">Insiden</label>
                            <select class="form-select" aria-label="Default select example" id="insiden" name="insiden" required>
                                <option value="<?= $va["insiden"] ?>" selected><?= $va["insiden"] ?></option>
                                <?php for ($a=0; $a < count($data); $a++) : ?>
                                <option value="<?= $data[$a]["bencana"] ?>"><?= $data[$a]["bencana"] ?></option>
                                <?php endfor; ?>
                            </select>
                            <h6 class="mt-3">Lokasi Bencana:</h6>
                            <span class=" text-muted">Alamat sebelumnya: <?= $va["address"] . ", " . $va["desa"] . ", " . $va["kecamatan"] . ", " . $va["kabten"] . ", " . $va["vinsi"] ?></span>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="provinsi">Provinsi</label>
                                    <select class="form-select" aria-label="Default select example" id="provinsi" name="provinsi" onchange="chgvins()">
                                    <!-- <option value="<?= $va["idvin"] ?>" selected><?= $va["vinsi"] ?></option> -->
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
                                <div class="col-5">
                                    <button type="button" class="btn btn-primary" onclick="getLokasi()"> Ubah Lokasi saat ini</button>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="titud" name="titu" value="<?= $va["titu"] ?>">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="ngitud" name="ngitu" value="<?= $va["ngitu"] ?>">
                                </div>
                            </div>
                            <!-- <?php if(isset($_SESSION["signin"]) && $_SESSION["user"] != 4) : ?> -->
                                <h5 class="mt-3">Korban dan Kerugian</h5>
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <label for="wafat" class=" form-label">Meninggal</label>
                                        <input type="text" class="form-control" id="wafat" name="wafat" value="<?= $va["wafat"] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="luka" class=" form-label">Luka-luka</label>
                                        <input type="text" class="form-control" id="luka" name="luka" value="<?= $va["luka"] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="hilang" class=" form-label">Hilang</label>
                                        <input type="text" class="form-control" id="hilang" name="hilang" value="<?= $va["hilang"] ?>">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <label for="bangunan" class=" form-label">Bangunan</label>
                                        <input type="text" class="form-control" id="bangunan" name="bangunan" value="<?= $va["bangunan"] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="rumah" class=" form-label">Rumah</label>
                                        <input type="text" class="form-control" id="rumah" name="rumah" value="<?= $va["rumah"] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="lahan" class=" form-label">Lahan</label>
                                        <input type="text" class="form-control" id="lahan" name="lahan" value="<?= $va["lahan"] ?>">
                                    </div>
                                    <div class="col-md-4 mt-2">
                                        <label for="kerugian" class=" form-label">Kerugian</label>
                                        <input type="text" class="form-control" id="kerugian" name="kerugian" value="<?= $va["kerugian"] ?>">
                                    </div>
                                </div>
                            <!-- <?php endif; ?> -->
                            <input type="hidden" class="form-control" id="id" name="id" value="<?= $va["id"] ?>">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                                <a class="btn btn-primary me-md-2" href="data_incident.php">Cancel</a>
                                <button class="btn btn-primary" type="submit" name="ubah">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- bootstrap js -->
        <script src="http://localhost/gis_bencana/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

        <script>
            var titu = document.getElementById("titud");
            var ngitu = document.getElementById("ngitud");
            
            function getLokasi() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                } else { 
                    alert("Geolocation is not supported by this browser.");
                }
                }

                function showPosition(position) {
                    titu.value = position.coords.latitude;
                    ngitu.value = position.coords.longitude;
                }
        </script>

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