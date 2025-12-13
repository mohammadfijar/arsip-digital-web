<?php
include '../config/koneksi.php';
session_start();

// Hanya bisa diakses oleh pimpinan
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pimpinan') {
    header("Location: ../login.php");
    exit;
}

// Proses ubah status arsip
if (isset($_POST['ubah_status'])) {
    $arsip_id = $_POST['arsip_id'];
    $status = $_POST['status_arsip'];

    $update = "UPDATE arsip SET status_arsip='$status' WHERE arsip_id='$arsip_id'";
    mysqli_query($koneksi, $update);
    $pesan = "✅ Status arsip berhasil diperbarui menjadi <b>" . ucfirst($status) . "</b>.";
}

// Ambil data arsip
$query = "SELECT a.*, u.nama_lengkap, k.nama_kategori 
          FROM arsip a
          LEFT JOIN users u ON a.uploaded_by = u.user_id
          LEFT JOIN kategori k ON a.kategori_id = k.kategori_id
          ORDER BY a.tanggal_upload DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Verifikasi Arsip - Pimpinan</title>
  <link rel="icon" href="../img/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #0f172a, #1e293b);
      color: #e2e8f0;
      min-height: 100vh;
    }
    .card {
      background-color: #1e293b;
      border: none;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }
    .table {
      background-color: #0f172a;
      color: #f1f5f9;
    }
    .table thead {
      background-color: #1e293b;
    }
    .table-hover tbody tr:hover {
      background-color: #334155;
      transition: 0.2s;
    }
    .btn-primary {
      background-color: #2563eb;
      border: none;
    }
    .btn-primary:hover {
      background-color: #1d4ed8;
    }
    select.form-select {
      background-color: #1e293b;
      color: #f8fafc;
      border: 1px solid #334155;
    }
    select.form-select:focus {
      border-color: #2563eb;
      box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }
  </style>
</head>
<body>
<?php include 'navbar_pimpinan.php'; ?>

<div class="container mt-4">
  <div class="card p-4">
    <h3 class="mb-4 text-info"><i class="bi bi-check2-square"></i> Verifikasi Arsip</h3>

    <?php if (!empty($pesan)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $pesan ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-hover align-middle text-center">
        <thead>
          <tr>
            <th>No</th>
            <th>Nomor Arsip</th>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Uploader</th>
            <th>Tanggal Upload</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          while ($row = mysqli_fetch_assoc($result)):
          ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nomor_arsip']) ?></td>
            <td><?= htmlspecialchars($row['judul']) ?></td>
            <td><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></td>
            <td><?= date('d-m-Y H:i', strtotime($row['tanggal_upload'])) ?></td>
            <td>
              <?php
              $badge = [
                'baru' => 'warning',
                'diperiksa' => 'info',
                'disetujui' => 'success',
                'ditolak' => 'danger'
              ];
              ?>
              <span class="badge bg-<?= $badge[$row['status_arsip']] ?? 'secondary' ?>">
                <?= ucfirst($row['status_arsip']) ?>
              </span>
            </td>
            <td>
              <form method="POST" class="d-inline">
                <input type="hidden" name="arsip_id" value="<?= $row['arsip_id'] ?>">
                <select name="status_arsip" class="form-select form-select-sm d-inline-block" style="width: 150px;">
                  <option value="baru" <?= $row['status_arsip']=='baru'?'selected':'' ?>>Baru</option>
                  <option value="diperiksa" <?= $row['status_arsip']=='diperiksa'?'selected':'' ?>>Diperiksa</option>
                  <option value="disetujui" <?= $row['status_arsip']=='disetujui'?'selected':'' ?>>Disetujui</option>
                  <option value="ditolak" <?= $row['status_arsip']=='ditolak'?'selected':'' ?>>Ditolak</option>
                </select>
                <button type="submit" name="ubah_status" class="btn btn-primary btn-sm mt-1">
                  <i class="bi bi-arrow-repeat"></i> Ubah
                </button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
