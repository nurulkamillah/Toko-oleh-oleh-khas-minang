
<?php

require_once __DIR__ . "/../Backend/middleware/auth_check.php";
require_once __DIR__ . "/../Backend/config/koneksi.php";

$user_id = (int) $_SESSION["user_id"];

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function rupiah($angka) {
    return "Rp" . number_format((float)$angka, 0, ",", ".");
}

// TOKEN KEAMANAN
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// AMBIL DATA KERANJANG
$stmt = mysqli_prepare(
    $conn,
    "SELECT
        c.id AS cart_id,
        c.product_id,
        c.quantity,
        p.nama,
        p.harga,
        p.gambar,
        p.stok,
        p.is_active
     FROM cart_items c
     JOIN products p ON p.id = c.product_id
     WHERE c.user_id = ?
     ORDER BY c.id DESC"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$items = [];
$subtotal = 0;
$stokValid = true;

while ($row = mysqli_fetch_assoc($result)) {

    $items[] = $row;

    $subtotal +=
        (float)$row["harga"] * (int)$row["quantity"];

    if (
        (int)$row["is_active"] !== 1 ||
        (int)$row["stok"] < (int)$row["quantity"]
    ) {
        $stokValid = false;
    }
}

mysqli_stmt_close($stmt);

$shipping = 15000;
$total = $subtotal + $shipping;

$error = $_GET["error"] ?? "";
$success = $_GET["success"] ?? "";
$order_number = $_GET["order"] ?? "";

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checkout | Siruwa Store</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/checkout.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>

<header>

    <nav class="navbar">

        <a href="Beranda.php" class="logo">
            Siruwa<span>.</span>
        </a>

        <ul class="nav-menu">

            <li><a href="Beranda.php">Beranda</a></li>

            <li><a href="Beranda.php#produk">Produk</a></li>

            <li><a href="Beranda.php#tentang">Tentang Kami</a></li>

        </ul>

        <div class="nav-buttons">

            <a href="keranjang.php" class="cart">
                🛒 Keranjang
            </a>

            <a href="profil.php" class="login-btn">
                Profil Saya
            </a>

        </div>

    </nav>

</header>


<main class="checkout-page">

<?php if ($success === "1"): ?>

    <!-- HALAMAN BERHASIL -->

    <div class="success-card">

        <div class="success-icon">✓</div>

        <p class="checkout-label">PESANAN BERHASIL</p>

        <h1>Terima Kasih!</h1>

        <p>
            Pesanan Anda berhasil dibuat.
            Silakan periksa status pesanan melalui halaman riwayat pesanan.
        </p>

        <div class="order-number">

            Nomor Pesanan

            <strong><?= e($order_number) ?></strong>

        </div>

        <a href="riwayat-pesanan.php" class="checkout-button">
            Lihat Pesanan Saya
        </a>

        <a href="Beranda.php" class="back-cart">
            Kembali ke Beranda
        </a>

    </div>


<?php elseif (count($items) === 0): ?>

    <!-- KERANJANG KOSONG -->

    <div class="empty-checkout">

        <div>🛒</div>

        <h2>Keranjang Anda Masih Kosong</h2>

        <p>
            Silakan pilih produk terlebih dahulu sebelum melakukan checkout.
        </p>

        <a href="Beranda.php#produk" class="checkout-button">
            Mulai Belanja
        </a>

    </div>


<?php else: ?>

    <!-- JUDUL CHECKOUT -->

    <div class="checkout-heading">

        <p class="checkout-label">
            SIRUWA STORE
        </p>

        <h1>Checkout Pesanan</h1>

        <p>
            Lengkapi informasi pengiriman dan pembayaran Anda.
        </p>

    </div>


    <?php if ($error !== ""): ?>

        <div class="checkout-alert">
            <?= e($error) ?>
        </div>

    <?php endif; ?>


    <form
        action="../Backend/order/proses_checkout.php"
        method="POST"
    >

        <input
            type="hidden"
            name="csrf_token"
            value="<?= e($_SESSION["csrf_token"]) ?>"
        >

        <div class="checkout-layout">

            <!-- FORM PENGIRIMAN -->

            <div class="checkout-main">

                <section class="checkout-card">

                    <div class="card-heading">

                        <span>01</span>

                        <div>

                            <h2>Informasi Pengiriman</h2>

                            <p>
                                Masukkan alamat tujuan pengiriman pesanan.
                            </p>

                        </div>

                    </div>


                    <div class="form-grid">

                        <div class="form-group full">

                            <label>Nama Penerima</label>

                            <input
                                type="text"
                                name="recipient_name"
                                value="<?= e($_SESSION["nama"] ?? "") ?>"
                                placeholder="Nama lengkap penerima"
                                maxlength="150"
                                required
                            >

                        </div>


                        <div class="form-group full">

                            <label>Nomor Telepon</label>

                            <input
                                type="tel"
                                name="phone"
                                placeholder="Contoh: 081234567890"
                                maxlength="20"
                                required
                            >

                        </div>


                        <div class="form-group full">

                            <label>Alamat Lengkap</label>

                            <textarea
                                name="address"
                                rows="3"
                                placeholder="Nama jalan, nomor rumah, RT/RW, dan detail alamat"
                                required
                            ></textarea>

                        </div>


                        <div class="form-group">

                            <label>Kecamatan</label>

                            <input
                                type="text"
                                name="kecamatan"
                                placeholder="Kecamatan"
                                maxlength="100"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label>Kota / Kabupaten</label>

                            <input
                                type="text"
                                name="kota"
                                placeholder="Kota atau Kabupaten"
                                maxlength="100"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label>Provinsi</label>

                            <input
                                type="text"
                                name="provinsi"
                                placeholder="Provinsi"
                                maxlength="100"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label>Kode Pos</label>

                            <input
                                type="text"
                                name="kode_pos"
                                placeholder="Kode pos"
                                maxlength="10"
                            >

                        </div>


                        <div class="form-group full">

                            <label>Catatan Pesanan (Opsional)</label>

                            <textarea
                                name="notes"
                                rows="2"
                                placeholder="Catatan tambahan untuk penjual"
                            ></textarea>

                        </div>

                    </div>

                </section>


                <!-- METODE PEMBAYARAN -->

                <section class="checkout-card">

                    <div class="card-heading">

                        <span>02</span>

                        <div>

                            <h2>Metode Pembayaran</h2>

                            <p>
                                Pilih metode pembayaran yang tersedia.
                            </p>

                        </div>

                    </div>


                    <div class="payment-options">

                        <label class="payment-option">

                            <input
                                type="radio"
                                name="payment_method"
                                value="transfer"
                                required
                            >

                            <div class="payment-content">

                                <span class="payment-icon">🏦</span>

                                <div>

                                    <strong>Transfer Bank</strong>

                                    <p>
                                        Pembayaran melalui transfer bank.
                                    </p>

                                </div>

                            </div>

                        </label>


                        <label class="payment-option">

                            <input
                                type="radio"
                                name="payment_method"
                                value="qris"
                            >

                            <div class="payment-content">

                                <span class="payment-icon">📱</span>

                                <div>

                                    <strong>QRIS</strong>

                                    <p>
                                        Pembayaran menggunakan QRIS.
                                    </p>

                                </div>

                            </div>

                        </label>


                        <label class="payment-option">

                            <input
                                type="radio"
                                name="payment_method"
                                value="cod"
                            >

                            <div class="payment-content">

                                <span class="payment-icon">📦</span>

                                <div>

                                    <strong>Cash on Delivery (COD)</strong>

                                    <p>
                                        Pembayaran ketika pesanan diterima.
                                    </p>

                                </div>

                            </div>

                        </label>

                    </div>

                </section>

            </div>


            <!-- RINGKASAN PESANAN -->

            <aside class="checkout-summary">

                <h2>Ringkasan Pesanan</h2>


                <?php foreach ($items as $item): ?>

                    <?php
                    $gambar = basename(str_replace("\\", "/", $item["gambar"] ?? ""));
                    $gambarPath = "images/" . $gambar;
                    ?>

                   
<div class="summary-product">

    <img
        src="<?= e($gambarPath) ?>"
        alt="<?= e($item["nama"]) ?>"
        onerror="this.style.display='none'"
    >

    <div class="summary-product-info">

        <strong>
            <?= e($item["nama"]) ?>
        </strong>

        <p>
            <?= (int)$item["quantity"] ?> x
            <?= rupiah($item["harga"]) ?>
        </p>

    </div>

    <strong>
        <?= rupiah(
            (float)$item["harga"] *
            (int)$item["quantity"]
        ) ?>
    </strong>

</div>


                            <strong>
                                <?= e($item["nama"]) ?>
                            </strong>

                            <p>
                                <?= (int)$item["quantity"] ?> x
                                <?= rupiah($item["harga"]) ?>
                            </p>

                        </div>

                        <strong>
                            <?= rupiah(
                                (float)$item["harga"] *
                                (int)$item["quantity"]
                            ) ?>
                        </strong>

                    </div>

                <?php endforeach; ?>


                <div class="summary-line">

                    <span>Subtotal Produk</span>

                    <strong><?= rupiah($subtotal) ?></strong>

                </div>


                <div class="summary-line">

                    <span>Biaya Pengiriman</span>

                    <strong><?= rupiah($shipping) ?></strong>

                </div>


                <div class="summary-total">

                    <span>Total Pembayaran</span>

                    <strong><?= rupiah($total) ?></strong>

                </div>


                <?php if (!$stokValid): ?>

                    <div class="checkout-alert">
                        Ada produk yang stoknya tidak mencukupi atau tidak tersedia.
                        Periksa kembali keranjang Anda.
                    </div>

                <?php endif; ?>


                <button
                    type="submit"
                    class="checkout-button"
                    <?= !$stokValid ? "disabled" : "" ?>
                >
                    Buat Pesanan
                </button>


                <p class="secure-note">
                    Pastikan alamat dan pesanan sudah benar.
                </p>


                <a href="keranjang.php" class="back-cart">
                    ← Kembali ke Keranjang
                </a>

            </aside>

        </div>

    </form>

<?php endif; ?>

</main>


<footer>

    <h2>Siruwa<span>Store</span>.</h2>

    <p>
        Melestarikan cita rasa dan budaya Minangkabau.
    </p>

    <p class="copyright">
        &copy; 2026 Siruwa Store. All Rights Reserved.
    </p>

</footer>

</body>
</html>
