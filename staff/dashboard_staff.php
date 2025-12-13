<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/koneksi.php';
?>

<?php include '../include/header.php'; ?>
<?php include '../include/sidebar.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-speedometer2"></i> Dashboard Staff</h2>
        <span class="badge bg-primary fs-6">👤 <?= $_SESSION['username']; ?></span>
    </div>

    <!-- 👋 Sambutan -->
    <div class="alert alert-primary border-0 shadow-sm" style="background-color: #1e293b; color: #f1f5f9;">
        <h5 class="mb-0">Halo, <strong><?= $_SESSION['username']; ?></strong> 👋</h5>
        <p class="mb-0">Selamat datang di panel staff. Kelola arsip digital dengan cepat dan efisien.</p>
    </div>

    <?php
    $id_user = $_SESSION['id_user'];

    // Statistik ringkas
    $totalArsip = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip WHERE uploaded_by = '$id_user'"))['total'];
    $diperiksa  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip WHERE status_arsip = 'diperiksa' AND uploaded_by = '$id_user'"))['total'];
    $disetujui  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip WHERE status_arsip = 'disetujui' AND uploaded_by = '$id_user'"))['total'];
    $ditolak    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip WHERE status_arsip = 'ditolak' AND uploaded_by = '$id_user'"))['total'];

    // Arsip terbaru + join kategori biar tampil nama kategori
    $arsipTerbaru = mysqli_query($koneksi, "
        SELECT a.*, k.nama_kategori 
        FROM arsip a 
        LEFT JOIN kategori k ON a.kategori_id = k.kategori_id 
        WHERE a.uploaded_by = '$id_user' 
        ORDER BY a.tanggal_upload DESC 
        LIMIT 5
    ");
    ?>

    <!-- 📊 Statistik Arsip -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm" style="background-color: #1e293b; color: #f1f5f9;">
                <div class="card-body">
                    <h5>Total Arsip</h5>
                    <h2 class="fw-bold text-primary"><?= $totalArsip; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm" style="background-color: #1e293b; color: #f1f5f9;">
                <div class="card-body">
                    <h5>Diperiksa</h5>
                    <h2 class="fw-bold text-warning"><?= $diperiksa; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm" style="background-color: #1e293b; color: #f1f5f9;">
                <div class="card-body">
                    <h5>Disetujui</h5>
                    <h2 class="fw-bold text-success"><?= $disetujui; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm" style="background-color: #1e293b; color: #f1f5f9;">
                <div class="card-body">
                    <h5>Ditolak</h5>
                    <h2 class="fw-bold text-danger"><?= $ditolak; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- 🗂️ Arsip Terbaru -->
    <div class="card shadow-sm border-0 mb-4" style="background-color: #1e293b; color: #f1f5f9;">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-clock-history"></i> Arsip Terbaru Anda</h4>
            <a href="upload_arsip.php" class="btn btn-sm btn-primary"><i class="bi bi-upload"></i> Upload Baru</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nomor Arsip</th>
                            <th>Judul Arsip</th>
                            <th>Kategori</th>
                            <th>Tanggal Upload</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($arsipTerbaru) > 0) {
                            while ($row = mysqli_fetch_assoc($arsipTerbaru)) { 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['nomor_arsip']; ?></td>
                            <td><?= $row['judul']; ?></td>
                            <td><?= $row['nama_kategori'] ?? '-'; ?></td>
                            <td><?= date("d M Y", strtotime($row['tanggal_upload'])); ?></td>
                            <td>
    <?php if ($row['status_arsip'] == 'disetujui') { ?>
        <span class="badge bg-success">Disetujui</span>
    <?php } elseif ($row['status_arsip'] == 'ditolak') { ?>
        <span class="badge bg-danger">Ditolak</span>
    <?php } elseif ($row['status_arsip'] == 'diperiksa') { ?>
        <span class="badge bg-info text-dark">Diperiksa</span>
    <?php } else { ?>
        <span class="badge bg-warning text-dark">Baru</span>
    <?php } ?>
</td>
                        </tr>
                        <?php 
                            } 
                        } else { 
                        ?>
                        <tr>
                            <td colspan="6">Belum ada arsip yang diunggah.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 🔗 Navigasi Cepat -->
    <div class="row g-4">
        <div class="col-md-4">
            <a href="upload_arsip.php" class="text-decoration-none">
                <div class="card text-center shadow-sm border-0 py-4" style="background-color: #1e293b; color: #f1f5f9;">
                    <i class="bi bi-upload fs-1 text-primary"></i>
                    <h5 class="mt-3">Upload Arsip</h5>
                </div>
            </a>
        </div>
        <div class="col-md-4">
           <a href="edit_arsip_staff.php" class="text-decoration-none">
                <div class="card text-center shadow-sm border-0 py-4" style="background-color: #1e293b; color: #f1f5f9;">
                    <i class="bi bi-pencil-square fs-1 text-success"></i>
                    <h5 class="mt-3">Edit Arsip</h5>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="lihat_status.php" class="text-decoration-none">
                <div class="card text-center shadow-sm border-0 py-4" style="background-color: #1e293b; color: #f1f5f9;">
                    <i class="bi bi-info-circle fs-1 text-warning"></i>
                    <h5 class="mt-3">Lihat Status Arsip</h5>
                </div>
            </a>
        </div>
    </div>
</div>

<?php include '../include/footer.php'; ?>
