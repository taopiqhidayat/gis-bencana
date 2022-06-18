<?php
    require 'function.php';
    echo "<option value=''>Pilih Provinsi</option>";

    $query = mysqli_query($knk, "SELECT * FROM wilayah_provinsi ORDER BY nama ASC");
    while($dt = mysqli_fetch_assoc($query)){
        echo "<option value='". $dt["id"] ."'>". $dt["nama"] ."</option>";
    }