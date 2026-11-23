
<?php

require_once __DIR__ . "/../Backend/middleware/auth_check.php";

$namaRaw = $_SESSION["nama"] ?? "";
$emailRaw = $_SESSION["email"] ?? "";

$nama = htmlspecialchars($namaRaw, ENT_QUOTES, "UTF-8");
$email = htmlspecialchars($emailRaw, ENT_QUOTES, "UTF-8");

$inisial = strtoupper(substr(trim($namaRaw), 0, 1));

$error = $_GET["error"] ?? "";

$messages = [
    "empty"  => "Nama dan email wajib diisi.",
    "email"  => "Format email tidak valid.",
    "exists" => "Email sudah digunakan akun lain.",
    "failed" => "Gagal memperbarui profil."
];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Profil | Siruwa Store</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/edit-profil.css">
</head>

<body>

<header class="account-header">

    <a href="Beranda.php" class="brand">
        Siruwa<span>.</span>
    </a>

    <nav class="header-nav">
        <a href="Beranda.php">Beranda</a>
        <a href="produk.html">Produk</a>
        <a href="keranjang.php">Keranjang</a>
    </nav>

    <a href="profil.php" class="header-account">
        Kembali ke Profil
    </a>

</header>


<main class="account-container">

    <aside class="account-sidebar">

        <div class="sidebar-user">

            <div class="avatar">
                <?= htmlspecialchars($inisial, ENT_QUOTES, "UTF-8") ?>
            </div>

            <div class="sidebar-user-info">
                <strong><?= $nama ?></strong>
                <span><?= $email ?></span>
            </div>

        </div>


        <div class="sidebar-menu">

            <p class="menu-title">AKUN SAYA</p>

            <a href="profil.php">
                <span class="menu-indicator"></span>
                Profil Saya
            </a>

            <a href="edit-profil.php" class="active">
                <span class="menu-indicator"></span>
                Edit Profil
            </a>

            <a href="edit-profil.php#informasi">
                <span class="menu-indicator"></span>
                Informasi Pribadi
            </a>

            <a href="edit-profil.php#keamanan">
                <span class="menu-indicator"></span>
                Keamanan Akun
            </a>


            <p class="menu-title order-title">PESANAN SAYA</p>

            <a href="keranjang.php">
                <span class="menu-indicator"></span>
                Keranjang
            </a>

            <a href="riwayat-pesanan.php">
                <span class="menu-indicator"></span>
                Riwayat Pesanan
            </a>

            <a href="wishlist.php">
                <span class="menu-indicator"></span>
                Produk Favorit
            </a>

            <div class="sidebar-bottom">
                <a href="../Backend/auth/logout.php"
                   onclick="return confirm('Yakin ingin keluar dari akun?')">
                    Keluar dari Akun
                </a>
            </div>

        </div>

    </aside>


    <section class="account-content">

        <div class="content-heading">

            <div>
                <h1>Edit Profil</h1>

                <p>
                    Kelola informasi pribadi dan detail akun Siruwa Anda.
                </p>
            </div>

        </div>


        <div class="edit-panel" id="informasi">

            <div class="panel-heading">

                <h2>Informasi Profil</h2>

                <p>
                    Pastikan informasi akun Anda selalu diperbarui.
                </p>

            </div>


            <?php if (isset($messages[$error])): ?>

                <div class="form-message">
                    <?= htmlspecialchars($messages[$error], ENT_QUOTES, "UTF-8") ?>
                </div>

            <?php endif; ?>


            <form action="../Backend/profile/update_profil.php"
                  method="POST">

                <div class="form-row">

                    <label for="nama">Nama Lengkap</label>

                    <div class="input-wrapper">
                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            value="<?= $nama ?>"
                            maxlength="100"
                            placeholder="Masukkan nama lengkap"
                            required
                        >
                    </div>

                </div>


                <div class="form-row">

                    <label for="email">Alamat Email</label>

                    <div class="input-wrapper">

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= $email ?>"
                            maxlength="150"
                            placeholder="Masukkan alamat email"
                            required
                        >

                        <small>
                            Gunakan email yang masih aktif.
                        </small>

                    </div>

                </div>


                <div class="form-row">

                    <label>Status Akun</label>

                    <div class="account-status">
                        <span></span>
                        Aktif
                    </div>

                </div>


                <div class="form-actions">

                    <a href="profil.php" class="cancel-button">
                        Batal
                    </a>

                    <button type="submit" class="save-button">
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>


        <div class="security-note" id="keamanan">

            <div>
                <strong>Keamanan akun</strong>

                <p>
                    Jaga kerahasiaan informasi login Anda.
                    Jangan membagikan kata sandi kepada orang lain.
                </p>
            </div>

        </div>

    </section>

</main>


<footer class="account-footer">

    <span>Siruwa Store</span>

    <p>
        Oleh-Oleh Khas Minangkabau.
        &copy; 2026 Siruwa Store.
    </p>

</footer>

</body>
</html>
