<?php
session_start();
include '../config/koneksi.php';

// --- cek session & role admin (kompatibel dengan beberapa nama session) ---
$user_level = $_SESSION['level'] ?? $_SESSION['role'] ?? null;
if (!isset($_SESSION['username']) || $user_level != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// ambil id arsip dari querystring
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: kelola_arsip.php");
    exit();
}
$id = intval($_GET['id']);

// ambil data arsip (bergabung dengan kategori dan uploader jika ada)
$q = "
    SELECT a.*, k.nama_kategori, u.username AS uploader_name
    FROM arsip a
    LEFT JOIN kategori k ON a.kategori_id = k.kategori_id
    LEFT JOIN users u ON a.uploaded_by = u.user_id
    WHERE a.arsip_id = '$id'
    LIMIT 1
";
$res = mysqli_query($koneksi, $q);
if (!$res || mysqli_num_rows($res) == 0) {
    header("Location: kelola_arsip.php?msg=notfound");
    exit();
}
$row = mysqli_fetch_assoc($res);

// --- normalisasi nama kolom (untuk kompatibilitas kalau ada variasi nama kolom) ---
$arsip_id    = $row['arsip_id'] ?? $row['id_arsip'] ?? '';
$nomor_arsip = $row['nomor_arsip'] ?? '';
$judul       = $row['judul'] ?? '';
$deskripsi   = $row['deskripsi'] ?? '';
$kategori    = $row['nama_kategori'] ?? '-';
$uploader    = $row['uploader_name'] ?? $row['username'] ?? '-';
$nama_file   = $row['nama_file'] ?? '';
$ukuran      = isset($row['ukuran_file']) ? (int)$row['ukuran_file'] : (isset($row['size']) ? (int)$row['size'] : 0);
$ekstensi    = $row['ekstensi'] ?? '';
$tanggal     = $row['tanggal_upload'] ?? $row['created_at'] ?? '';
$status      = $row['status_arsip'] ?? $row['status'] ?? 'baru';

// --- helper: ubah bytes ke format human readable ---
function human_filesize($bytes, $decimals = 2) {
    if ($bytes <= 0) return '0 B';
    $sizes = ['B','KB','MB','GB','TB'];
    $factor = floor((strlen((string)$bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $sizes[$factor];
}

// --- catat aktivitas "lihat" ke log_akses ---
$current_user_id = $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
$uid_for_insert = is_numeric($current_user_id) ? intval($current_user_id) : "NULL";
$keterangan = mysqli_real_escape_string($koneksi, "Melihat detail arsip: " . $judul);
$log_sql = "INSERT INTO log_akses (user_id, arsip_id, aksi, keterangan) VALUES (" .
           ($uid_for_insert === "NULL" ? "NULL" : $uid_for_insert) . ", '$arsip_id', 'lihat', '$keterangan')";
@mysqli_query($koneksi, $log_sql); // gunakan @ supaya halaman tidak crash kalau gagal log

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Detail Arsip - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f5f6fa; }
    .card { border-radius: 12px; }
    .meta-label { font-weight: 600; color: #333; }
    .file-preview { max-height: 600px; border: 1px solid #e6e6e6; border-radius: 8px; padding: 8px; background:#fff; }
  </style>
</head>
<body>

<?php include '../include/header.php'; ?>
<?php include '../include/sidebar.php'; ?>

<div class="container mt-4">
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">📄 Detail Arsip</h5>
      <div>
        <a href="kelola_arsip.php" class="btn btn-secondary btn-sm">← Kembali</a>
        <a href="edit_arsip.php?id=<?= htmlspecialchars($arsip_id); ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
        <?php if(!empty($nama_file)): ?>
          <a href="../upload/<?= rawurlencode($nama_file); ?>" target="_blank" class="btn btn-primary btn-sm">⬇️ Unduh</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="card-body">
      <div class="row g-4">
        <div class="col-md-6">
          <dl class="row">
            <dt class="col-sm-4 meta-label">Nomor Arsip</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($nomor_arsip ?: '-'); ?></dd>

            <dt class="col-sm-4 meta-label">Judul</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($judul ?: '-'); ?></dd>

            <dt class="col-sm-4 meta-label">Kategori</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($kategori); ?></dd>

            <dt class="col-sm-4 meta-label">Uploader</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($uploader); ?></dd>

            <dt class="col-sm-4 meta-label">Ukuran File</dt>
            <dd class="col-sm-8"><?= $ukuran ? human_filesize($ukuran) : '-'; ?></dd>

            <dt class="col-sm-4 meta-label">Ekstensi</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($ekstensi ?: '-'); ?></dd>

            <dt class="col-sm-4 meta-label">Tanggal Upload</dt>
            <dd class="col-sm-8"><?= $tanggal ? htmlspecialchars(date('d-m-Y H:i', strtotime($tanggal))) : '-'; ?></dd>

            <dt class="col-sm-4 meta-label">Status</dt>
            <dd class="col-sm-8">
              <span class="badge 
                <?php
                  if ($status == 'baru') echo 'bg-secondary';
                  elseif ($status == 'diperiksa') echo 'bg-warning text-dark';
                  elseif ($status == 'disetujui') echo 'bg-success';
                  elseif ($status == 'ditolak') echo 'bg-danger';
                  else echo 'bg-info';
                ?>">
                <?= htmlspecialchars(ucfirst($status)); ?>
              </span>
            </dd>

            <dt class="col-sm-4 meta-label">Deskripsi</dt>
            <dd class="col-sm-8" style="white-space:pre-wrap;"><?= htmlspecialchars($deskripsi ?: '-'); ?></dd>
          </dl>
        </div>

          <?php if (!empty($nama_file) && in_array(strtolower($ekstensi), ['jpg','jpeg','png','gif'])): ?>
            <div class="file-preview text-center">
              <img src="../upload/<?= rawurlencode($nama_file); ?>" alt="<?= htmlspecialchars($judul); ?>" class="img-fluid" style="max-height:520px;">
            </div>
          <?php elseif (!empty($nama_file) && strtolower($ekstensi) === 'pdf'): ?>
            <div class="file-preview">
              <iframe src="../upload/<?= rawurlencode($nama_file); ?>" style="width:100%; height:520px;" frameborder="0"></iframe>
            </div>
          <?php elseif (!empty($nama_file)): ?>
            <div class="file-preview">
              <p><strong>File:</strong> <a href="../upload/<?= rawurlencode($nama_file); ?>" target="_blank"><?= htmlspecialchars($nama_file); ?></a></p>
              <p class="small text-muted">Format preview tidak tersedia untuk ekstensi ini.</p>
            </div>
          <?php else: ?>
            <div class="alert alert-warning">Tidak ada file ter-upload untuk arsip ini.</div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../include/footer.php'; ?>

</body>
</html>
