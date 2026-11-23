 
<?php
session_start();

$isLogin = isset($_SESSION["user_id"]);

$nama = $isLogin
    ? htmlspecialchars($_SESSION["nama"] ?? "Pelanggan", ENT_QUOTES, "UTF-8")
    : "";

$produk = [
    [
        "nama" => "Rendang Minang",
        "kategori" => "Makanan Khas",
        "harga" => "Rp85.000",
        "gambar" => "images/rendang.jpeg",
        "slug" => "rendang",
        "label" => "BEST SELLER"
    ],
    [
        "nama" => "Kue Sapik",
        "kategori" => "Kue Tradisional",
        "harga" => "Rp25.000",
        "gambar" => "images/kuesapik.jpeg",
        "slug" => "kue-sapik",
        "label" => ""
    ],
    [
        "nama" => "Karak Kaliang",
        "kategori" => "Camilan Tradisional",
        "harga" => "Rp35.000",
        "gambar" => "images/karakkaliang.jpeg",
        "slug" => "karak-kaliang",
        "label" => "FAVORIT"
    ],
    [
        "nama" => "Kerupuk Jangek",
        "kategori" => "Camilan Tradisional",
        "harga" => "Rp55.000",
        "gambar" => "images/kerupukjangek.jpeg",
        "slug" => "kerupuk-jangek",
        "label" => ""
    ],
    [
        "nama" => "Pisang Salai",
        "kategori" => "Camilan Tradisional",
        "harga" => "Rp35.000",
        "gambar" => "images/pisangsalai.jpeg",
        "slug" => "pisang-salai",
        "label" => ""
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Siruwa Store | Oleh-Oleh Khas Minangkabau</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<!-- NAVBAR -->

<header class="site-header">
    <nav class="navbar">

        <a href="Beranda.php" class="logo">
            Siruwa<span>.</span>
        </a>

        <ul class="nav-menu">
            <li><a href="Beranda.php" class="active">Beranda</a></li>
            <li><a href="#produk">Produk</a></li>
            <li><a href="#tentang">Tentang Kami</a></li>
        </ul>

        <div class="nav-buttons">

            <a href="keranjang.php" class="cart">
                🛒 Keranjang
            </a>

            <?php if ($isLogin): ?>
                <a href="profil.php" class="profile-nav">
                    👤 Profil Saya
                </a>
            <?php else: ?>
                <a href="login.html" class="profile-nav">
                    Login
                </a>
            <?php endif; ?>

        </div>

    </nav>
</header>


<!-- HERO EDITORIAL -->

<section class="lux-hero" id="beranda">

    <div class="lux-hero-content">

        <span class="lux-eyebrow">
            AUTHENTIC MINANGKABAU TASTE
        </span>

        <div class="lux-line"></div>

        <h1>
            Warisan Rasa,<br>
            <span>Istimewa di</span><br>
            Setiap Sajian.
        </h1>

        <p>
            Jelajahi kekayaan kuliner Minangkabau melalui
            pilihan oleh-oleh autentik dengan cita rasa
            tradisional dan kualitas terbaik.
        </p>

        <div class="lux-hero-buttons">

            <a href="#produk" class="lux-primary-btn">
                Jelajahi Koleksi
                <span>→</span>
            </a>

            <a href="#tentang" class="lux-secondary-btn">
                Cerita Siruwa
            </a>

        </div>

        <div class="lux-hero-note">
            <span></span>
            MENGHADIRKAN KELEZATAN KHAS SUMATERA BARAT
        </div>

    </div>

    <div class="lux-hero-visual">

        <div class="lux-image-frame">

            <img
                src="images/rendang.jpeg"
                alt="Rendang autentik Minangkabau"
            >

            <div class="lux-image-caption">
                <span>01 / SIGNATURE COLLECTION</span>
                <strong>Rendang Minangkabau</strong>
            </div>

        </div>

        <div class="lux-floating-text">
            AUTHENTIC<br>
            HERITAGE
        </div>

    </div>

</section>


<!-- INTRODUCTION -->

<section class="lux-intro">

     

    <p>
        Dari tanah Minangkabau, untuk dinikmati di mana saja.
    </p>
 

</section>


<!-- PRODUK -->

<section class="lux-products" id="produk">

    <div class="lux-section-heading">

        <div>
            <span class="lux-section-label">
                THE COLLECTION
            </span>

            <h2>
                Pilihan Rasa <span>Minang</span>
            </h2>

            <p>
                Temukan keistimewaan setiap produk pilihan
                yang membawa cita rasa tradisi Minangkabau.
            </p>
        </div>

        <a href="#produk" class="lux-view-all">
            Jelajahi Semua Produk →
        </a>

    </div>


    <div class="lux-product-grid">

        <?php foreach ($produk as $item): ?>

            <article class="lux-product-card">

                <a
                    href="detail-produk.html?produk=<?= htmlspecialchars($item['slug'], ENT_QUOTES, 'UTF-8') ?>"
                    class="lux-product-image"
                >

                    <img
                        src="<?= htmlspecialchars($item['gambar'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8') ?>"
                        loading="lazy"
                    >

                    <?php if ($item['label'] !== ''): ?>
                        <span class="lux-product-label">
                            <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    <?php endif; ?>

                </a>

                <div class="lux-product-info">

                    <span class="lux-product-category">
                        <?= htmlspecialchars($item['kategori'], ENT_QUOTES, 'UTF-8') ?>
                    </span>

                    <h3>
                        <?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8') ?>
                    </h3>

                    <div class="lux-product-bottom">

                        <strong>
                            <?= htmlspecialchars($item['harga'], ENT_QUOTES, 'UTF-8') ?>
                        </strong>

                        <a
                            href="detail-produk.html?produk=<?= htmlspecialchars($item['slug'], ENT_QUOTES, 'UTF-8') ?>"
                            class="lux-detail-link"
                        >
                            Lihat Detail →
                        </a>

                    </div>

                </div>

            </article>

        <?php endforeach; ?>

    </div>

</section>


<!-- ABOUT -->

<section class="lux-about" id="tentang">

    <div class="lux-about-decoration">
        SIRUWA
    </div>

    <div class="lux-about-content">

        <span class="lux-section-label">
            OUR STORY
        </span>

        <h2>
            Mengenal Lebih Dekat
            <span>Warisan Kuliner Minangkabau</span>
        </h2>

        <p>
            Siruwa Store hadir untuk memperkenalkan kekayaan
            kuliner Sumatera Barat melalui berbagai pilihan
            oleh-oleh khas Minangkabau.

            Kami percaya bahwa setiap produk memiliki cerita,
            tradisi, dan cita rasa yang layak untuk dinikmati
            serta dikenal lebih luas.
        </p>

        <a href="#produk" class="lux-about-btn">
            Temukan Produk Kami →
        </a>

    </div>

</section>


<!-- FOOTER -->

<footer class="lux-footer">

    <div class="lux-footer-main">

        <div class="lux-footer-brand">

            <h2>Siruwa<span>.</span></h2>

            <p>
                Melestarikan cita rasa dan budaya
                Minangkabau melalui pilihan kuliner autentik.
            </p>

        </div>

        <div class="lux-footer-links">

            <h3>Navigasi</h3>

            <a href="Beranda.php">Beranda</a>
            <a href="#produk">Produk</a>
            <a href="#tentang">Tentang Kami</a>

        </div>

        <div class="lux-footer-links">

            <h3>Layanan</h3>

            <a href="keranjang.php">Keranjang Belanja</a>

            <?php if ($isLogin): ?>
                <a href="profil.php">Profil Saya</a>
            <?php else: ?>
                <a href="login.html">Login Pelanggan</a>
            <?php endif; ?>

        </div>

    </div>

    <div class="lux-footer-bottom">

        <p>
            © 2026 Siruwa Store. All Rights Reserved.
        </p>

        <span>
            MADE WITH LOVE IN MINANGKABAU
        </span>

    </div>

</footer>

<script src="js/script.js"></script>

</body>
</html>
