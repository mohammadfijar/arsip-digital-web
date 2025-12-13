<?php
// Cegah session_start() dobel
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-light" href="dashboard_pimpinan.php">
      📁 Arsip Digital
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="dashboard_pimpinan.php">🏠 Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="verifikasi_arsip.php">📂 Verifikasi Arsip</a></li>
        <li class="nav-item"><a class="nav-link" href="laporan_ringkas.php">📊 Ringkasan</a></li>
        <li class="nav-item"><a class="nav-link" href="riwayat_arsip.php">🕒 Riwayat Arsip</a></li>
      </ul>

      <ul class="navbar-nav">
        <li class="nav-item">
          <span class="navbar-text text-info me-3">
            👤 <?= $_SESSION['username'] ?? 'Pimpinan' ?> (<?= $_SESSION['role'] ?? '' ?>)
          </span>
        </li>
        <li class="nav-item">
          <a class="btn btn-danger btn-sm" href="../auth/logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
