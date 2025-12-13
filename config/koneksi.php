<?php
// konfigurasi koneksi ke database
$host     = "localhost";
$user     = "root";
$password = ""; // kosongkan jika default XAMPP
$database = "arsip_digital"; // sesuaikan dengan nama database kamu

// membuat koneksi
$koneksi = mysqli_connect($host, $user, $password, $database);

// cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
