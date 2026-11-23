
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html?error=login_required");
    exit;
}

require_once __DIR__ . "/../Backend/config/koneksi.php";

$user_id = (int)$_SESSION["user_id"];

// Ambil produk berdasarkan akun pelanggan
$stmt = mysqli_prepare(
    $conn,
    "SELECT
        c.id AS cart_id,
        c.quantity,
        p.id AS product_id,
        p.nama,
        p.slug,
        p.harga,
        p.gambar,
        p.stok
     FROM cart_items c
     JOIN products p ON p.id = c.product_id
     WHERE c.user_id = ?
     ORDER BY c.created_at DESC"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$items = [];
$subtotal = 0;
$totalItem = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;

    $subtotal += (float)$row["harga"] * (int)$row["quantity"];
    $totalItem += (int)$row["quantity"];
}

mysqli_stmt_close($stmt);

function rupiah($angka) {
    return "Rp" . number_format($angka, 0, ",", ".");
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Keranjang Belanja | Siruwa Store</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/keranjang.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

<header>
    <nav class="navbar">

        <a href="Beranda.php" class="logo">
            Siruwa<span>.</span>
        </a>

        <ul class="nav-menu">
            <li><a href="Beranda.php">Beranda</a></li>
            <li><a href="produk.php">Produk</a></li>
            <li><a href="Beranda.php#tentang">Tentang Kami</a></li>
        </ul>

        <div class="nav-buttons">
            <a href="keranjang.php" class="cart active">
                🛒 Keranjang
            </a>

            <a href="profil.php" class="login-btn">
                👤 <?= e($_SESSION["nama"]) ?>
            </a>
        </div>

    </nav>
</header>

<main class="cart-page">

    <div class="cart-heading">
        <span class="cart-subtitle">SIRUWA SHOPPING BAG</span>
        <h1>Keranjang Belanja</h1>
        <p>Periksa kembali pilihan oleh-oleh khas Minangkabau Anda.</p>
    </div>

    <?php if (isset($_GET["success"])): ?>
        <div class="cart-alert success">
            <?php
            $messages = [
                "added" => "Produk berhasil ditambahkan ke keranjang.",
                "updated" => "Jumlah produk berhasil diperbarui.",
                "deleted" => "Produk berhasil dihapus dari keranjang."
            ];

            echo e($messages[$_GET["success"]] ?? "Berhasil.");
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET["error"])): ?>
        <div class="cart-alert error">
            <?= e($_GET["error"]) ?>
        </div>
    <?php endif; ?>

    <div class="cart-layout">

        <section class="cart-items">

            <div class="cart-section-heading">
                <h2>Produk Pilihan Anda</h2>
                <span><?= $totalItem ?> item</span>
            </div>

            <?php if (count($items) > 0): ?>

                <?php foreach ($items as $item): ?>

                    <div class="cart-item">

                        <img
                            src="<?= e($item["gambar"]) ?>"
                            alt="<?= e($item["nama"]) ?>"
                            onerror="this.src='images/placeholder.png'"
                        >

                        <div class="cart-info">

                            <span class="cart-category">
                                OLEH-OLEH KHAS MINANG
                            </span>

                            <h3><?= e($item["nama"]) ?></h3>

                            <p class="cart-price">
                                <?= rupiah($item["harga"]) ?>
                            </p>

                            <form
                                action="../Backend/cart/update_keranjang.php"
                                method="POST"
                                class="quantity-form"
                            >

                                <input
                                    type="hidden"
                                    name="cart_id"
                                    value="<?= (int)$item["cart_id"] ?>"
                                >

                                <label>Jumlah:</label>

                                <input
                                    type="number"
                                    name="quantity"
                                    value="<?= (int)$item["quantity"] ?>"
                                    min="1"
                                    max="<?= (int)$item["stok"] ?>"
                                    required
                                >

                                <button type="submit" class="update-btn">
                                    Perbarui
                                </button>

                            </form>

                            <small class="stock-info">
                                Stok tersedia: <?= (int)$item["stok"] ?>
                            </small>

                        </div>

                        <div class="cart-item-side">

                            <strong>
                                <?= rupiah($item["harga"] * $item["quantity"]) ?>
                            </strong>

                            <form
                                action="../Backend/cart/hapus_keranjang.php"
                                method="POST"
                                onsubmit="return confirm('Hapus produk ini dari keranjang?')"
                            >

                                <input
                                    type="hidden"
                                    name="cart_id"
                                    value="<?= (int)$item["cart_id"] ?>"
                                >

                                <button type="submit" class="remove-btn">
                                    Hapus
                                </button>

                            </form>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="empty-cart">
                    <div class="empty-icon">🛒</div>

                    <h3>Keranjang Anda Masih Kosong</h3>

                    <p>
                        Temukan berbagai oleh-oleh khas Minangkabau
                        dan tambahkan produk favorit Anda.
                    </p>

                    <a href="Beranda.php#produk" class="shop-btn">
                        Mulai Belanja
                    </a>
                </div>

            <?php endif; ?>

            <a href="Beranda.php#produk" class="continue-shopping">
                ← Lanjut Belanja
            </a>

        </section>

        <aside class="cart-summary">

            <span class="summary-label">ORDER SUMMARY</span>

            <h2>Ringkasan Belanja</h2>

            <div class="summary-row">
                <span>Total Produk</span>
                <strong><?= $totalItem ?> item</strong>
            </div>

            <div class="summary-row">
                <span>Subtotal</span>
                <strong><?= rupiah($subtotal) ?></strong>
            </div>

            <div class="summary-row total">
                <span>Total Pembayaran</span>
                <strong><?= rupiah($subtotal) ?></strong>
            </div>

            <?php if (count($items) > 0): ?>

                <button
                    class="checkout-btn"
                    onclick="window.location.href='checkout.php'"
                >
                    Lanjut ke Pembayaran →
                </button>

            <?php else: ?>

                <button class="checkout-btn" disabled>
                    Keranjang Kosong
                </button>

            <?php endif; ?>

            <p class="secure-note">
                ✦ Belanja mudah dan nyaman bersama Siruwa Store.
            </p>

        </aside>

    </div>

</main>

<footer>
    <h2>Siruwa<span>Store</span>.</h2>
    <p>Melestarikan cita rasa dan budaya Minangkabau.</p>

    <p class="copyright">
        &copy; 2026 Siruwa Store. All Rights Reserved.
    </p>
</footer>

</body>
</html>
