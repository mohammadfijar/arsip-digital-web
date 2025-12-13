<?php
session_start();
include '../config/koneksi.php';

// Cek apakah sudah login dan role admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Ambil data statistik
$total_users_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users");
$total_users = mysqli_fetch_assoc($total_users_query)['total'];

$total_arsip_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip");
$total_arsip = mysqli_fetch_assoc($total_arsip_query)['total'];

$total_kategori_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM kategori");
$total_kategori = mysqli_fetch_assoc($total_kategori_query)['total'];

$total_log_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM log_akses");
$total_log = mysqli_fetch_assoc($total_log_query)['total'];

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f6fa;
            animation: fadeIn 0.4s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .card {
            border-radius: 12px;
            transition: 0.3s;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 5px 18px rgba(0,0,0,0.2);
        }

        .log-item-highlight {
            background: #e9f7ff !important;
            animation: glow 1s ease-in-out;
        }

        @keyframes glow {
            from { background: #d8f0ff; }
            to   { background: #e9f7ff; }
        }

        .counter {
            font-size: 2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include '../include/header.php'; ?>
<?php include '../include/sidebar.php'; ?>

<div class="container mt-4">

    <h2 class="mb-4">📊 Dashboard Admin</h2>

    <div class="row g-4">

        <div class="col-md-3">
            <div class="card bg-primary text-white text-center p-3">
                <div class="counter" data-target="<?= $total_users; ?>">0</div>
                <p>Pengguna Terdaftar</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white text-center p-3">
                <div class="counter" data-target="<?= $total_arsip; ?>">0</div>
                <p>Total Arsip</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white text-center p-3">
                <div class="counter" data-target="<?= $total_kategori; ?>">0</div>
                <p>Kategori Arsip</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white text-center p-3">
                <div class="counter" data-target="<?= $total_log; ?>">0</div>
                <p>Aktivitas Log</p>
            </div>
        </div>

    </div>

    <div class="card mt-5">
        <div class="card-header bg-dark text-white">
            <strong>📜 5 Aktivitas Terbaru</strong>
        </div>
        <div class="card-body">
            <ul class="list-group log-list" id="logList">
                <?php 
                $i = 0;
                while ($log = mysqli_fetch_assoc($logs_query)): 
                ?>
                    <li class="list-group-item <?= $i == 0 ? 'log-item-highlight' : '' ?>">
                        <strong><?= $log['username'] ?? 'User Dihapus'; ?></strong> melakukan 
                        <b><?= $log['aksi']; ?></b> 
                        pada arsip: <em><?= $log['judul'] ?? '-'; ?></em> 
                        <span class="text-muted float-end"><?= $log['waktu']; ?></span>
                    </li>
                <?php $i++; endwhile; ?>
            </ul>
        </div>
    </div>

</div>

<?php include '../include/footer.php'; ?>

<script>
// =========================
// ANIMASI ANGKA COUNTER
// =========================
const counters = document.querySelectorAll('.counter');

counters.forEach(counter => {
    counter.innerText = "0";
    const target = +counter.getAttribute("data-target");
    const speed = 300;

    const updateCounter = () => {
        const current = +counter.innerText;
        const increment = target / speed;

        if (current < target) {
            counter.innerText = Math.ceil(current + increment);
            setTimeout(updateCounter, 5);
        } else {
            counter.innerText = target;
        }
    };
    updateCounter();
});

// =========================
// EFEK HIGHLIGHT LOG TERBARU
// =========================
setTimeout(() => {
    let firstLog = document.querySelector(".log-item-highlight");
    if (firstLog) firstLog.classList.remove("log-item-highlight");
}, 2000);

</script>

</body>
</html>
