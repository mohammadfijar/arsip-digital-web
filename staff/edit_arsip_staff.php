<?php
session_start();
include '../config/koneksi.php';

// Pastikan hanya staff yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'staff') {
    header("Location: ../auth/login.php");
    exit();
}

// Ambil ID arsip dari URL (jika ada)
$arsip_id = isset($_GET['arsip_id']) ? intval($_GET['arsip_id']) : 0;

// ======================
// MODE 1: Tidak ada ID → tampilkan daftar arsip
// ======================
if ($arsip_id == 0) {
    $result = mysqli_query($koneksi, "SELECT a.*, k.nama_kategori 
                                      FROM arsip a
                                      LEFT JOIN kategori k ON a.kategori_id = k.kategori_id
                                      ORDER BY a.tanggal_upload DESC");
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
      <meta charset="UTF-8">
      <title>Daftar Arsip</title>
      <link rel="icon" href="../img/logo.png" type="image/png">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    </head>
    <body class="bg-dark text-light">
    <div class="container py-4">
      <div class="card bg-secondary text-light p-4">
        <h3><i class="bi bi-folder"></i> Daftar Arsip</h3>
        <table class="table table-dark table-striped mt-3">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nomor Arsip</th>
              <th>Judul</th>
              <th>Kategori</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <td><?= $row['arsip_id'] ?></td>
                <td><?= htmlspecialchars($row['nomor_arsip']) ?></td>
                <td><?= htmlspecialchars($row['judul']) ?></td>
                <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                <td><?= htmlspecialchars($row['status_arsip']) ?></td>
                <td>
                  <a href="edit_arsip_staff.php?arsip_id=<?= $row['arsip_id'] ?>" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil-square"></i> Edit
                  </a>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <a href="dashboard_staff.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
      </div>
    </div>
    </body>
    </html>
    <?php
    exit();
}

// ======================
// MODE 2: Ada ID → tampilkan form edit arsip
// ======================
$query = "SELECT a.*, k.nama_kategori 
          FROM arsip a
          LEFT JOIN kategori k ON a.kategori_id = k.kategori_id
          WHERE a.arsip_id = '$arsip_id'";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("<div class='alert alert-danger'>❌ Query Error: " . mysqli_error($koneksi) . "</div>");
}

$arsip = mysqli_fetch_assoc($result);
if (!$arsip) {
    die("<div class='alert alert-danger text-center'>❌ Arsip tidak ditemukan.</div>");
}

// ======================
// PROSES UPDATE ARSIP
// ======================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $kategori_id = intval($_POST['kategori_id']);
    $user_id = $_SESSION['id_user'];
    $aksi = 'edit';
    $waktu = date('Y-m-d H:i:s');

 $update = mysqli_query($koneksi, "
    UPDATE arsip SET 
        judul = '$judul',
        deskripsi = '$deskripsi',
        kategori_id = '$kategori_id'
    WHERE arsip_id = '$arsip_id'
");

         if ($update) {
                // Catat ke log_akses
                $keterangan = "Mengedit arsip dan mengganti file: " . $judul;
                $keterangan = mysqli_real_escape_string($koneksi, $keterangan);
                $waktu = date('Y-m-d H:i:s');

                mysqli_query($koneksi, "
                    INSERT INTO log_akses (user_id, arsip_id, aksi, keterangan, waktu)
                    VALUES ('$user_id', '$arsip_id', 'edit', '$keterangan', '$waktu')
                ");

        $success = "✅ Arsip berhasil diperbarui dan dicatat di log akses.";
        $result = mysqli_query($koneksi, $query);
        $arsip = mysqli_fetch_assoc($result);
    } else {
        $error = "❌ Gagal memperbarui arsip: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Arsip</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

<div class="container py-4">
  <div class="card bg-secondary text-light p-4">
    <h3><i class="bi bi-pencil-square"></i> Edit Arsip</h3>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (!empty($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Nomor Arsip</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($arsip['nomor_arsip']) ?>" disabled>
        <small class="text-warning">⚠ Nomor arsip tidak dapat diubah.</small>
      </div>

      <div class="mb-3">
        <label class="form-label">Judul Arsip</label>
        <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($arsip['judul']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($arsip['deskripsi']) ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="kategori_id" class="form-select">
          <option value="">-- Pilih Kategori --</option>
          <?php
          $kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
          while ($k = mysqli_fetch_assoc($kategori)) {
              $selected = ($arsip['kategori_id'] == $k['kategori_id']) ? 'selected' : '';
              echo "<option value='{$k['kategori_id']}' $selected>{$k['nama_kategori']}</option>";
          }
          ?>
        </select>
      </div>

      <div class="text-end">
        <a href="edit_arsip_staff.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
