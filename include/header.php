<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Digital</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f172a; /* dark background */
            color: #f1f5f9;
        }
        .navbar-dark {
            background-color: #1e3a8a; /* dark blue */
        }
        .sidebar {
            background-color: #1e293b;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar a {
            color: #cbd5e1;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #334155;
            color: #fff;
        }
        .sidebar .nav-link.active {
            background-color: #2563eb;
            color: #fff;
        }
        .content {
            transition: margin-left 0.3s ease;
        }
        .sidebar.collapsed ~ .content {
            margin-left: 70px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm px-3">
    <div class="container-fluid">
        <button class="btn btn-outline-light me-3" id="toggleSidebar"><i class="bi bi-list"></i></button>
        <a class="navbar-brand fw-bold" href="#">📁 Arsip Digital</a>
        <div class="d-flex align-items-center ms-auto">
            <span class="me-3"><i class="bi bi-person-circle"></i> <?= $_SESSION['username'] ?? 'Guest'; ?> (<?= $_SESSION['role'] ?? ''; ?>)</span>
            <a href="../auth/logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>
</nav>
