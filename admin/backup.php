<?php
session_start();
include '../config/koneksi.php';

// ✅ Cek login & role admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

// 📦 Proses Backup
if (isset($_POST['backup'])) {
    $backup_dir = "../backup/";
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }

    $filename = "backup_" . date("Ymd_His") . ".sql";
    $file_path = $backup_dir . $filename;

    // Jalankan perintah mysqldump
    $command = "C:\\xampp\\mysql\\bin\\mysqldump --user=root --password= --host=localhost arsip_digital > \"$file_path\"";
    system($command, $output);

    if (file_exists($file_path)) {
        $message = "<div class='alert alert-success'>✅ Backup berhasil dibuat: <strong>$filename</strong></div>";

        // Simpan ke backup_log
        mysqli_query($koneksi, "INSERT INTO backup_log (admin_id, jenis, file_path, keterangan) 
            VALUES ('{$_SESSION['id_user']}', 'backup', '$file_path', 'Backup database berhasil dibuat')");
    } else {
        $message = "<div class='alert alert-danger'>❌ Gagal membuat backup. Periksa pengaturan mysqldump.</div>";
    }
}

// 🔄 Proses Restore
if (isset($_POST['restore'])) {
    if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] === 0) {
        $file_tmp = $_FILES['backup_file']['tmp_name'];
        $file_name = $_FILES['backup_file']['name'];

        $command = "C:\\xampp\\mysql\\bin\\mysql --user=root --password= --host=localhost arsip_digital < \"$file_tmp\"";
        system($command, $output);

        $message = "<div class='alert alert-success'>✅ Database berhasil direstore dari file <strong>$file_name</strong></div>";

        // Simpan ke backup_log
        mysqli_query($koneksi, "INSERT INTO backup_log (admin_id, jenis, file_path, keterangan) 
            VALUES ('{$_SESSION['id_user']}', 'restore', '$file_name', 'Restore database dari file backup')");
    } else {
        $message = "<div class='alert alert-danger'>❌ Gagal mengunggah file backup.</div>";
    }
}

// 📜 Ambil riwayat backup & restore
$riwayat = mysqli_query($koneksi, "
    SELECT b.*, u.username 
    FROM backup_log b
    LEFT JOIN users u ON b.admin_id = u.user_id
    ORDER BY b.waktu DESC
");

include '../include/header.php';
include '../include/sidebar.php';
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        background: #f4f6f9;
        font-family: 'Segoe UI', sans-serif;
    }
    .backup-card {
        border: none;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        overflow: hidden;
        animation: fadeIn 0.7s ease-in-out;
    }
    .backup-card .card-header {
        background: linear-gradient(90deg, #0d6efd, #0b5ed7);
        color: #fff;
        padding: 1.2rem 1.5rem;
        font-size: 1.4rem;
        font-weight: 600;
    }
    .backup-card .card-header i {
        margin-right: 10px;
    }
    .backup-section .card {
        border: none;
        border-radius: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
        background: #fff;
        box-shadow: 0 3px 10px rgba(0,0,0,0.06);
        padding: 2rem 1.5rem;
    }
    .backup-section .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }
    .backup-section i {
        font-size: 3.8rem;
        margin-bottom: 15px;
        transition: transform 0.3s;
    }
    .backup-section i:hover {
        transform: scale(1.15);
    }
    .backup-section h5 {
        font-weight: 600;
        margin-top: 10px;
        color: #343a40;
    }
    h5.section-title {
        font-weight: 600;
        font-size: 1.3rem;
        color: #343a40;
        margin-top: 2.5rem;
        border-left: 5px solid #0d6efd;
        padding-left: 12px;
    }
    tbody tr:hover {
        background: #f8faff;
        transform: scale(1.005);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="container-fluid py-4">
    <div class="backup-card card mb-4">
        <div class="card-header">
            <i class="bi bi-hdd-stack"></i> Backup & Restore Database
        </div>
        <div class="card-body">

            <?= $message; ?>

            <div class="backup-section row g-4">
                <div class="col-md-6">
                    <div class="card text-center h-100">
                        <i class="bi bi-cloud-arrow-down text-primary"></i>
                        <h5>Backup Database</h5>
                        <p>Buat salinan database dalam bentuk file <code>.sql</code>.</p>
                        <form method="post">
                            <button type="submit" name="backup" class="btn btn-success px-4">
                                <i class="bi bi-download"></i> Backup Sekarang
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card text-center h-100">
                        <i class="bi bi-cloud-arrow-up text-warning"></i>
                        <h5>Restore Database</h5>
                        <p>Pilih file <code>.sql</code> hasil backup sebelumnya untuk mengembalikan database.</p>
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <input type="file" name="backup_file" class="form-control" accept=".sql" required>
                            </div>
                            <button type="submit" name="restore" class="btn btn-warning px-4">
                                <i class="bi bi-upload"></i> Restore Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <h5 class="section-title"><i class="bi bi-clock-history"></i> Riwayat Backup & Restore</h5>
            <div class="table-responsive mt-3">
                <table class="table table-hover text-center align-middle" id="backupTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Admin</th>
                            <th>Jenis</th>
                            <th>File Path</th>
                            <th>Keterangan</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($row = mysqli_fetch_assoc($riwayat)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><span class="badge bg-<?= $row['jenis']=='backup'?'success':'warning'; ?>"><?= strtoupper($row['jenis']); ?></span></td>
                            <td><?= htmlspecialchars($row['file_path']); ?></td>
                            <td><?= htmlspecialchars($row['keterangan']); ?></td>
                            <td><?= date('d M Y - H:i', strtotime($row['waktu'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#backupTable').DataTable({
        pageLength: 5,
        lengthChange: false,
        language: {
            search: "🔍 Cari:",
            zeroRecords: "Tidak ada data.",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: { previous: "← Sebelumnya", next: "Berikutnya →" }
        }
    });
});
</script>

<?php include '../include/footer.php'; ?>
