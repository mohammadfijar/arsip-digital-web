<?php
session_start();
include '../config/koneksi.php';

// ✅ Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

// ✅ Ambil ID arsip dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID arsip tidak valid.");
}
$id = intval($_GET['id']);

// ✅ Ambil data arsip berdasarkan ID
$query = mysqli_query($koneksi, "SELECT * FROM arsip WHERE arsip_id = '$id' LIMIT 1");
if (!$query || mysqli_num_rows($query) == 0) {
    die("Arsip tidak ditemukan.");
}
$arsip = mysqli_fetch_assoc($query);

// ✅ Ambil nama file dan path
$nama_file = $arsip['nama_file'];
$file_path = "../upload/" . $nama_file;

// ✅ Cek apakah file ada di folder upload
if (!file_exists($file_path)) {
    die("File tidak ditemukan di server.");
}

// ✅ Catat aktivitas unduh ke log_akses
$current_user_id = $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
$uid_for_insert = is_numeric($current_user_id) ? intval($current_user_id) : "NULL";
$judul = mysqli_real_escape_string($koneksi, $arsip['judul']);
$keterangan = mysqli_real_escape_string($koneksi, "Mengunduh arsip: " . $judul);

$log_sql = "INSERT INTO log_akses (user_id, arsip_id, aksi, keterangan) VALUES (" .
           ($uid_for_insert === "NULL" ? "NULL" : $uid_for_insert) . ", '$id', 'unduh', '$keterangan')";
@mysqli_query($koneksi, $log_sql); // pakai @ supaya tidak error kalau log gagal

// ✅ Kirim header untuk memulai download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($nama_file) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));

// ✅ Baca dan kirim file ke browser
readfile($file_path);
exit();
?>
