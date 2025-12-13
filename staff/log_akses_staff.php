<?php
session_start();
include '../config/koneksi.php';

// ✅ Cek apakah user login dan admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// 📊 Ambil semua data log akses dari database
$query = "
    SELECT l.*, 
           u.username, 
           u.nama_lengkap, 
           a.judul AS judul_arsip
    FROM log_akses l
    LEFT JOIN users u ON l.user_id = u.user_id
    LEFT JOIN arsip a ON l.arsip_id = a.arsip_id
    ORDER BY l.waktu DESC
";
$logs = mysqli_query($koneksi, $query);

if (isset($_POST['reset_log'])) {
    mysqli_query($koneksi, "TRUNCATE TABLE log_akses");
    echo "<script>alert('Aktivitas log berhasil direset!'); window.location='dashboard_staff.php';</script>";
}

include '../include/header.php';
include '../include/sidebar.php';
?>
<style>
    body {
        background-color: #0f172a;
        font-family: 'Segoe UI', sans-serif;
    }

    /* 🧾 Card utama */
    .card {
        border-radius: 20px;
        box-shadow: 0px 4px 15px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .card-header {
        font-weight: 600;
        font-size: 1.3rem;
        background: linear-gradient(90deg, #0d6efd, #0b5ed7);
        padding: 1rem 1.5rem;
    }

    .card-header h4 {
        font-size: 1.25rem;
        margin: 0;
    }

    .btn-light.btn-sm {
        border-radius: 25px;
        font-weight: 500;
        transition: 0.3s;
    }
    .btn-light.btn-sm:hover {
        background: #fff;
        color: #0d6efd;
    }

    /* 📋 Tabel styling */
    .table {
        border-radius: 15px;
        overflow: hidden;
    }
    thead.table-dark {
        background: linear-gradient(90deg, #0d6efd, #0b5ed7);
    }
    thead th {
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }
    tbody tr {
        transition: all 0.2s;
    }
    tbody tr:hover {
        background: #f1f5ff;
        transform: scale(1.01);
    }

    /* 🏷️ Badge username */
    .badge.bg-secondary {
        font-size: 0.85rem;
        border-radius: 20px;
        padding: 6px 12px;
    }

    /* 📅 Kolom waktu */
    td i.bi-calendar-event {
        color: #0d6efd;
        margin-right: 4px;
    }

    /* ⚠️ Alert kosong */
    .alert-warning {
        border-radius: 15px;
        background: #fff3cd;
        color: #856404;
        font-size: 1.1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    /* ✨ Efek transisi */
    .table-hover tbody tr {
        cursor: pointer;
    }

    /* 📊 Responsif container
    .container-fluid {
        max-width: 1200px;
    } */
</style>

<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top-4">
            <h4 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Aktivitas Pengguna</h4>

            <div class="d-flex gap-2">
                <a href="log_akses_staff.php" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>

                <!-- ✅ Tombol reset ditambahkan di sini -->
                <form method="post" onsubmit="return confirm('Yakin ingin menghapus semua log aktivitas?');" class="m-0 p-0">
                    <button type="submit" name="reset_log" class="btn btn-danger btn-sm">
                        🔄 Reset Aktivitas
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <?php if (mysqli_num_rows($logs) == 0): ?>
                <div class="alert alert-warning text-center p-4">
                    <i class="bi bi-exclamation-triangle fs-4"></i><br>
                    Belum ada aktivitas tercatat.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle text-center" id="logTable">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nama Pengguna</th>
                                <th>Username</th>
                                <th>Judul Arsip</th>
                                <th>Aksi</th>
                                <th>Keterangan</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            while ($row = mysqli_fetch_assoc($logs)): 
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap'] ?? '-'); ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['username'] ?? '-'); ?></span></td>
                                <td><?= htmlspecialchars($row['judul_arsip'] ?? '-'); ?></td>
                                <td>
                                    <?php 
                                        $aksiClass = [
                                            'upload' => 'success',
                                            'edit' => 'warning',
                                            'hapus' => 'danger',
                                            'lihat' => 'info',
                                            'unduh' => 'primary',
                                            'login' => 'secondary',
                                            'logout' => 'dark'
                                        ];
                                        $badgeClass = $aksiClass[$row['aksi']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>"><?= strtoupper($row['aksi']); ?></span>
                                </td>
                                <td><?= htmlspecialchars($row['keterangan']); ?></td>
                                <td><i class="bi bi-calendar-event"></i> <?= date('d M Y - H:i', strtotime($row['waktu'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ✅ DataTables agar tabel interaktif -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#logTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        language: {
            search: "🔍 Cari:",
            zeroRecords: "Tidak ada data yang cocok.",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data tersedia",
            paginate: {
                previous: "← Sebelumnya",
                next: "Berikutnya →"
            }
        }
    });
});
</script>

<?php include '../include/footer.php'; ?>
