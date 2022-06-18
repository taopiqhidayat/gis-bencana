<?php
    require 'function.php';
    $kecamatan = $_POST["kecamatan"];
    echo "<option value=''>Pilih Desa</option>";

    $query = mysqli_query($knk, "SELECT * FROM wilayah_desa WHERE kecamatan_id = '$kecamatan' ORDER BY nama ASC");
    while($dt = mysqli_fetch_assoc($query)){
        echo "<option value='". $dt["id"] ."'>". $dt["nama"] ."</option>";
    }