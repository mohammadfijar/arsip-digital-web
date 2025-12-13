<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pimpinan') {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/koneksi.php';
?>

<?php include '../include/header.php'; ?>
<?php include '../include/sidebar.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-folder"></i> Akses Arsip</h2>
        <span class="badge bg-primary fs-6">📅 <?= date("l, d F Y"); ?></span>
    </div>

    <!-- 🔍 Form Pencarian Arsip -->
    <div class="card shadow-sm mb-4 border-0" style="background-color: #1e293b; color: #f1f5f9;">
        <div class="card-body">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="cari" class="form-control" placeholder="🔍 Cari berdasarkan judul atau kategori..." value="<?= isset($_GET['cari']) ? $_GET['cari'] : '' ?>">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                <a href="akses_arsip.php" class="btn btn-secondary"><i class="bi bi-arrow-repeat"></i> Reset</a>
            </form>
        </div>
    </div>

    <!-- 📂 Daftar Arsip -->
    <div class="card shadow-sm border-0" style="background-color: #1e293b; color: #f1f5f9;">
        <div class="card-header bg-transparent border-0">
            <h4 class="fw-bold"><i class="bi bi-archive"></i> Daftar Arsip</h4>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-dark table-hover text-center align-middle">
                <thead class="table-primary text-dark">
                    <tr>
                        <th>#</th>
                        <th>Judul Arsip</th>
                        <th>Kategori</th>
                        <th>Tanggal Upload</th>
                        <th>Diunggah Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $where = "";
                    if (isset($_GET['cari']) && $_GET['cari'] != '') {
                        $cari = mysqli_real_escape_string($koneksi, $_GET['cari']);
                        $where = "WHERE judul LIKE '%$cari%' OR kategori LIKE '%$cari%'";
                    }

                    $query = mysqli_query($koneksi, "SELECT a.*, u.username FROM arsip a 
                                                     LEFT JOIN users u ON a.uploaded_by = u.user_id 
                                                     $where ORDER BY a.tanggal_upload DESC");
                    // 🔍 Cek jika query gagal
if (!$query) {
    die("Query Error: " . mysqli_error($koneksi));
}
                    if (mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['judul']}</td>
                                    <td>{$row['kategori_id']}</td>
                                    <td>" . date('d-m-Y', strtotime($row['tanggal_upload'])) . "</td>
                                    <td>{$row['username']}</td>
                                    <td>
                                        <a href='../upload/{$row['nama_file']}' target='_blank' class='btn btn-sm btn-success'><i class='bi bi-eye'></i> Lihat</a>
                                        <a href='../upload/{$row['nama_file']}' download class='btn btn-sm btn-info'><i class='bi bi-download'></i> Unduh</a>
                                    </td>
                                  </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center text-muted'>📭 Tidak ada arsip ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../include/footer.php'; ?>
