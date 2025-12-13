<?php
session_start();
include '../config/koneksi.php';

// Cek login dan role admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// --- Ambil data statistik utama ---
$total_users = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users"))['total'];
$total_arsip = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip"))['total'];
$total_kategori = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM kategori"))['total'];
$total_log = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM log_akses"))['total'];

// --- Tambahan statistik ---
$total_pending = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip WHERE status_arsip='pending'"))['total'];
$total_disetujui = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip WHERE status_arsip='disetujui'"))['total'];
$total_ditolak = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip WHERE status_arsip='ditolak'"))['total'];
$login_bulan = mysqli_fetch_assoc(mysqli_query($koneksi, "
  SELECT COUNT(*) AS total FROM log_akses 
  WHERE aksi='login' AND MONTH(waktu)=MONTH(CURDATE())
"))['total'];

// --- Ambil data grafik arsip per kategori ---
$data_kategori = mysqli_query($koneksi, "
  SELECT k.nama_kategori, COUNT(a.arsip_id) AS total
  FROM kategori k
  LEFT JOIN arsip a ON a.kategori_id = k.kategori_id
  GROUP BY k.nama_kategori
");
$labels = [];
$values = [];
while ($row = mysqli_fetch_assoc($data_kategori)) {
  $labels[] = $row['nama_kategori'];
  $values[] = $row['total'];
}

// --- Ambil log aktivitas terakhir ---
$logs_query = mysqli_query($koneksi, "
    SELECT l.*, u.username, a.judul 
    FROM log_akses l 
    LEFT JOIN users u ON l.user_id = u.user_id
    LEFT JOIN arsip a ON l.arsip_id = a.arsip_id
    ORDER BY l.waktu DESC LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - Sistem Arsip Digital</title>
  <link rel="icon" href="../img/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { background-color: #f5f6fa; font-family: 'Segoe UI', sans-serif; }
    .card {
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: 0.3s;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 14px rgba(0,0,0,0.12);
        cursor: pointer;
    }
    .log-list { font-size: 0.9rem; }
    .chart-container { height: 400px; }
  </style>
</head>
<body>

<?php include '../include/header.php'; ?>
<?php include '../include/sidebar.php'; ?>

<div class="container mt-4">
  <h3 class="mb-4 fw-bold">📊 Dashboard Admin</h3>

  <!-- Statistik Utama -->
  <div class="row g-4">

    <div class="col-md-3">
      <a href="kelola_user.php" style="text-decoration:none;">
        <div class="card bg-primary text-white text-center p-3">
          <h4><?= $total_users; ?></h4>
          <p>Pengguna Terdaftar</p>
        </div>
      </a>
    </div>

    <div class="col-md-3">
      <a href="kelola_arsip.php" style="text-decoration:none;">
        <div class="card bg-success text-white text-center p-3">
          <h4><?= $total_arsip; ?></h4>
          <p>Total Arsip</p>
        </div>
      </a>
    </div>

    <div class="col-md-3">
      <a href="kategori.php" style="text-decoration:none;">
        <div class="card bg-warning text-white text-center p-3">
          <h4><?= $total_kategori; ?></h4>
          <p>Kategori Arsip</p>
        </div>
      </a>
    </div>

    <div class="col-md-3">
      <a href="log_akses_admin.php" style="text-decoration:none;">
        <div class="card bg-danger text-white text-center p-3">
          <h4><?= $total_log; ?></h4>
          <p>Aktivitas Log</p>
        </div>
      </a>
    </div>
  </div>

  <!-- Statistik tambahan -->
  <div class="row g-4 mt-3">
    <div class="col-md-3">
      <a href="kelola_arsip.php" style="text-decoration:none;">
        <div class="card bg-info text-white text-center p-3">
          <h4><?= $total_pending; ?></h4>
          <p>Menunggu Persetujuan</p>
        </div>
      </a>
    </div>

    <div class="col-md-3">
      <a href="kelola_arsip.php?status=disetujui" style="text-decoration:none;">
        <div class="card bg-success text-white text-center p-3">
          <h4><?= $total_disetujui; ?></h4>
          <p>Arsip Disetujui</p>
        </div>
      </a>
    </div>

    <div class="col-md-3">
      <a href="kelola_arsip.php?status=ditolak" style="text-decoration:none;">
        <div class="card bg-danger text-white text-center p-3">
          <h4><?= $total_ditolak; ?></h4>
          <p>Arsip Ditolak</p>
        </div>
      </a>
    </div>

    <div class="col-md-3">
      <a href="log_akses_admin.php" style="text-decoration:none;">
        <div class="card bg-secondary text-white text-center p-3">
          <h4><?= $login_bulan; ?></h4>
          <p>Login Bulan Ini</p>
        </div>
      </a>
    </div>
  </div>

  <!-- Grafik -->
  <div class="card mt-5">
    <div class="card-header bg-dark text-white">
      <strong>📈 Statistik Arsip per Kategori</strong>
    </div>
    <div class="card-body chart-container">
      <canvas id="arsipChart"></canvas>
    </div>
  </div>

  <!-- Log aktivitas -->
  <div class="card mt-5 mb-5">
    <div class="card-header bg-dark text-white">
      <strong>🕒 5 Aktivitas Terbaru</strong>
    </div>
    <div class="card-body">
      <ul class="list-group log-list">
        <?php while ($log = mysqli_fetch_assoc($logs_query)): ?>
          <li class="list-group-item">
            <strong><?= htmlspecialchars($log['username'] ?? 'User Dihapus'); ?></strong>
            melakukan <b><?= htmlspecialchars($log['aksi']); ?></b> pada arsip:
            <em><?= htmlspecialchars($log['judul'] ?? '-'); ?></em>
            <span class="text-muted float-end"><?= date('d-m-Y H:i', strtotime($log['waktu'])); ?></span>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>
  </div>

</div>

<?php include '../include/footer.php'; ?>

<script>
const ctx = document.getElementById('arsipChart');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($labels) ?>,
    datasets: [{
      label: 'Jumlah Arsip',
      data: <?= json_encode($values) ?>,
      backgroundColor: [
        'rgba(54,162,235,0.7)',
        'rgba(255,206,86,0.7)',
        'rgba(75,192,192,0.7)',
        'rgba(255,99,132,0.7)'
      ],
      borderRadius: 6
    }]
  },
  options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>

</body>
</html>
