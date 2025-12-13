<?php
session_start();
include '../config/koneksi.php';

// Cek login dan role admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['id_user']; // pastikan session ini ada di login

// Cek apakah ada ID arsip yang dikirim
if (!isset($_GET['id'])) {
    header("Location: kelola_arsip.php");
    exit();
}
$arsip_id = intval($_GET['id']);

// Ambil data arsip berdasarkan ID
$query = mysqli_query($koneksi, "SELECT * FROM arsip WHERE arsip_id = '$arsip_id'");
if (!$query) {
    die("❌ Query gagal: " . mysqli_error($koneksi));
}
$arsip = mysqli_fetch_assoc($query);
if (!$arsip) {
    header("Location: kelola_arsip.php?msg=notfound");
    exit();
}

// Proses update status arsip
if (isset($_POST['update_status'])) {
    $status_arsip_baru = mysqli_real_escape_string($koneksi, $_POST['status_arsip']);
    $status_arsip_lama = $arsip['status_arsip'];
    $judul_arsip = mysqli_real_escape_string($koneksi, $arsip['judul']);

    $update = mysqli_query($koneksi, "
        UPDATE arsip 
        SET status_arsip = '$status_arsip_baru'
        WHERE arsip_id = '$arsip_id'
    ");

    if ($update) {
        // Tambahkan ke log_akses
        $aksi = 'edit';
        $keterangan = "Admin mengubah status arsip '$judul_arsip' dari '$status_arsip_lama' menjadi '$status_arsip_baru'";
        $keterangan = mysqli_real_escape_string($koneksi, $keterangan);
        $waktu = date('Y-m-d H:i:s');

        mysqli_query($koneksi, "
            INSERT INTO log_akses (user_id, arsip_id, aksi, keterangan, waktu)
            VALUES ('$user_id', '$arsip_id', '$aksi', '$keterangan', '$waktu')
        ");

        header("Location: kelola_arsip.php?msg=status_updated");
        exit();
    } else {
        $error = "❌ Gagal memperbarui status arsip.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Arsip - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f6fa; }
        .card { border-radius: 12px; }
        .form-control[readonly], .form-select[disabled] {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<?php include '../include/header.php'; ?>
<?php include '../include/sidebar.php'; ?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h4>✏️ Validasi Arsip</h4>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>

            <form action="" method="post">
                <div class="mb-3">
                    <label>Nomor Arsip</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($arsip['nomor_arsip']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label>Judul Arsip</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($arsip['judul']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea class="form-control" rows="4" readonly><?= htmlspecialchars($arsip['deskripsi']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label>Kategori Arsip</label>
                    <?php
                    $kategori = mysqli_query($koneksi, "SELECT nama_kategori FROM kategori WHERE kategori_id = '{$arsip['kategori_id']}'");
                    $kat = mysqli_fetch_assoc($kategori);
                    ?>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($kat['nama_kategori'] ?? 'Tidak diketahui'); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label>File Arsip</label><br>
                    <a href="../upload/<?= htmlspecialchars($arsip['nama_file']); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                        📂 Lihat File: <?= htmlspecialchars($arsip['nama_file']); ?>
                    </a>
                </div>

                <div class="mb-3">
                    <label>Status Arsip <span class="text-danger">*</span></label>
                    <select name="status_arsip" class="form-select" required>
                        <option value="baru" <?= ($arsip['status_arsip'] == 'baru') ? 'selected' : ''; ?>>Baru</option>
                        <option value="diperiksa" <?= ($arsip['status_arsip'] == 'diperiksa') ? 'selected' : ''; ?>>Diperiksa</option>
                        <option value="disetujui" <?= ($arsip['status_arsip'] == 'disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                        <option value="ditolak" <?= ($arsip['status_arsip'] == 'ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                    </select>
                </div>

                <div class="text-end">
                    <a href="kelola_arsip.php" class="btn btn-secondary">Kembali</a>
                    <button type="submit" name="update_status" class="btn btn-warning">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../include/footer.php'; ?>

</body>
</html>
