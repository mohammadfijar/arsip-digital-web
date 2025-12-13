<?php
session_start();
include '../config/koneksi.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Ambil daftar kategori untuk pilihan select
$kategori_query = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

// Proses simpan data saat form disubmit
if (isset($_POST['simpan'])) {
    //  echo "<pre>";
    // var_dump($_SESSION);
    // echo "</pre>";
    $nomor_arsip = mysqli_real_escape_string($koneksi, $_POST['nomor_arsip']);
    $judul        = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi    = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $kategori_id  = intval($_POST['kategori_id']);
    $uploaded_by  = $_SESSION['id_user'];

    // === Upload File ===
    $file = $_FILES['file_arsip']['name'];
    $tmp  = $_FILES['file_arsip']['tmp_name'];
    $ukuran = $_FILES['file_arsip']['size'];
    $ekstensi = pathinfo($file, PATHINFO_EXTENSION);

    // Validasi ekstensi
    $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'png'];
    if (!in_array(strtolower($ekstensi), $allowed)) {
        $error = "❌ Format file tidak diizinkan!";
    } else {
        // Rename file agar unik
        $nama_file = time() . '_' . $file;
        $tujuan = "../upload/" . $nama_file;

        if (move_uploaded_file($tmp, $tujuan)) {
            // Simpan ke database
            $query = "INSERT INTO arsip 
            (nomor_arsip, judul, deskripsi, kategori_id, nama_file, ukuran_file, ekstensi, uploaded_by, status_arsip) 
            VALUES 
            ('$nomor_arsip', '$judul', '$deskripsi', '$kategori_id', '$nama_file', '$ukuran', '$ekstensi', '$uploaded_by', 'baru')";
            $simpan = mysqli_query($koneksi, $query);

            if ($simpan) {
                header("Location: kelola_arsip.php?msg=added");
                exit();
            } else {
                $error = "❌ Gagal menyimpan data ke database: " . mysqli_error($koneksi);
            }
        } else {
            $error = "❌ Gagal mengunggah file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Arsip - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f6fa; }
        .card { border-radius: 12px; }
    </style>
</head>
<body>

<?php include '../include/header.php'; ?>
<?php include '../include/sidebar.php'; ?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h4>+ Tambah Arsip Baru</h4>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Nomor Arsip <span class="text-danger">*</span></label>
                    <input type="text" name="nomor_arsip" class="form-control" placeholder="Contoh: SK-2025-004" required>
                </div>
                <div class="mb-3">
                    <label>Judul Arsip <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" placeholder="Masukkan judul arsip" required>
                </div>
                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4" placeholder="Deskripsi arsip"></textarea>
                </div>
                <div class="mb-3">
                    <label>Kategori Arsip <span class="text-danger">*</span></label>
                    <select name="kategori_id" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($kat = mysqli_fetch_assoc($kategori_query)): ?>
                            <option value="<?= $kat['kategori_id']; ?>"><?= $kat['nama_kategori']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Upload File Arsip <span class="text-danger">*</span></label>
                    <input type="file" name="file_arsip" class="form-control" required>
                    <div class="form-text">Format yang diizinkan: pdf, doc, docx, xls, xlsx, jpg, png</div>
                </div>
                <div class="text-end">
                    <a href="kelola_arsip.php" class="btn btn-secondary">Kembali</a>
                    <button type="submit" name="simpan" class="btn btn-success">Simpan Arsip</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../include/footer.php'; ?>

</body>
</html>