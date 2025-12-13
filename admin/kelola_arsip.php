<?php
session_start();
include '../config/koneksi.php';

// Cek login dan role admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Proses Hapus Arsip
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $q = mysqli_query($koneksi, "SELECT nama_file FROM arsip WHERE arsip_id = '$id'");
    $file = mysqli_fetch_assoc($q);

    if ($file && file_exists("../upload/" . $file['nama_file'])) {
        unlink("../upload/" . $file['nama_file']); // hapus file fisik
    }
    mysqli_query($koneksi, "DELETE FROM arsip WHERE arsip_id = '$id'");
    header("Location: kelola_arsip.php?msg=deleted");
    exit();
}

// Ambil semua arsip
$arsip = mysqli_query($koneksi, "
    SELECT a.*, k.nama_kategori, u.username 
    FROM arsip a
    LEFT JOIN kategori k ON a.kategori_id = k.kategori_id
    LEFT JOIN users u ON a.uploaded_by = u.user_id
    ORDER BY a.tanggal_upload DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Arsip - Admin</title>
  <link rel="icon" href="../img/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
      body { background-color: #0f172a; }
      .table th { background: #0d6efd; color: #fff; }
  </style>
</head>
<body>

<?php include '../include/header.php'; ?>
<?php include '../include/sidebar.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>📂 Kelola Arsip</h2>
        <!-- <a href="tambah_arsip.php" class="btn btn-success">+ Tambah Arsip Baru</a> -->
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success">✅ Arsip berhasil dihapus.</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Nomor Arsip</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Uploader</th>
                    <th>Status</th>
                    <th>Tanggal Upload</th>
                    <th>File</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($arsip)): ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= $row['nomor_arsip']; ?></td>
                    <td><?= $row['judul']; ?></td>
                    <td><?= $row['nama_kategori'] ?? '-'; ?></td>
                    <td><?= $row['username'] ?? '-'; ?></td>
                    <td class="text-center">
                        <span class="badge 
                            <?php 
                                if ($row['status_arsip'] == 'baru') echo 'bg-secondary';
                                elseif ($row['status_arsip'] == 'diperiksa') echo 'bg-warning';
                                elseif ($row['status_arsip'] == 'disetujui') echo 'bg-success';
                                else echo 'bg-danger';
                            ?>">
                            <?= ucfirst($row['status_arsip']); ?>
                        </span>
                    </td>
                    <td><?= date('d-m-Y H:i', strtotime($row['tanggal_upload'])); ?></td>
                    <td>
                        <a href="../upload/<?= $row['nama_file']; ?>" target="_blank"><?= $row['nama_file']; ?></a>
                    </td>
                    <td class="text-center">
                        <a href="detail_arsip.php?id=<?= $row['arsip_id']; ?>" class="btn btn-info btn-sm">Detail</a>
                        <a href="edit_arsip.php?id=<?= $row['arsip_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="kelola_arsip.php?hapus=<?= $row['arsip_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus arsip ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../include/footer.php'; ?>

</body>
</html>