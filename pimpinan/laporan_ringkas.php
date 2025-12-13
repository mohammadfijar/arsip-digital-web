<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pimpinan') {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/koneksi.php';
?>

<?php include '../include/header.php'; ?>
<?php include 'navbar_pimpinan.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-graph-up"></i> Laporan Ringkas Arsip</h2>
        <span class="badge bg-primary fs-6">📅 <?= date("l, d F Y"); ?></span>
    </div>

    <!-- 📆 Filter Bulan & Tahun -->
    <div class="card shadow-sm mb-4 border-0" style="background-color: #1e293b; color: #f1f5f9;">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">-- Semua Bulan --</option>
                        <?php
                        $bulanSekarang = date("m");
                        for ($i = 1; $i <= 12; $i++) {
                            $val = str_pad($i, 2, '0', STR_PAD_LEFT);
                            $namaBulan = date("F", mktime(0, 0, 0, $i, 10));
                            $selected = (isset($_GET['bulan']) && $_GET['bulan'] == $val) ? 'selected' : '';
                            echo "<option value='$val' $selected>$namaBulan</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        <option value="">-- Semua Tahun --</option>
                        <?php
                        $tahunSekarang = date("Y");
                        for ($t = $tahunSekarang; $t >= 2020; $t--) {
                            $selected = (isset($_GET['tahun']) && $_GET['tahun'] == $t) ? 'selected' : '';
                            echo "<option value='$t' $selected>$t</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Tampilkan</button>
                    <a href="laporan_ringkas.php" class="btn btn-secondary"><i class="bi bi-arrow-repeat"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    <?php
    $filter = "WHERE 1";
    if (!empty($_GET['bulan'])) {
        $bulan = $_GET['bulan'];
        $filter .= " AND MONTH(tanggal_upload) = '$bulan'";
    }
    if (!empty($_GET['tahun'])) {
        $tahun = $_GET['tahun'];
        $filter .= " AND YEAR(tanggal_upload) = '$tahun'";
    }

    // Statistik utama
    $total = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM arsip $filter"))['total'];
    $terbaru = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT judul FROM arsip $filter ORDER BY tanggal_upload DESC LIMIT 1"))['judul'] ?? '-';
    $topUploader = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT u.username, COUNT(*) AS jumlah FROM arsip a JOIN users u ON a.uploaded_by = u.user_id $filter GROUP BY a.uploaded_by ORDER BY jumlah DESC LIMIT 1"));
    ?>

    <!-- 📊 Statistik Ringkas -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm" style="background-color: #1e293b; color: #f1f5f9;">
                <div class="card-body">
                    <h5>Total Arsip</h5>
                    <h2 class="fw-bold text-primary"><?= $total; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm" style="background-color: #1e293b; color: #f1f5f9;">
                <div class="card-body">
                    <h5>Arsip Terbaru</h5>
                    <h2 class="fw-bold text-success"><?= $terbaru; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm" style="background-color: #1e293b; color: #f1f5f9;">
                <div class="card-body">
                    <h5>Uploader Teraktif</h5>
                    <h2 class="fw-bold text-warning"><?= $topUploader['username'] ?? '-'; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- 📈 Grafik Arsip per Kategori -->
    <div class="card shadow-sm border-0 mb-4" style="background-color: #1e293b; color: #f1f5f9;">
        <div class="card-header bg-transparent border-0">
            <h4 class="fw-bold"><i class="bi bi-pie-chart"></i> Grafik Arsip per Kategori</h4>
        </div>
        <div class="card-body d-flex justify-content-center">
  <canvas id="grafikKategori" width="300" height="300" style="max-width: 300px; max-height: 300px;"></canvas>
</div>
    </div>

    <!-- 🖨️ Tombol Cetak PDF -->
    <div class="d-flex justify-content-end mb-5">
        <button class="btn btn-danger" onclick="window.print()"><i class="bi bi-file-earmark-pdf"></i> Cetak PDF</button>
    </div>
</div>

<?php
// Ambil data kategori untuk grafik
$kategoriData = [];
$queryKategori = mysqli_query($koneksi, "SELECT kategori_id, COUNT(*) as jumlah FROM arsip $filter GROUP BY kategori_id");
while ($row = mysqli_fetch_assoc($queryKategori)) {
    $kategoriData[] = $row;
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('grafikKategori').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($kategoriData, 'kategori')); ?>,
        datasets: [{
            data: <?= json_encode(array_column($kategoriData, 'jumlah')); ?>,
            backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#20c997', '#6f42c1'],
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#f1f5f9' }
            }
        }
    }
});
</script>

<?php include '../include/footer.php'; ?>
