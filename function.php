<?php

date_default_timezone_set("Asia/Jakarta");

// koneksi ke database dan ambil data
$server = 'localhost';
$username = 'root';
$passw = '';
$db = 'gis';
$knk = mysqli_connect($server, $username, $passw, $db);

function register($data){
    global $knk;

    // ambil data dari form yang sudah disubmit
    $sebagai = $data["sebagai"];
    $nama = ucwords(htmlspecialchars($data["nama"]));
    $nik = htmlspecialchars($data["nik"]);
    $email = htmlspecialchars($data["email"]);
    $nohp = htmlspecialchars($data["nohp"]);
    $nip = htmlspecialchars($data["nip"]);
    $jabatan = htmlspecialchars($data["jabatan"]);
    $username = strtolower(stripslashes(htmlspecialchars($data["username"])));
    $password = mysqli_real_escape_string($knk, $data["password"]);
    $cnf_password = mysqli_real_escape_string($knk, $data["cnf_password"]);

    // validasi
    if ($sebagai != 1 && $sebagai != 2 && $sebagai != 3 && $sebagai != 4) {
        echo "<script>
                alert('Anda harus memilih registrasi sebagai siapa!, Siapakah diri Anda?');
            </script>";
            return false;
    }
    if (!preg_match("/^[a-zA-Z]*$/", $nama)) {
        echo "<script>
                alert('Nama tidak boleh mengandung angka atau karakter lain, Nama hanya boleh mengandung huruf!');
            </script>";
            return false;
    }
    if (!preg_match("/^[0-9]*$/", $nik)) {
        echo "<script>
        alert('NIK tidak boleh mengandung huruf atau karakter lain, NIK hanya boleh mengandung angka!');
        </script>";
        return false;
    }
    if (strlen($nik) != 16) {
        echo "<script>
        alert('NIK memiliki 16 digit angka, silahkan masukkan NIK dengan benar!');
        </script>";
        return false;
    }
    // Valid email
    function valid_email($email){
        return !filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    if (valid_email($email)) {
        echo "<script>
        alert('Email tidak sesuai (valid), silahkan masukkan email yang benar!');
        </script>";
        return false;
    }
    if (strlen($email) < 6 || strlen($email) > 30) {
        echo "<script>
        alert('Email memiliki 6-30 digit, silahkan masukkan email yang benar!');
        </script>";
        return false;
    }
    // akhir valid email
    if (!preg_match("/^[0-9]*$/", $nohp)) {
        echo "<script>
        alert('No HP tidak boleh mengandung huruf atau karakter lain, No HP hanya boleh mengandung angka!');
        </script>";
        return false;
    }
    if (strlen($nohp) < 10 || strlen($nohp) > 14) {
        echo "<script>
        alert('No HP memiliki 10-14 digit, silahkan masukkan No HP dengan benar!');
        </script>";
        return false;
    }
    if (strlen($nip) != 0) {
        if (!preg_match("/^[0-9]*$/", $nip)) {
            echo "<script>
            alert('NIP tidak boleh mengandung huruf atau karakter lain, NIP hanya boleh mengandung angka!');
            </script>";
            return false;
        }
        if (strlen($nip) < 8 || strlen($nip) > 18) {
            echo "<script>
            alert('NIP memiliki 8-18 digit, silahkan masukkan NIP dengan benar!');
            </script>";
            return false;
        }
    }
    if (strlen($jabatan) != 0) {
        if (!preg_match("/^[a-zA-Z]*$/", $jabatan)) {
            echo "<script>
            alert('Jabatan tidak boleh mengandung angka atau karakter lain, Jabatan hanya boleh mengandung huruf!');
            </script>";
            return false;
        }
    }
    
    // cek username
    $cek = mysqli_query($knk, "SELECT username FROM users WHERE username = '$username'");
    
    if ( mysqli_fetch_assoc($cek) ) {
        echo "<script>
        alert('Username sudah digunakan, silahkan masukkan username lain!');
        </script>";
        return false;
    }
    
    // validasi dan cek password
    $ucase = preg_match('@[A-Z]@',$password);
    $wrcase = preg_match('@[a-z]@',$password);
    $number = preg_match('@[0-9]@',$password);
    $schar = preg_match('@[^\w]@',$password);

    if (strlen($password) < 6 || strlen($password) > 16) {
        echo "<script>
        alert('Password memiliki 8-16 digit, silahkan buat Password dengan benar!');
        </script>";
        return false;
    }
    if (!$ucase || !$wrcase || !$number || !$schar) {
        echo "<script>
        alert('Password setidaknya harus memiliki 1 huruf besar, huruf kecil, angka dan sepesial karakter!');
        </script>";
        return false;
    }

    if ($password !== $cnf_password) {
        echo "<script>
                alert('Password dan Konfirmasi Password tidak sesuai!');
            </script>";
            return false;
    }

    // enkripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // tanggal
    $date = date('Y-m-d H:i:s');

    // insert ke database
    if ($sebagai == 1) {
        mysqli_query($knk, "INSERT INTO users VALUES('', '$nama', '$nik', '$nohp', '$email', '$nip', '$jabatan', '$username', '$password', '1', '', '', '', 1, '$date', '$date')");
    } elseif ($sebagai == 2) {
        mysqli_query($knk, "INSERT INTO users VALUES('', '$nama', '$nik', '$nohp', '$email', '$nip', '$jabatan', '$username', '$password', '', '1', '', '', 1, '$date', '$date')");
    } elseif ($sebagai == 3) {
        mysqli_query($knk, "INSERT INTO users VALUES('', '$nama', '$nik', '$nohp', '$email', '$nip', '$jabatan', '$username', '$password', '', '', '1', '', 1, '$date', '$date')");
    } elseif ($sebagai == 4) {
        mysqli_query($knk, "INSERT INTO users VALUES('', '$nama', '$nik', '$nohp', '$email', '$nip', '$jabatan', '$username', '$password', '', '', '', '1', 1, '$date', '$date')");
    }

    return mysqli_affected_rows($knk);
}

// pembulatan nilai
function cekBilangan($bilangan)
{
    if ($bilangan < 0) {
        return round($bilangan, 1, PHP_ROUND_HALF_DOWN);
    } else {
        return round($bilangan, 1, PHP_ROUND_HALF_DOWN);
    }
}

// mencari titik yang sama dari titik Latitude
function cekTitubu($titubu, $array)
{
    for ($x=0; $x < count($array); $x++) { 
        $ceki[$x] = in_array($titubu, $array[$x]);
        if ($ceki[$x] === true) {
            $ret[] = $array[$x];
        }
    }
    return $ret;
}

// mencari titik yang sama dari titik Longitude
function cekNgitubu($ngitubu, $array)
{
    for ($x=0; $x < count($array); $x++) { 
        $ceki[$x] = in_array($ngitubu, $array[$x]);
        if ($ceki[$x] === true) {
            $ret[] = $array[$x];
        }
    }
    return $ret;
}

// mencari dan mengelompokkan bencana yang sama
function cekBencana($bencana, $array)
{
    for ($r=0; $r < count($array); $r++) { 
        $ceka[$r] = in_array($bencana, $array[$r]);
        if ($ceka[$r] === true) {
            $ret[] = $array[$r];
        }
    }
    return $ret;
}

function geturti($arrr) {
    for ($k=0; $k < count($arrr); $k++) { 
        $get[] = $arrr[$k]["titu"];
    }
    return $get;
}
function geturngi($arrr) {
    for ($k=0; $k < count($arrr); $k++) { 
        $get[] = $arrr[$k]["ngitu"];
    }
    return $get;
}

function setAktif($data)
{
    global $knk;
    $id = $data["id"];
    $active = 1;
    $date = date('Y-m-d H:i:s');
    $query = mysqli_query($knk, "UPDATE users SET active = '$active', updated_at = '$date' WHERE id = '$id'");

    return mysqli_affected_rows($knk);
}
function setBlokir($data)
{
    global $knk;
    $id = $data["id"];
    $active = 0;
    $date = date('Y-m-d H:i:s');
    $query = mysqli_query($knk, "UPDATE users SET active = '$active', updated_at = '$date' WHERE id = '$id'");

    return mysqli_affected_rows($knk);
}

function getNama($id)
{
    global $knk;
    $query = mysqli_query($knk, "SELECT nama FROM users WHERE id = '$id'");
    $ret = mysqli_fetch_row($query);
    return $ret[0];
}
function getNIK($id)
{
    global $knk;
    $query = mysqli_query($knk, "SELECT nik FROM users WHERE id = '$id'");
    $ret = mysqli_fetch_row($query);
    return $ret[0];
}
function getNohp($id)
{
    global $knk;
    $query = mysqli_query($knk, "SELECT no_hp FROM users WHERE id = '$id'");
    $ret = mysqli_fetch_row($query);
    return $ret[0];
}

function getBencana($id)
{
    global $knk;
    $query = mysqli_query($knk, "SELECT * FROM disaster WHERE id = '$id'");
    $data = mysqli_fetch_row($query);
    $ret = $data[0]["incident"];
    return $ret;
}

function terReIncident($id)
{
    global $knk;
    $id = $id["id"];
    $ubah = mysqli_query($knk, "UPDATE reports_disaster SET status_cnf = '1' WHERE id = '$id'");
    $query = mysqli_query($knk, "SELECT * FROM reports_disaster WHERE id = '$id'");
    $data = mysqli_fetch_assoc($query);
    $id_user = $data["id_user"];
    $insiden = $data["incident"];
    $address = $data["address"];
    $district = $data["district"];
    $titu = $data["latitude"];
    $ngitu = $data["longitude"];
    $date = $data["tanggal"];
    $meninggal = $data["wafat"];
    $luka = $data["luka"];
    $hilang = $data["hilang"];
    $bangunan = $data["bangunan"];
    $rumah = $data["rumah"];
    $lahan = $data["hilang"];
    $kerugian = $data["hilang"];
    $query = mysqli_query($knk, "INSERT INTO disaster VALUES('', '$id_user', '$insiden', '$address', '$district', '$titu', '$ngitu','$meninggal','$luka','$hilang','$bangunan','$rumah','$lahan','$kerugian', '$date','')");
    
    
    return mysqli_affected_rows($knk);
}
function denyReIncident($id)
{
    global $knk;
    $id = $id["id"];
    $query = mysqli_query($knk, "UPDATE reports_disaster SET status_cnf = '0' WHERE id = '$id'");

    return mysqli_affected_rows($knk);
}

function terReEvac($id)
{
    global $knk;
    $id = $id["id"];
    $ubah = mysqli_query($knk, "UPDATE reports_evacuation SET status_cnf = '1' WHERE id = '$id'");
    $query = mysqli_query($knk, "SELECT * FROM reports_evacuation WHERE id = '$id'");
    $data = mysqli_fetch_assoc($query);
    $id_user = $data["id_user"];
    $insiden = $data["id_disaster"];
    $address = $data["address"];
    $district = $data["district"];
    $titu = $data["latitude"];
    $ngitu = $data["longitude"];
    $date = $data["tanggal"];
    $laki = $data["laki_laki"];
    $wanita = $data["wanita"];
    $ortu = $data["orang_tua"];
    $anak = $data["anak_anak"];
    $bumil = $data["ibu_hamil"];
    $jumlah = $data["jumlah"];
    $query = mysqli_query($knk, "INSERT INTO evacuation VALUES('', '$id_user', '$insiden', '$address', '$district', '$titu', '$ngitu','','','','', '$laki', '$wanita', '$anak', '$ortu', '$bumil', '$jumlah', '$date')");
    
    return mysqli_affected_rows($knk);
}
function denyReEvac($id)
{
    global $knk;
    $id = $id["id"];
    $query = mysqli_query($knk, "UPDATE reports_evacuation SET status_cnf = '0' WHERE id = '$id'");

    return mysqli_affected_rows($knk);
}

function reIncident($data){
    global $knk;

    // get data
    $user_id = $_SESSION["id_user"];
    $insiden = htmlspecialchars($data["insiden"]);
    $address = htmlspecialchars($data["address"]) . " " . htmlspecialchars($data["rt"]) . "/" . htmlspecialchars($data["rw"]);
    $district = htmlspecialchars($data["desa"]);
    $titu = htmlspecialchars($data["titu"]);
    $ngitu = htmlspecialchars($data["ngitu"]);
    $wafat = htmlspecialchars($data["wafat"]);
    $luka = htmlspecialchars($data["luka"]);
    $hilang = htmlspecialchars($data["hilang"]);
    $bangunan = htmlspecialchars($data["bangunan"]);
    $rumah = htmlspecialchars($data["rumah"]);
    $lahan = htmlspecialchars($data["lahan"]);
    $kerugian = htmlspecialchars($data["kerugian"]);
    $date = date('Y-m-d H:i:s');

    $query = mysqli_query($knk, "INSERT INTO reports_disaster VALUES('', '$user_id', '$insiden', '$address', '$district', '$titu', '$ngitu', '$date', '$wafat','$luka','$hilang','$bangunan','$rumah','$lahan','$kerugian', '2')");
    
    return mysqli_affected_rows($knk);
}
function ubahIncident($data){
    global $knk;

    // get data
    $id = $data["id"];
    $user_id = $_SESSION["id_user"];
    $insiden = $data["insiden"];
    $address = htmlspecialchars($data["address"]) . " " . htmlspecialchars($data["rt"]) . "/" . htmlspecialchars($data["rw"]);
    $district = $data["desa"];
    $titu = htmlspecialchars($data["titu"]);
    $ngitu = htmlspecialchars($data["ngitu"]);
    $wafat = htmlspecialchars($data["wafat"]);
    $luka = htmlspecialchars($data["luka"]);
    $hilang = htmlspecialchars($data["hilang"]);
    $bangunan = htmlspecialchars($data["bangunan"]);
    $rumah = htmlspecialchars($data["rumah"]);
    $lahan = htmlspecialchars($data["lahan"]);
    $kerugian = htmlspecialchars($data["kerugian"]);

    $query = mysqli_query($knk, "UPDATE disaster SET user_id = '$user_id', incident = '$insiden', location = '$address', district = '$district', latitude = '$titu', longitude = '$ngitu', meninggal = '$wafat', luka = '$luka', hilang = '$hilang', bangunan = '$bangunan', rumah = '$rumah', lahan ='$lahan', kerugian = '$kerugian' WHERE id = '$id'");
    
    return mysqli_affected_rows($knk);
}

function reEvac($data){
    global $knk;
    
    // get data
    $id_user = $_SESSION["id_user"];
    $insiden = htmlspecialchars($data["insiden"]);
    $address = htmlspecialchars($data["address"]) . htmlspecialchars($data["rt"]) . "/" . htmlspecialchars($data["rw"]) .
                htmlspecialchars($data["desa"]) . " " . 
                htmlspecialchars($data["kecamatan"]) . " " . 
                htmlspecialchars($data["kabupaten"]) . " " . 
                htmlspecialchars($data["provinsi"]);
    $district = htmlspecialchars($data["kabupaten"]);
    $titu = htmlspecialchars($data["titu"]);
    $ngitu = htmlspecialchars($data["ngitu"]);
    $date = date('Y-m-d H:i:s');
    $laki = htmlspecialchars($data["laki"]);
    $wanita = htmlspecialchars($data["wanita"]);
    $ortu = htmlspecialchars($data["ortu"]);
    $anak = htmlspecialchars($data["anak"]);
    $bumil = htmlspecialchars($data["buham"]);
    $jumlah = $laki + $wanita;
    
    $query = mysqli_query($knk, "INSERT INTO reports_evacuation VALUES('', '$id_user', '$insiden', '$address', '$district', '$titu', '$ngitu', '$date', '$laki', '$wanita', '$anak', '$ortu', '$bumil', '$jumlah', '2')");
    return mysqli_affected_rows($knk);
}
function ubahEvac($data){
    global $knk;
    
    // get data
    $id = $data["id"];
    $user_id = $_SESSION["id_user"];
    $insiden = htmlspecialchars($data["insiden"]);
    $address = htmlspecialchars($data["address"]) . htmlspecialchars($data["rt"]) . "/" . htmlspecialchars($data["rw"]) .
                htmlspecialchars($data["desa"]) . " " . 
                htmlspecialchars($data["kecamatan"]) . " " . 
                htmlspecialchars($data["kabupaten"]) . " " . 
                htmlspecialchars($data["provinsi"]);
    $district = htmlspecialchars($data["kabupaten"]);
    $titu = htmlspecialchars($data["titu"]);
    $ngitu = htmlspecialchars($data["ngitu"]);
    $laki = htmlspecialchars($data["laki"]);
    $wanita = htmlspecialchars($data["wanita"]);
    $ortu = htmlspecialchars($data["ortu"]);
    $anak = htmlspecialchars($data["anak"]);
    $bumil = htmlspecialchars($data["buham"]);
    $jumlah = $laki + $wanita;
    
    $query = mysqli_query($knk, "UPDATE evacuation SET user_id = '$user_id', id_disaster = '$insiden', location = '$address', district = '$district', latitude = '$titu', longitude = '$ngitu', laki = '$laki', wanita = '$wanita', anak = '$anak', ortu = '$ortu', bumil = '$bumil', jumlah = '$jumlah' WHERE id = '$id'");
    return mysqli_affected_rows($knk);
}

function getIDVinsi($id){
    global $knk;
    $getIdKec = mysqli_query($knk, "SELECT * FROM wilayah_desa WHERE id = '$id'");
    while ($dt1 = mysqli_fetch_assoc($getIdKec)) {
        $idKec = $dt1["kecamatan_id"];
        $getIdKab = mysqli_query($knk, "SELECT * FROM wilayah_kecamatan WHERE id = '$idKec'");
        while ($dt2 = mysqli_fetch_assoc($getIdKab)) {
            $idKab = $dt2["kabupaten_id"];
            $getIdVins = mysqli_query($knk, "SELECT * FROM wilayah_kabupaten WHERE id = '$idKab'");
            while ($dt3 = mysqli_fetch_assoc($getIdVins)){
                return $idVins = $dt3["provinsi_id"];
            }
        } 
    } 
}
function getIDKab($id){
    global $knk;
    $getIdKec = mysqli_query($knk, "SELECT * FROM wilayah_desa WHERE id = '$id'");
    while ($dt1 = mysqli_fetch_assoc($getIdKec)) {
        $idKec = $dt1["kecamatan_id"];
        $getIdKab = mysqli_query($knk, "SELECT * FROM wilayah_kecamatan WHERE id = '$idKec'");
        while ($dt2 = mysqli_fetch_assoc($getIdKab)) {
            return $idKab = $dt2["kabupaten_id"];
        } 
    } 
}
function getIDKec($id){
    global $knk;
    $getIdKec = mysqli_query($knk, "SELECT * FROM wilayah_desa WHERE id = '$id'");
    while ($dt = mysqli_fetch_assoc($getIdKec)) {
        return $idKec = $dt["kecamatan_id"];
    } 
}

function getVinsi($id){
    global $knk;
    $getIdKec = mysqli_query($knk, "SELECT * FROM wilayah_desa WHERE id = '$id'");
    while ($dt1 = mysqli_fetch_assoc($getIdKec)) {
        $idKec = $dt1["kecamatan_id"];
        $getIdKab = mysqli_query($knk, "SELECT * FROM wilayah_kecamatan WHERE id = '$idKec'");
        while ($dt2 = mysqli_fetch_assoc($getIdKab)) {
            $idKab = $dt2["kabupaten_id"];
            $getIdVins = mysqli_query($knk, "SELECT * FROM wilayah_kabupaten WHERE id = '$idKab'");
            while ($dt3 = mysqli_fetch_assoc($getIdVins)){
                $idVins = $dt3["provinsi_id"];
                $query = mysqli_query($knk, "SELECT * FROM wilayah_provinsi WHERE id = '$idVins'");
                while ($ret = mysqli_fetch_assoc($query)){
                    return $ret["nama"];
                }
            }
        } 
    } 
}
function getKab($id){
    global $knk;
    $getIdKec = mysqli_query($knk, "SELECT * FROM wilayah_desa WHERE id = '$id'");
    while ($dt1 = mysqli_fetch_assoc($getIdKec)) {
        $idKec = $dt1["kecamatan_id"];
        $getIdKab = mysqli_query($knk, "SELECT * FROM wilayah_kecamatan WHERE id = '$idKec'");
        while ($dt2 = mysqli_fetch_assoc($getIdKab)) {
            $idKab = $dt2["kabupaten_id"];
            $query = mysqli_query($knk, "SELECT * FROM wilayah_kabupaten WHERE id = '$idKab'");
            while ($ret = mysqli_fetch_assoc($query)) {
                return $ret["nama"];
            } 
        } 
    } 
}
function getKec($id){
    global $knk;
    $getIdKec = mysqli_query($knk, "SELECT * FROM wilayah_desa WHERE id = '$id'");
    while ($dt = mysqli_fetch_assoc($getIdKec)) {
        $idKec = $dt["kecamatan_id"];
        $query = mysqli_query($knk, "SELECT * FROM wilayah_kecamatan WHERE id = '$idKec'");
        while ($ret = mysqli_fetch_assoc($query)) {
            return $ret["nama"];
        } 
    } 
}
function getDes($id){
    global $knk;
    $query = mysqli_query($knk, "SELECT * FROM wilayah_desa WHERE id = $id");
    while  ($dt = mysqli_fetch_assoc($query)){
        $ret = $dt["nama"];
        return $ret;
    }
}