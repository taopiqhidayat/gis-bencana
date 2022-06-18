<?php
    require 'function.php';
    $provinsi = $_POST["provinsi"];
    echo "<option value=''>Pilih Kabupaten</option>";

    $query = mysqli_query($knk, "SELECT * FROM wilayah_kabupaten WHERE provinsi_id = '$provinsi' ORDER BY nama ASC");
    while($dt = mysqli_fetch_assoc($query)){
        echo "<option value='". $dt["id"] ."'>". $dt["nama"] ."</option>";
    }