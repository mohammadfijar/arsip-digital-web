<?php
session_start();
include '../config/koneksi.php';

// Cek login dan role admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

/*
Pastikan tabel users memiliki salah satu kolom berikut:
  - user_id  ✅ (umum di sistem lama)
  - id       ✅ (umum di sistem baru)
  - username ✅ (jika tidak ada id numerik)
*/

// Coba join dengan kolom yang sesuai
$query = "
SELECT a.*, k.nama_kategori, u.username AS uploader_nama
FROM arsip a
LEFT JOIN kategori k ON a.kategori_id = k.kategori_id
LEFT JOIN users u ON a.uploaded_by = u.user_id  -- ubah ke u.id atau u.username jika perlu
ORDER BY a.tanggal_upload DESC";

$result = mysqli_query($koneksi, $query) or die(mysqli_error($koneksi));

// Ubah status arsip jika ada permintaan (POST)
if (isset($_POST['ubah_status'])) {
    $arsip_id = $_POST['arsip_id'];
    $status_baru = $_POST['status_arsip'];

    mysqli_query($koneksi, "UPDATE arsip SET status_arsip='$status_baru' WHERE arsip_id='$arsip_id'");
    echo "<script>alert('Status arsip berhasil diperbarui!');window.location='laporan.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Arsip - Admin</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard_admin.php">📂 Arsip Digital</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="laporan.php">Data Arsip</a></li>
        <li class="nav-item"><a class="nav-link" href="log_akses_admin.php">Riwayat</a></li>
        <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- CONTAINER -->
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>📁 Daftar Arsip</h4>
        <a href="export_arsip_pdf.php" class="btn btn-danger btn-sm">Ekspor ke PDF</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nomor Arsip</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Uploader</th>
                            <th>Tanggal Upload</th>
                            <th>Status</th>
                            <th>File</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nomor_arsip']); ?></td>
                            <td><?= htmlspecialchars($row['judul']); ?></td>
                            <td><?= htmlspecialchars($row['nama_kategori'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($row['uploader_nama'] ?? '-'); ?></td>
                            <td><?= date('d M Y', strtotime($row['tanggal_upload'])); ?></td>
                            <td>
                                <?php if ($row['status_arsip'] == 'disetujui'): ?>
                                    <span class="badge bg-success">Disetujui</span>
                                <?php elseif ($row['status_arsip'] == 'ditolak'): ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php elseif ($row['status_arsip'] == 'diperiksa'): ?>
                                    <span class="badge bg-warning text-dark">Diperiksa</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Menunggu</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="../uploads/<?= htmlspecialchars($row['nama_file']); ?>" class="btn btn-sm btn-primary" target="_blank">Lihat</a>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="arsip_id" value="<?= $row['arsip_id']; ?>">
                                    <select name="status_arsip" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                        <option value="">Pilih</option>
                                        <option value="disetujui" <?= $row['status_arsip'] == 'disetujui' ? 'selected' : ''; ?>>Disetujui</option>
                                        <option value="ditolak" <?= $row['status_arsip'] == 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                                        <option value="diperiksa" <?= $row['status_arsip'] == 'diperiksa' ? 'selected' : ''; ?>>Diperiksa</option>
                                    </select>
                                    <input type="hidden" name="ubah_status" value="1">
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">Belum ada arsip yang diunggah.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
