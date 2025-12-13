<?php
require '../config/koneksi.php';
require '../vendor/autoload.php'; // jika kamu sudah punya folder vendor (Composer + dompdf)

use Dompdf\Dompdf;
use Dompdf\Options;

// --- Jika belum install dompdf ---
if (!class_exists('Dompdf\Dompdf')) {
    echo "<h3 style='color:red;text-align:center;margin-top:50px'>❌ Library DOMPDF belum terinstal.<br>
    Jalankan perintah berikut di CMD (di folder proyek kamu):<br><code>composer require dompdf/dompdf</code></h3>";
    exit;
}

// --- Ambil data arsip ---
$query = "
SELECT a.*, k.nama_kategori, u.username AS uploader
FROM arsip a
LEFT JOIN kategori k ON a.kategori_id = k.kategori_id
LEFT JOIN users u ON a.uploaded_by = u.user_id
ORDER BY a.tanggal_upload DESC
";
$result = mysqli_query($koneksi, $query);

// --- Siapkan HTML untuk PDF ---
$html = '
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
h2 { text-align: center; }
table { border-collapse: collapse; width: 100%; margin-top: 20px; }
th, td { border: 1px solid #333; padding: 6px; text-align: center; }
th { background-color: #f2f2f2; }
.badge { border-radius: 4px; color: white; padding: 2px 6px; font-size: 11px; }
.success { background-color: #28a745; }
.danger { background-color: #dc3545; }
.secondary { background-color: #6c757d; }
</style>

<h2>📄 Laporan Data Arsip Digital</h2>
<p style="text-align:right;">Dicetak: '.date('d/m/Y H:i').'</p>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nomor Arsip</th>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Uploader</th>
            <th>Status</th>
            <th>Tanggal Upload</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $statusClass = 'secondary';
    if ($row['status_arsip'] == 'disetujui') $statusClass = 'success';
    elseif ($row['status_arsip'] == 'ditolak') $statusClass = 'danger';

    $html .= '<tr>
        <td>'.$no++.'</td>
        <td>'.htmlspecialchars($row['nomor_arsip']).'</td>
        <td>'.htmlspecialchars($row['judul']).'</td>
        <td>'.($row['nama_kategori'] ?? '-').'</td>
        <td>'.($row['uploader'] ?? '-').'</td>
        <td><span class="badge '.$statusClass.'">'.ucfirst($row['status_arsip']).'</span></td>
        <td>'.date('d/m/Y', strtotime($row['tanggal_upload'])).'</td>
    </tr>';
}

$html .= '</tbody></table>';

// --- Konfigurasi DOMPDF ---
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// --- Output PDF ke browser ---
$dompdf->stream("Laporan_Arsip_".date('Ymd_His').".pdf", ["Attachment" => false]);
exit;
?>
