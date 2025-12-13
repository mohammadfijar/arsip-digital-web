<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/koneksi.php';

// Proses form upload
$alert = "";
if (isset($_POST['upload'])) {
    $nomor_arsip = mysqli_real_escape_string($koneksi, $_POST['nomor_arsip']);
    $judul       = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $kategori_id = mysqli_real_escape_string($koneksi, $_POST['kategori_id']);
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $uploader    = $_SESSION['id_user']; // pastikan session id_user ada
    $tanggal     = date("Y-m-d H:i:s");

    // Proses file
    $file_name  = $_FILES['file_arsip']['name'];
    $file_tmp   = $_FILES['file_arsip']['tmp_name'];
    $file_size  = $_FILES['file_arsip']['size'];
    $ext        = pathinfo($file_name, PATHINFO_EXTENSION);

    $allowed_ext = ['pdf', 'doc', 'docx', 'zip'];
    if (!in_array(strtolower($ext), $allowed_ext)) {
        $alert = "<div class='alert alert-danger'>Format file tidak diperbolehkan. Hanya PDF, DOC, DOCX, dan ZIP.</div>";
    } elseif ($file_size > 5242880) { // max 5MB
        $alert = "<div class='alert alert-danger'>Ukuran file maksimal 5MB.</div>";
    } else {
        $new_name = uniqid() . "_" . $file_name;
        $target   = "../upload/" . $new_name;

        if (move_uploaded_file($file_tmp, $target)) {
            // Simpan ke tabel arsip
            $query = "INSERT INTO arsip 
                (nomor_arsip, judul, deskripsi, kategori_id, nama_file, ukuran_file, ekstensi, uploaded_by, tanggal_upload, status_arsip) 
                VALUES 
                ('$nomor_arsip', '$judul', '$deskripsi', '$kategori_id', '$new_name', '$file_size', '$ext', '$uploader', '$tanggal', 'baru')";
            
            if (mysqli_query($koneksi, $query)) {
                // Ambil ID arsip yang baru disimpan
                $arsip_id = mysqli_insert_id($koneksi);

                // Siapkan data log
                $aksi = 'upload';
                $keterangan = "Staff mengupload arsip berjudul: $judul";

                // Simpan ke tabel log_akses
                $log = "INSERT INTO log_akses (user_id, arsip_id, aksi, keterangan) 
                        VALUES ('$uploader', '$arsip_id', '$aksi', '$keterangan')";
                mysqli_query($koneksi, $log);

                $alert = "<div class='alert alert-success'>✅ Arsip berhasil diunggah dan dicatat di log akses.</div>";
            } else {
                $alert = "<div class='alert alert-danger'>Terjadi kesalahan saat menyimpan ke database.</div>";
            }
        } else {
            $alert = "<div class='alert alert-danger'>Gagal mengunggah file.</div>";
        }
    }
}
?>

<?php include '../include/header.php'; ?>
<?php include '../include/sidebar.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-upload"></i> Upload Arsip Baru</h2>
        <a href="dashboard_staff.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <?= $alert; ?>

    <div class="card shadow-sm border-0" style="background-color: #1e293b; color: #f1f5f9;">
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Nomor Arsip</label>
                    <input type="text" name="nomor_arsip" class="form-control" placeholder="Masukkan nomor arsip..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Judul Arsip</label>
                    <input type="text" name="judul" class="form-control" placeholder="Masukkan judul arsip..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php
                        $kategori_id = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                        while ($row = mysqli_fetch_assoc($kategori_id)) {
                            echo "<option value='{$row['kategori_id']}'>{$row['nama_kategori']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi Arsip</label>
                    <textarea name="deskripsi" class="form-control" rows="4" placeholder="Tulis deskripsi singkat..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload File Arsip</label>
                    <input type="file" name="file_arsip" class="form-control" required>
                    <small class="text-muted">📁 Format: PDF, DOC, DOCX, ZIP (maks. 5MB)</small>
                </div>

                <div class="text-end">
                    <button type="submit" name="upload" class="btn btn-primary px-4">
                        <i class="bi bi-cloud-upload"></i> Upload Arsip
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../include/footer.php'; ?>
