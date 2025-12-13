<?php
session_start();
include '../config/koneksi.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query  = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($koneksi, $query);
    $data   = mysqli_fetch_assoc($result);

    if ($data) {
        $_SESSION['id_user'] = $data['user_id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        if ($data['role'] == 'admin') {
            header("Location: /arsip_digital/admin/dashboard.php");
        } elseif ($data['role'] == 'staff') {
            header("Location: /arsip_digital/staff/dashboard_staff.php");
        } elseif ($data['role'] == 'pimpinan') {
            header("Location: /arsip_digital/pimpinan/dashboard_pimpinan.php");
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../img/logo.png" type="image/png">

  <title>Login - Sistem Arsip Digital</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    @keyframes slideInLeft {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(40px); }
      to { opacity: 1; transform: translateX(0); }
    }
    .animate-left { animation: slideInLeft 1s ease forwards; }
    .animate-right { animation: slideInRight 1.2s ease forwards; }
   
    body {
        background: #f6f6f6;
        overflow: hidden;
    }

    /* 🔥 Fade in & slight zoom */
    .card-animate {
        opacity: 0;
        transform: scale(0.9);
        animation: popUp 1s forwards ease;
    }

    @keyframes popUp {
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* ✨ Input glow */
    input:focus {
        box-shadow: 0 0 10px #facc15;
        transition: 0.2s ease;
    }

    /* 🔥 3D Tilt Effect */
    .tilt-card {
        transition: transform 0.15s ease;
    }

    /* Mouse spotlight */
    #spotlight {
        position: fixed;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.25), transparent 70%);
        pointer-events: none;
        mix-blend-mode: overlay;
        border-radius: 50%;
        z-index: 10;
    }

    /* 🔘 Ripple button effect */
    .ripple {
        position: relative;
        overflow: hidden;
    }
    .ripple span {
        position: absolute;
        border-radius: 50%;
        transform: scale(0);
        background: rgba(99, 212, 137, 0.6);
        animation: rippleEffect 0.6s linear;
    }
    @keyframes rippleEffect {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* Icon dance animation on hover */
    .icon-animate:hover {
        animation: wiggle 0.3s ease;
    }

    @keyframes wiggle {
        0% { transform: rotate(0); }
        25% { transform: rotate(8deg); }
        50% { transform: rotate(-8deg); }
        100% { transform: rotate(0); }
    }

  </style>
</head>

<body class="h-screen w-full font-sans transition-colors duration-500 bg-gray-100 dark:bg-gray-900">

  <div class="flex flex-col md:flex-row w-full h-full">

    <!-- 🗄️ Sisi kiri - gambar kantor -->
    <div class="relative w-full md:w-1/2 bg-cover bg-center animate-left"
         style="background-image: url('https://images.pexels.com/photos/374720/pexels-photo-374720.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2
');">

      <div class="absolute inset-0 bg-gradient-to-br from-black/70 to-black/40"></div>

      <div class="relative z-10 flex flex-col items-center justify-center h-full text-center text-white px-6 md:px-12">
        <div class="text-yellow-400 text-6xl mb-4">
          <i class="bi bi-archive-fill"></i>
        </div>
        <h1 class="text-4xl font-bold mb-3">Sistem Arsip Digital</h1>
        <p class="text-gray-200 max-w-md leading-relaxed text-lg">
          Solusi modern untuk pengelolaan dokumen kantor.  
          Akses mudah, cepat, dan efisien — dari mana saja.
        </p>
      </div>
    </div>

    <!-- 🧾 Sisi kanan - form login -->
    <div class="w-full md:w-1/2 flex items-center justify-center bg-gray-50 dark:bg-gray-800 relative animate-right">
      <!-- Background lembut -->
      <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1920&q=80')] bg-cover bg-center opacity-10 dark:opacity-20"></div>

      <div class="relative bg-white/80 dark:bg-gray-900/70 backdrop-blur-md border border-gray-200 dark:border-gray-700 shadow-2xl rounded-3xl p-8 md:p-10 w-[90%] max-w-md">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 text-center mb-6">
          <i class="bi bi-box-arrow-in-right text-yellow-500"></i> Login Pengguna
        </h2>

        <?php if (isset($error)) : ?>
          <div class="bg-red-500/80 text-white text-center p-3 rounded-lg mb-4 shadow-md">
            <?= $error; ?>
          </div>
        <?php endif; ?>

        <form action="" method="post" class="space-y-5">
          <div class="relative">
            <input type="text" name="username" required
              class="w-full px-4 py-3 pl-11 rounded-xl border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-yellow-400 outline-none text-gray-800 dark:text-gray-100 bg-white/70 dark:bg-gray-800/50"
              placeholder="Masukkan username">
            <i class="bi bi-person-fill absolute left-3 top-3.5 text-yellow-500 text-lg"></i>
          </div>

          <div class="relative">
            <input type="password" name="password" required
              class="w-full px-4 py-3 pl-11 rounded-xl border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-yellow-400 outline-none text-gray-800 dark:text-gray-100 bg-white/70 dark:bg-gray-800/50"
              placeholder="Masukkan password">
            <i class="bi bi-lock-fill absolute left-3 top-3.5 text-yellow-500 text-lg"></i>
          </div>

          <button type="submit" name="login"
            class="w-full bg-gradient-to-r from-yellow-400 to-yellow-500 text-gray-900 py-3 rounded-xl font-semibold shadow-lg hover:shadow-yellow-300 hover:scale-[1.02] transition-all duration-300">
            <i class="bi bi-door-open-fill"></i> Masuk
          </button>
        </form>

        <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6">
          © <?= date('Y'); ?> <span class="font-semibold text-yellow-500">Sistem Arsip Digital</span> - Kantor Arsip
        </p>
      </div>
    </div>
  </div>

  <script>
    // 🌗 Dark mode otomatis berdasarkan preferensi sistem
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
      document.documentElement.classList.add('dark');
    }
    
/* 🌟 Spotlight mengikuti mouse */
const spot = document.getElementById("spotlight");
document.addEventListener("mousemove", (e) => {
    spot.style.left = e.pageX - 100 + "px";
    spot.style.top = e.pageY - 100 + "px";
});

/* 🎮 Card tilt mengikuti gerakan mouse */
const card = document.querySelector(".tilt-card");
card.addEventListener("mousemove", (e) => {
    const rect = card.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width;
    const y = (e.clientY - rect.top) / rect.height;

    const rotateX = (y - 0.5) * 10;
    const rotateY = (x - 0.5) * -10;

    card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
});
card.addEventListener("mouseleave", () => {
    card.style.transform = "rotateX(0) rotateY(0)";
});

/* 🌊 Ripple button */
document.querySelectorAll(".ripple").forEach(btn => {
    btn.addEventListener("click", function (e) {
        let x = e.clientX - e.target.offsetLeft;
        let y = e.clientY - e.target.offsetTop;

        let rip = document.createElement("span");
        rip.style.left = x + "px";
        rip.style.top = y + "px";
        this.appendChild(rip);

        setTimeout(() => rip.remove(), 600);
    });
});

  </script>
