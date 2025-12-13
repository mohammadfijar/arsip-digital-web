<?php
include '../config/koneksi.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pimpinan') {
    header("Location: ../login.php");
    exit;
}

// Ambil data log akses dari tabel log_akses
$query = "
SELECT 
    l.log_id,
    u.nama_lengkap AS user,
    a.judul AS arsip,
    l.aksi,
    l.keterangan,
    l.waktu
FROM log_akses l
LEFT JOIN users u ON l.user_id = u.user_id
LEFT JOIN arsip a ON l.arsip_id = a.arsip_id
ORDER BY l.waktu DESC
";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Riwayat Arsip - Pimpinan</title>
  <link rel="icon" href="../img/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #0f172a;
      color: #f8fafc;
      padding-top: 80px;
      font-family: 'Segoe UI', sans-serif;
    }
    .table-container {
      background-color: #1e293b;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 0 8px rgba(0,0,0,0.4);
    }
    table thead {
      background-color: #1e40af;
      color: white;
    }
    .badge-upload { background-color: #3b82f6; }
    .badge-edit { background-color: #10b981; }
    .badge-unduh { background-color: #f59e0b; }
    .badge-lihat { background-color: #6366f1; }
    .badge-hapus { background-color: #ef4444; }
    .badge-login { background-color: #14b8a6; }
    .badge-logout { background-color: #475569; }
  </style>
</head>
<body>

<?php include 'navbar_pimpinan.php'; ?>

<div class="container mt-4">
  <h3 class="mb-3 text-info">🕒 Riwayat Aktivitas Arsip</h3>

   <!-- ✅ Tombol reset ditambahkan di sini -->
                <form method="post" onsubmit="return confirm('Yakin ingin menghapus semua log aktivitas?');" class="m-0 p-0">
                    <button type="submit" name="reset_log" class="btn btn-danger btn-sm">
                        🔄 Reset Aktivitas
                    </button>
                </form>

  <div class="table-container">
    <table class="table table-striped table-dark table-hover align-middle">
      <thead class="text-center">
        <tr>
          <th>No</th>
          <th>Nama Pengguna</th>
          <th>Judul Arsip</th>
          <th>Aksi</th>
          <th>Keterangan</th>
          <th>Waktu</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)):
          $aksi = $row['aksi'];
          $badgeClass = match($aksi) {
              'upload' => 'badge-upload',
              'edit' => 'badge-edit',
              'unduh' => 'badge-unduh',
              'lihat' => 'badge-lihat',
              'hapus' => 'badge-hapus',
              'login' => 'badge-login',
              'logout' => 'badge-logout',
              default => 'bg-secondary'
          };
        ?>
        <tr>
          <td class="text-center"><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['user'] ?? '-') ?></td>
          <td><?= htmlspecialchars($row['arsip'] ?? '-') ?></td>
          <td class="text-center">
            <span class="badge <?= $badgeClass ?>"><?= ucfirst($aksi) ?></span>
          </td>
          <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
          <td class="text-center"><?= date('d-m-Y H:i:s', strtotime($row['waktu'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
