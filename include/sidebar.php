<div class="d-flex">
    <div class="sidebar p-3" id="sidebar">
        <h5 class="text-light mb-4"><i class="bi bi-archive"></i> Menu</h5>
        <ul class="nav flex-column">

            <?php if ($_SESSION['role'] == 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="../admin/dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="../admin/kelola_user.php"><i class="bi bi-people"></i> Kelola User</a></li>
                <li class="nav-item"><a class="nav-link" href="../admin/kelola_arsip.php"><i class="bi bi-folder"></i> Kelola Arsip</a></li>
               <li class="nav-item"><a class="nav-link" href="../admin/log_akses_admin.php"><i class="bi bi-clock-history"></i> Riwayat Pengguna</a></li>
                <li class="nav-item"><a class="nav-link" href="../admin/kategori.php"><i class="bi bi-folder"></i> Tambah Kategori Arsip</a></li>
                <li class="nav-item"><a class="nav-link" href="../admin/laporan.php"><i class="bi bi-people"></i> Laporan</a></li>

            <?php elseif ($_SESSION['role'] == 'staff'): ?>
                <li class="nav-item"><a class="nav-link" href="../staff/dashboard_staff.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="../staff/upload_arsip.php"><i class="bi bi-upload"></i> Upload Arsip</a></li>
                <li class="nav-item"><a class="nav-link" href="../staff/lihat_status.php"><i class="bi bi-eye"></i> Lihat Status Arsip</a></li>
                 <li class="nav-item"><a class="nav-link" href="../staff/edit_arsip_staff.php"><i class="bi bi-folder"></i> Edit Arsip</a></li>
                
            <?php elseif ($_SESSION['role'] == 'pimpinan'): ?>
                <li class="nav-item"><a class="nav-link" href="../pimpinan/dashboard_pimpinan.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="../pimpinan/akses_arsip.php"><i class="bi bi-folder"></i> Akses Arsip</a></li>
                <li class="nav-item"><a class="nav-link" href="../pimpinan/laporan_ringkas.php"><i class="bi bi-bar-chart"></i> Laporan Ringkas</a></li>
            <?php endif; ?>

        </ul>
    </div>

    <div class="content p-4 flex-grow-1" id="mainContent">
