
<?php

require_once __DIR__ . "/../Backend/middleware/auth_check.php";

$nama = htmlspecialchars($_SESSION["nama"], ENT_QUOTES, "UTF-8");
$email = htmlspecialchars($_SESSION["email"], ENT_QUOTES, "UTF-8");

$error = $_GET["error"] ?? "";

$messages = [
    "empty" => "Nama dan email wajib diisi.",
    "email" => "Format email tidak valid.",
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

    <link rel="stylesheet" href="css/profil.css">

</head>

<body>

<header class="profile-header">

    <a href="Beranda.php" class="brand">
        Siruwa<span>.</span>
        <small>OLEH-OLEH KHAS MINANG</small>
    </a>

    <a href="profil.php" class="back-home">
        ← Kembali ke Profil
    </a>

</header>

<main class="profile-container">

    <div class="profile-heading">
        <span>PENGATURAN AKUN</span>
        <h1>Edit Profil</h1>
        <p>Perbarui informasi pribadi akun Siruwa Anda.</p>
    </div>

    <section class="profile-card">

        <div class="profile-card-heading">
            <div>
                <span>INFORMASI PRIBADI</span>
                <h2>Ubah Data Akun</h2>
            </div>
        </div>

        <?php if (isset($messages[$error])): ?>
            <p class="form-message">
                <?= htmlspecialchars($messages[$error]) ?>
            </p>
        <?php endif; ?>

        <form action="../Backend/profile/update_profil.php"
              method="POST">

            <div class="edit-form-group">
                <label for="nama">Nama Lengkap</label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="<?= $nama ?>"
                    maxlength="100"
                    required
                >
            </div>

            <div class="edit-form-group">
                <label for="email">Alamat Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= $email ?>"
                    maxlength="150"
                    required
                >
            </div>

            <div class="edit-form-actions">

                <a href="profil.php" class="cancel-button">
                    Batal
                </a>

                <button type="submit" class="save-button">
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </section>

</main>

<footer>
    © 2026 Siruwa Store. Oleh-Oleh Khas Minangkabau.
</footer>

</body>
</html>
