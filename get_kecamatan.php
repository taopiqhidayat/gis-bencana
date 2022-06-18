<?php
    require 'function.php';
    $kabupaten = $_POST["kabupaten"];
    echo "<option value=''>Pilih Kecamatan</option>";

    $query = mysqli_query($knk, "SELECT * FROM wilayah_kecamatan WHERE kabupaten_id = '$kabupaten' ORDER BY nama ASC");
    while($dt = mysqli_fetch_assoc($query)){
        echo "<option value='". $dt["id"] ."'>". $dt["nama"] ."</option>";
    }