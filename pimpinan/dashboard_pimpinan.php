<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pimpinan') {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/koneksi.php';
include '../include/header.php';
include 'navbar_pimpinan.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-speedometer2"></i> Selamat Datang Bos</h2>
        <span class="badge bg-primary fs-6">📅 <?= date("l, d F Y"); ?></span>
    </div>

    <!-- 🔍 Filter Tahun -->
    <form method="GET" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="tahun" class="col-form-label fw-bold text-light">Pilih Tahun:</label>
            </div>
            <div class="col-auto">
                <select name="tahun" id="tahun" class="form-select bg-dark text-light border-0">
                    <?php
                    $tahunSekarang = date('Y');
                    $tahunDipilih = isset($_GET['tahun']) ? $_GET['tahun'] : $tahunSekarang;
                    for ($i = $tahunSekarang; $i >= 2020; $i--) {
                        $selected = $tahunDipilih == $i ? 'selected' : '';
                        echo "<option value='$i' $selected>$i</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Tampilkan</button>
            </div>
        </div>
    </form>

    <?php
    // Ambil data berdasarkan tahun
    $tahunFilter = $tahunDipilih;

    $qTotal = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip WHERE YEAR(tanggal_upload) = '$tahunFilter'");
    $dTotal = mysqli_fetch_assoc($qTotal);

    $bulanIni = date('m');
    $qBulan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip WHERE MONTH(tanggal_upload) = '$bulanIni' AND YEAR(tanggal_upload) = '$tahunFilter'");
    $dBulan = mysqli_fetch_assoc($qBulan);

    $qStaff = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users WHERE role='staff'");
    $dStaff = mysqli_fetch_assoc($qStaff);
    ?>

    <!-- 📊 Ringkasan Arsip Utama -->
    <div class="row g-4">

        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm bg-dark text-light">
                <div class="card-body">
                    <i class="bi bi-folder-fill fs-1 text-info"></i>
                    <h5>Total Arsip Tahun <?= $tahunFilter; ?></h5>
                    <h2><?= $dTotal['total']; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm bg-dark text-light">
                <div class="card-body">
                    <i class="bi bi-cloud-arrow-up-fill fs-1 text-success"></i>
                    <h5>Arsip Bulan Ini</h5>
                    <h2><?= $dBulan['total']; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm bg-dark text-light">
                <div class="card-body">
                    <i class="bi bi-people-fill fs-1 text-warning"></i>
                    <h5>Total Staff</h5>
                    <h2><?= $dStaff['total']; ?></h2>
                </div>
            </div>
        </div>

        <!-- Tombol ke halaman verifikasi arsip -->
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm bg-dark text-light">
                <div class="card-body">
                    <i class="bi bi-check2-square fs-1 text-primary"></i>
                    <h5>Verifikasi Arsip</h5>
                    <a href="verifikasi_arsip.php" class="btn btn-outline-primary btn-sm mt-2">Kelola Status Arsip</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 📊 Statistik Status Arsip -->
    <div class="row mt-4">
        <?php
        $statusList = ['baru', 'diperiksa', 'disetujui', 'ditolak'];
        $colors = ['warning', 'info', 'success', 'danger'];
        foreach ($statusList as $i => $status) {
            $qStat = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip WHERE status_arsip='$status'");
            $dStat = mysqli_fetch_assoc($qStat);
            echo "
            <div class='col-md-3'>
                <div class='card text-center border-0 shadow-sm bg-dark text-light'>
                    <div class='card-body'>
                        <h5 class='fw-bold text-{$colors[$i]}'>".ucfirst($status)."</h5>
                        <h3>{$dStat['total']}</h3>
                    </div>
                </div>
            </div>";
        }
        ?>
    </div>

    <!-- 📈 Grafik Arsip Per Bulan -->
    <div class="card mt-5 shadow-sm border-0 bg-dark text-light">
        <div class="card-header bg-transparent border-0">
            <h4 class="fw-bold"><i class="bi bi-bar-chart-line"></i> Statistik Arsip Per Bulan (<?= $tahunFilter; ?>)</h4>
        </div>
        <div class="card-body">
            <canvas id="arsipChart"></canvas>
        </div>
    </div>

    <!-- 🥧 Grafik Arsip per Kategori -->
    <div class="card mt-4 shadow-sm border-0 bg-dark text-light">
        <div class="card-header bg-transparent border-0">
            <h4 class="fw-bold"><i class="bi bi-pie-chart"></i> Distribusi Arsip per Kategori</h4>
        </div>
        <div class="card-body">
            <canvas id="kategoriChart"></canvas>
        </div>
    </div>

    <!-- 📄 Arsip Terbaru -->
    <div class="card mt-4 shadow-sm border-0 bg-dark text-light">
        <div class="card-header border-0 bg-transparent">
            <h4 class="fw-bold"><i class="bi bi-journal-text"></i> Arsip Terbaru</h4>
        </div>
        <div class="card-body">
            <table class="table table-dark table-striped table-sm align-middle">
                <thead>
                    <tr><th>Judul</th><th>Kategori</th><th>Status</th><th>Uploader</th><th>Tanggal</th></tr>
                </thead>
                <tbody>
                    <?php
                    $arsipBaru = mysqli_query($koneksi, "
                        SELECT a.*, k.nama_kategori, u.nama_lengkap 
                        FROM arsip a
                        LEFT JOIN kategori k ON a.kategori_id=k.kategori_id
                        LEFT JOIN users u ON a.uploaded_by=u.user_id
                        ORDER BY a.tanggal_upload DESC LIMIT 5");
                    while($r = mysqli_fetch_assoc($arsipBaru)){
                        echo "<tr>
                                <td>{$r['judul']}</td>
                                <td>{$r['nama_kategori']}</td>
                                <td><span class='badge bg-secondary'>{$r['status_arsip']}</span></td>
                                <td>{$r['nama_lengkap']}</td>
                                <td>".date('d-m-Y H:i', strtotime($r['tanggal_upload']))."</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 📊 Statistik Aktivitas -->
    <div class="card mt-4 shadow-sm border-0 bg-dark text-light">
        <div class="card-header border-0 bg-transparent">
            <h4 class="fw-bold"><i class="bi bi-activity"></i> Aktivitas Log Akses User</h4>
        </div>
        <div class="card-body text-center">
            <?php
            $aksiList = ['upload','edit'];
            foreach ($aksiList as $aksi) {
                $qAksi = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM log_akses WHERE aksi='$aksi'");
                $dAksi = mysqli_fetch_assoc($qAksi);
                echo "<span class='badge bg-info m-2 p-3 fs-6 text-dark'><b>$aksi:</b> {$dAksi['total']}</span>";
            }
            ?>
        </div>
    </div>

</div>

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('arsipChart').getContext('2d');
const arsipChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
        datasets: [{
            label: 'Jumlah Arsip',
            data: [
                <?php
                for ($i = 1; $i <= 12; $i++) {
                    $qStat = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip 
                        WHERE MONTH(tanggal_upload)='$i' AND YEAR(tanggal_upload)='$tahunFilter'");
                    $dStat = mysqli_fetch_assoc($qStat);
                    echo $dStat['total'].",";
                }
                ?>
            ],
            backgroundColor: '#2563eb'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { ticks: { color: '#f1f5f9' }, beginAtZero: true },
            x: { ticks: { color: '#f1f5f9' } }
        },
        plugins: { legend: { labels: { color: '#f1f5f9' } } }
    }
});

const ctx2 = document.getElementById('kategoriChart').getContext('2d');
const kategoriChart = new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: [
            <?php
            $qKat = mysqli_query($koneksi, "SELECT nama_kategori FROM kategori");
            while ($dKat = mysqli_fetch_assoc($qKat)) echo "'".$dKat['nama_kategori']."',";
            ?>
        ],
        datasets: [{
            data: [
                <?php
                $qCount = mysqli_query($koneksi, "SELECT COUNT(a.arsip_id) AS total 
                    FROM kategori k LEFT JOIN arsip a ON a.kategori_id = k.kategori_id 
                    GROUP BY k.kategori_id");
                while ($dCount = mysqli_fetch_assoc($qCount)) echo $dCount['total'].","; 
                ?>
            ],
            backgroundColor: ['#2563eb','#10b981','#f59e0b','#ef4444','#8b5cf6','#0ea5e9']
        }]
    },
    options: { plugins: { legend: { labels: { color: '#f1f5f9' } } } }
});
</script>

<?php include '../include/footer.php'; ?>
