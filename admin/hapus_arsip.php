<?php
session_start();
include '../config/koneksi.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Pastikan ada ID arsip yang dikirim
if (!isset($_GET['id'])) {
    header("Location: kelola_arsip.php");
    exit();
}

$id_arsip = intval($_GET['id']);

// Ambil data arsip untuk dapatkan nama file-nya
$query = mysqli_query($koneksi, "SELECT nama_file FROM arsip WHERE id_arsip = '$id_arsip'");
$data = mysqli_fetch_assoc($query);

if ($data) {
    $file_path = "../upload/" . $data['nama_file'];

    // Hapus file dari folder upload jika ada
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Hapus data dari database
    $hapus = mysqli_query($koneksi, "DELETE FROM arsip WHERE id_arsip = '$id_arsip'");

    if ($hapus) {
        header("Location: kelola_arsip.php?msg=deleted");
        exit();
    } else {
        header("Location: kelola_arsip.php?msg=error");
        exit();
    }
} else {
    header("Location: kelola_arsip.php?msg=notfound");
    exit();
}
?>
