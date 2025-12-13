<?php
session_start();
include '../config/koneksi.php';

// Pastikan hanya staff yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'staff') {
    header("Location: ../auth/login.php");
    exit();
}

$id_staff = $_SESSION['id_user'];

// Ambil filter pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($koneksi, $_GET['status']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Query dasar
$query = "SELECT a.*, k.nama_kategori 
          FROM arsip a 
          LEFT JOIN kategori k ON a.kategori_id = k.kategori_id 
          WHERE a.uploaded_by = '$id_staff'";

// Filter pencarian
if (!empty($search)) {
    $query .= " AND (a.nomor_arsip LIKE '%$search%' OR a.judul LIKE '%$search%')";
}

// Filter status
if (!empty($status_filter)) {
    $query .= " AND a.status_arsip = '$status_filter'";
}

// Filter tanggal
if (!empty($start_date) && !empty($end_date)) {
    $query .= " AND DATE(a.tanggal_upload) BETWEEN '$start_date' AND '$end_date'";
}

$query .= " ORDER BY a.tanggal_upload DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lihat Status Arsip - Staff</title>
  <link rel="icon" href="../img/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
      body {
          background-color: #121212;
          color: #f1f1f1;
      }
      .card {
          background-color: #1e1e1e;
          border: none;
          border-radius: 15px;
          box-shadow: 0px 0px 15px rgba(0,0,0,0.5);
      }
      .table-dark {
          background-color: #222 !important;
      }
      .form-control, .form-select {
          background-color: #2a2a2a;
          color: #fff;
          border: 1px solid #444;
      }
      .form-control:focus, .form-select:focus {
          background-color: #333;
          border-color: #0d6efd;
          color: #fff;
          box-shadow: none;
      }
      .btn-primary {
          background-color: #0d6efd;
          border: none;
      }
      .btn-primary:hover {
          background-color: #0b5ed7;
      }
  </style>
</head>
<body>

<?php include '../include/header.php'; ?>
<?php include '../include/sidebar.php'; ?>

<div class="container-fluid py-4">
  <div class="card shadow-sm p-4">
    <h3 class="mb-4"><i class="bi bi-clipboard-check"></i> Lihat Status Arsip Saya</h3>

    <!-- 🔍 Filter Form -->
    <form method="GET" class="row g-3 mb-4">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Cari Judul atau Nomor Arsip..." value="<?= $search ?>">
      </div>
      <div class="col-md-2">
        <select name="status" class="form-select">
          <option value="">-- Semua Status --</option>
          <option value="baru" <?= ($status_filter == 'baru') ? 'selected' : '' ?>>Baru</option>
          <option value="diperiksa" <?= ($status_filter == 'diperiksa') ? 'selected' : '' ?>>Diperiksa</option>
          <option value="disetujui" <?= ($status_filter == 'disetujui') ? 'selected' : '' ?>>Disetujui</option>
          <option value="ditolak" <?= ($status_filter == 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
        </select>
      </div>
      <div class="col-md-2">
        <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
      </div>
      <div class="col-md-2">
        <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
      </div>
    </form>

    <!-- 📊 Tabel Status Arsip -->
    <div class="table-responsive">
      <table class="table table-dark table-hover text-center align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Nomor Arsip</th>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Tanggal Upload</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nomor_arsip']) ?></td>
                <td><?= htmlspecialchars($row['judul']) ?></td>
                <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                <td><?= date('d M Y', strtotime($row['tanggal_upload'])) ?></td>
                <td>
                  <?php if ($row['status_arsip'] == 'baru'): ?>
                      <span class="badge bg-secondary">Baru</span>
                  <?php elseif ($row['status_arsip'] == 'diperiksa'): ?>
                      <span class="badge bg-info text-dark">Diperiksa</span>
                  <?php elseif ($row['status_arsip'] == 'disetujui'): ?>
                      <span class="badge bg-success">Disetujui</span>
                  <?php else: ?>
                      <span class="badge bg-danger">Ditolak</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($row['nama_file'])): ?>
                      <a href="../upload/<?= $row['nama_file'] ?>" target="_blank" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye"></i> Lihat
                      </a>
                  <?php else: ?>
                      <span class="text-muted">Tidak ada file</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center text-muted">Tidak ada arsip ditemukan.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include '../include/footer.php'; ?>

</body>
</html>
