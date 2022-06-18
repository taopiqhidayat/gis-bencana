<?php
    session_start();
    require 'function.php';

    // if (!isset($_SESSION["signin"])) {
    //     header("Location: login.php");
    //     exit;
    // }

    // koneksi ke database dan ambil data
    $query = mysqli_query($knk, "SELECT incident as bencana, district as daerah, latitude as titu, longitude as ngitu, meninggal, luka, hilang, bangunan, rumah, kerugian, tgl FROM disaster ORDER BY latitude ASC");
    $data = array();
    $x = 0;

    // masukan data ke sebuah array, tambah data titik yang sudah dibulatkan nilainya
    while($rw = mysqli_fetch_assoc($query)) {
        if ($rw["titu"] != null || $rw["ngitu"] != null) {
            $data[$x] = [
                "bencana" => $rw["bencana"],
                "daerah" => $rw["daerah"],
                "titu" => $rw["titu"],
                "ngitu" => $rw["ngitu"],
                "titubu" => cekBilangan($rw["titu"]),
                "ngitubu" => cekBilangan($rw["ngitu"]),
                "wafat" => $rw["meninggal"],
                "luka" => $rw["luka"],
                "hilang" => $rw["hilang"],
                "bangunan" => $rw["bangunan"],
                "rumah" => $rw["rumah"],
                "kerugian" => $rw["kerugian"],
                "tgl" => $rw["tgl"]
            ];
            $x++;
        }
    }
    for ($u=0; $u < count($data); $u++) { 
        $data[$u]["daerah"] = getKab($data[$u]["daerah"]);
    }
    $titik = null;
    $area = null;

    // memisahkan titik yang sama/serupa dengan yang tidak
    for ($a=0; $a < count($data); $a++) { 
        // $qq = mysqli_query($knk, "SELECT * FROM disaster WHERE  ORDER BY latitude ASC");
        $cek = cekTitubu($data[$a]["titubu"], $data);
        $cek2 = cekNgitubu($data[$a]["ngitubu"], $data);

        $urti = $data[$a]["titubu"];
        $urngi = $data[$a]["ngitubu"];
        if (count($cek) >= 3) {
            // polygon marker
            $areas[$urti] = $cek;
        } elseif (count($cek2) >= 3) {
            // polygon marker
            $areas[$urngi] = $cek2;
        } else {
            // pointer marker
            $titik[] = $data[$a];
        }
    }
    // var_dump($titik);
    // var_dump($areas);

    // memisahkan bencana yang sama dengan yang tidak
    foreach ($areas as $rea) {
        for ($t=0; $t < count($rea); $t++) { 
            $ini = cekBencana($rea[$t]["bencana"], $rea);
        
            $urben = $rea[$t]["titubu"];
            if (count($ini) >= 3) {
                $area[$urben] = $ini;
            } else {
                $titik[] = $rea[$t];
            }
        }
    }
    // var_dump($titik);
    // var_dump($area);

    $urut_titu = array();
    $urut_ngitu = array();
    $ut = array();
    $ung = array();
    $v = 0;
    foreach ($area as $are) {
        // var_dump(count($are));
        $urut_titu[] = geturti($are);
        $urut_ngitu[] = geturngi($are);
    }
    // var_dump($urut_titu);
    // var_dump($urut_ngitu);
    
    for ($h=0; $h < count($urut_titu); $h++) { 
        $jum = array_sum($urut_titu[$h]);
        $ttti[] = $jum / count($urut_titu[$h]);
        $maxti[] = max($urut_titu[$h]);
    }
    for ($g=0; $g < count($urut_ngitu); $g++) { 
        $jum = array_sum($urut_ngitu[$g]);
        $ttngi[] = $jum / count($urut_ngitu[$g]);
        $maxngi[] = max($urut_ngitu[$g]);
    }
    // var_dump($ttti);
    // var_dump($ttngi);
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
        
        <title>SPL GIS</title>

        <!-- Bootstrap core CSS -->
        <link href="http://localhost/gis_bencana/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
        <link href="https://cdn.datatables.net/1.12.0/css/dataTables.bootstrap5.min.css">

        <!-- Desain/Styling web -->
        <link rel="stylesheet" href="desain.css">

        

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
                                    <!-- <a class="btn btn-primary" href="register.php">Register</a> -->
                                    <a class="btn btn-success" href="login.php">Login</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="row">
                <div class="col-lg-9">
                    <div id="map"></div>
                </div>
                <div class="col-md-3">
                    <div class="col-sm-12">                            
                        <div class="card">                                  
                            <div class="card-header">
                                <h5>Data Bencana Terbaru</h5>
                            </div>                              
                            <?php foreach ($data as $dt) : ?>
                            <div class="card-body">
                                <h6 class="card-title"><?= $dt["bencana"] . " di " . $dt["daerah"] ?></h6>
                                <p class="card-text"><b>Tanggal:</b> <?= $dt["tgl"] ?></p>
                                <button type="button" class="btn btn-primary btn-sm btn-block">Detail</button>
                                <hr> 
                            </div>                     
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
                
                <!-- Evacuation Contents -->
                <div class="col-md-6">
                    <h3>Data Evakuasi</h3>
                    <!-- <div class="card">
                        <div class="card-body" style="max-height: 600px;"> -->

                        <!-- </div>
                    </div> -->
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

        <!-- script map -->
        <script type="text/javascript">

            const map = L.map('map').setView([-1.5127367,119.2098285], 4);

            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoidGFvcGlxOTgiLCJhIjoiY2wzNTJucGJ2MGk2bjNrcXo4MTgzMWFmZyJ9.L-sqHG-KDrJUkqyuHchCcA', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 18,
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                accessToken: 'your.mapbox.access.token'
            }).addTo(map);

            const redIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            const titikBencana = <?= json_encode($titik, JSON_PRETTY_PRINT) ?>;

            for(let i = 0; i < titikBencana.length; i++)
            {
                const popup = '<strong>Bencana: </strong>' + titikBencana[i].bencana
                            + '<br>'+
                            '<strong>Lokasi: </strong>' + <?php  ?>titikBencana[i].daerah
                            /*+ '<br>'+
                            '<strong>Meninggal </strong>' + titikBencana[i].Meninggal
                            + '<br>'+
                            '<strong>Kerugian </strong>' + titikBencana[i].Kerugian*/;
                
                const marker = L.marker([titikBencana[i]["titu"],titikBencana[i]["ngitu"]],{icon: redIcon}).bindPopup(popup).addTo(map);
                const circle = L.circle([titikBencana[i]["titu"],titikBencana[i]["ngitu"]],{color: 'red', fillColor: '#f03', fillOpacity: 0.5, radius: 300}).bindPopup(popup).addTo(map);
            }
            
            var polygon = []
            var a = 0;
            <?php
            foreach ($area as $rea) :
            ?>
                polygon[a] = L.polygon([
                    <?php
                    for ($u=0; $u < count($rea); $u++) { 
                        if ($u==count($rea)-1) {
                            echo "[" . $rea[$u]["titu"] . "," . $rea[$u]["ngitu"] . "]";
                        } else {
                            echo "[" . $rea[$u]["titu"] . "," . $rea[$u]["ngitu"] . "],";
                        }
                    }
                    ?>
                ], {color: 'red'}).addTo(map);
                a++;
            <?php
            endforeach;
            ?>

            const areaBencana = <?= json_encode($area, JSON_PRETTY_PRINT) ?>;
            for (let e = 0; e < areaBencana.length; e++) {
                const area = areaBencana[e];
                for (let u = 0; u < area.length; u++) {
                    const element = area[u];
                    console.log(element);
                    
                }
            }

            const tiTeti = <?= json_encode($ttti, JSON_PRETTY_PRINT) ?>;
            const tiTengi = <?= json_encode($ttngi, JSON_PRETTY_PRINT) ?>;
            const Maxti = <?= json_encode($maxti, JSON_PRETTY_PRINT) ?>;
            const Maxngi = <?= json_encode($maxngi, JSON_PRETTY_PRINT) ?>;

            for(let i = 0; i < tiTeti.length; i++)
            {
                var distance;
                var radius;
                var mk1;
                var mk2;

                mk1 = [tiTeti[i],tiTengi[i]];
                mk2 = [Maxti[i],Maxngi[i]];
                distance = haversine_distance(mk1,mk2);
                radius = distance.toFixed(16) * 1609.3444444444444444;
                console.log(radius);
                const markertt = L.marker([tiTeti[i],tiTengi[i]],{icon: redIcon}).addTo(map);
                const circlett = L.circle([tiTeti[i],tiTengi[i]],{color: 'green', fillColor: '#0f3', fillOpacity: 0.3, radius: radius}).addTo(map);
            }

            function haversine_distance(mk1, mk2){
                var R = 3958.8;
                var rlat1 = mk1[0]*(Math.PI/180);
                var rlat2 = mk2[0]*(Math.PI/180);
                var difflat = rlat2 - rlat1;
                var difflon = (mk2[1]-mk1[1]) * (Math.PI/180);
                var d = 2 * R * Math.asin(Math.sqrt(Math.sin(difflat/2)*Math.sin(difflat/2)+Math.cos(rlat1)*Math.cos(rlat2)*Math.sin(difflon/2)*Math.sin(difflon/2)))
                return d;
            }

            L.control.scale().addTo(map);

            function onMapClick(e) {
                alert("You clicked the map at " + e.latlng);
            }

            map.on('click', onMapClick);
        </script>
    </body>
</html>