<?php
session_start();
include '../config/koneksi.php';

// ✅ Cek login
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

// ============================================
// 📌 Tambah Kategori
// ============================================
if (isset($_POST['tambah'])) {
    $nama_kategori = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    if (!empty($nama_kategori)) {
        $sql = "INSERT INTO kategori (nama_kategori, deskripsi) VALUES ('$nama_kategori', '$deskripsi')";
        mysqli_query($koneksi, $sql);
        $alert = "✅ Kategori berhasil ditambahkan!";
    } else {
        $alert = "⚠️ Nama kategori tidak boleh kosong!";
    }
}

// ============================================
// 📌 Hapus Kategori
// ============================================
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $sql = "DELETE FROM kategori WHERE kategori_id = '$id'";
    mysqli_query($koneksi, $sql);
    $alert = "🗑️ Kategori berhasil dihapus!";
}

// ============================================
// 📌 Edit Kategori
// ============================================
if (isset($_POST['update'])) {
    $id = intval($_POST['kategori_id']);
    $nama_kategori = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    $sql = "UPDATE kategori SET nama_kategori='$nama_kategori', deskripsi='$deskripsi' WHERE kategori_id='$id'";
    mysqli_query($koneksi, $sql);
    $alert = "✏️ Kategori berhasil diperbarui!";
}

// ============================================
// 📊 Ambil semua kategori
// ============================================
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY kategori_id DESC");

include '../include/header.php';
include '../include/sidebar.php';
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body {
        background: #f0f4f8;
        font-family: 'Segoe UI', sans-serif;
    }
    h3 {
        font-weight: bold;
        color: #333;
    }
    .card {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .card-header {
        font-size: 1.1rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .table thead {
        background: linear-gradient(90deg, #343a40, #495057);
        color: #fff;
    }
    .table tbody tr:hover {
        background: #f1f7ff;
    }
    .btn {
        border-radius: 8px;
        font-weight: 500;
    }
    .btn-warning {
        color: #fff;
    }
    .modal-content {
        border-radius: 16px;
    }
    .form-label {
        font-weight: 500;
    }
    .modal-content .modal-header.bg-warning {
    margin-top: -20px; /* geser bidang kuning ke atas */
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
    padding-top: 0.6rem;
    padding-bottom: 0.6rem;
}

</style>

<div class="container mt-5">
    <h3 class="mb-4"><i class="bi bi-folder2"></i> Kelola Kategori Arsip</h3>

    <?php if (isset($alert)) : ?>
        <div class="alert alert-info shadow-sm"><?= $alert; ?></div>
    <?php endif; ?>

    <!-- ✅ Form Tambah Kategori -->
    <div class="card mb-5">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-plus-circle"></i> Tambah Kategori Baru
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Surat Keuangan" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" placeholder="Deskripsi kategori..."></textarea>
                </div>
                <button type="submit" name="tambah" class="btn btn-success">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>

    <!-- 📊 Tabel Daftar Kategori -->
    <div class="card">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-collection"></i> Daftar Kategori</span>
            <span class="badge bg-light text-dark"><?= mysqli_num_rows($kategori); ?> total</span>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr class="text-center">
                        <th width="5%">#</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Tanggal Dibuat</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; 
                    while ($row = mysqli_fetch_assoc($kategori)) : 
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td><i class="bi bi-folder-fill text-primary"></i> <?= htmlspecialchars($row['nama_kategori']); ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                            <td class="text-center"><?= date('d-m-Y H:i', strtotime($row['created_at'])); ?></td>
                            <td class="text-center">
                                <!-- Tombol Edit (Modal) -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['kategori_id']; ?>">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>

                                <!-- Tombol Hapus -->
                                <a href="?hapus=<?= $row['kategori_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kategori ini?');">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editModal<?= $row['kategori_id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Kategori</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="kategori_id" value="<?= $row['kategori_id']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Kategori</label>
                                                <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($row['nama_kategori']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control"><?= htmlspecialchars($row['deskripsi']); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="update" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include '../include/footer.php'; ?>