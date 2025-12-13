<?php
session_start();
include '../config/koneksi.php';

// ✅ Cek login & role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// =============================
// 📌 Tambah User
// =============================
if (isset($_POST['tambah'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    $check = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $alert = "⚠️ Username sudah terdaftar!";
    } else {
        $sql = "INSERT INTO users (username, password, nama_lengkap, email, role) 
                VALUES ('$username', '$password', '$nama_lengkap', '$email', '$role')";
        mysqli_query($koneksi, $sql);
        $alert = "✅ User baru berhasil ditambahkan!";
    }
}

// =============================
// 📌 Hapus User
// =============================
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM users WHERE user_id='$id'");
    $alert = "🗑️ User berhasil dihapus!";
}

// =============================
// 📌 Update User
// =============================
if (isset($_POST['update'])) {
    $id = intval($_POST['user_id']);
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    // Jika password diisi, update juga password
    if (!empty($_POST['password'])) {
        $password = mysqli_real_escape_string($koneksi, $_POST['password']);

        $sql = "UPDATE users SET nama_lengkap='$nama_lengkap', email='$email', role='$role', password='$password' WHERE user_id='$id'";
    } else {
        $sql = "UPDATE users SET nama_lengkap='$nama_lengkap', email='$email', role='$role' WHERE user_id='$id'";
    }

    mysqli_query($koneksi, $sql);
    $alert = "✏️ Data user berhasil diperbarui!";
}

// =============================
// 📊 Ambil semua user
// =============================
$users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY created_at DESC");

include '../include/header.php';
include '../include/sidebar.php';
?>
<!-- ✅ Custom Style -->
<style>
    body {
        background-color: #0f172a;
        font-family: 'Segoe UI', sans-serif;
    }

    h3.mb-3 {
        font-weight: 700;
        color: #ffffffff;
        border-left: 5px solid #0d6efd;
        padding-left: 10px;
    }

    /* 📦 Card */
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .card-header {
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* 📋 Form styling */
    label {
        font-weight: 500;
        color: #495057;
    }
    input.form-control, select.form-control {
        border-radius: 10px;
        border: 1px solid #ced4da;
        transition: all 0.2s;
    }
    input.form-control:focus, select.form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.25);
    }
    button.btn-success {
        border-radius: 8px;
        font-weight: 600;
        padding: 10px 20px;
    }

    /* 📊 Tabel pengguna */
    table.table {
        border-radius: 15px;
        overflow: hidden;
    }
    thead.table-dark {
        background: linear-gradient(90deg, #0d6efd, #0b5ed7);
    }
    thead.table-dark th {
        border: none;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
    }
    tbody tr:hover {
        background: #f1f3f5;
        transition: 0.2s;
    }

    /* 🏷️ Badge Role */
    .badge.bg-info {
        background: linear-gradient(135deg, #0dcaf0, #0bbcd6);
        color: #fff;
        font-size: 0.85rem;
        padding: 6px 10px;
        border-radius: 20px;
    }

    /* 🛠️ Tombol aksi */
    .btn-sm {
        border-radius: 6px;
        font-weight: 500;
    }
    .btn-warning.btn-sm {
        color: #fff;
    }
    .btn-danger.btn-sm:hover {
        background: #dc3545;
        color: #fff;
    }

    /* 🪄 Modal styling */
    .modal-content {
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
    }
    .modal-header.bg-warning {
        background: linear-gradient(90deg, #ffc107, #e0a800);
        color: #fff;
    }
    .modal-title {
        font-weight: 600;
    }
</style>

<div class="container mt-4">
    <h3 class="mb-3">👤 Kelola Pengguna Sistem</h3>

    <?php if (isset($alert)): ?>
        <div class="alert alert-info"><?= $alert; ?></div>
    <?php endif; ?>

    <!-- ✅ Form Tambah User -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">+ Tambah Pengguna Baru</div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Peran (Role)</label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Pilih Peran --</option>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                            <option value="pimpinan">Pimpinan</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="tambah" class="btn btn-success">💾 Simpan</button>
            </form>
        </div>
    </div>

    <!-- 📊 Tabel Daftar User -->
    <div class="card">
        <div class="card-header bg-secondary text-white">📋 Daftar Pengguna</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Peran</th>
                        <th>Dibuat</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($row = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td><span class="badge bg-info"><?= ucfirst($row['role']); ?></span></td>
                            <td><?= date('d-m-Y H:i', strtotime($row['created_at'])); ?></td>
                            <td>
                                <!-- ✏️ Edit -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['user_id']; ?>">Edit</button>

                                <!-- 🗑️ Hapus -->
                                <a href="?hapus=<?= $row['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus user ini?');">Hapus</a>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editModal<?= $row['user_id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">✏️ Edit Pengguna</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="user_id" value="<?= $row['user_id']; ?>">
                                            <div class="mb-3">
                                                <label>Nama Lengkap</label>
                                                <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($row['nama_lengkap']); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label>Peran</label>
                                                <select name="role" class="form-control">
                                                    <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                                    <option value="staff" <?= $row['role'] == 'staff' ? 'selected' : '' ?>>Staff</option>
                                                    <option value="pimpinan" <?= $row['role'] == 'pimpinan' ? 'selected' : '' ?>>Pimpinan</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label>Password (kosongkan jika tidak diubah)</label>
                                                <input type="password" name="password" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="update" class="btn btn-primary">💾 Simpan Perubahan</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
<?php include '../include/footer.php'; ?>
