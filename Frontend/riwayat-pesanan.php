
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

// LABEL STATUS PESANAN
function statusPesanan($status) {
    $statuses = [
        "pending"    => "Menunggu Konfirmasi",
        "processing" => "Sedang Diproses",
        "shipped"    => "Sedang Dikirim",
        "completed"  => "Selesai",
        "cancelled"  => "Dibatalkan"
    ];

    return $statuses[strtolower($status)] ?? ucfirst($status);
}

// LABEL STATUS PEMBAYARAN
function statusPembayaran($status) {
    $statuses = [
        "pending" => "Belum Dibayar",
        "paid"    => "Sudah Dibayar",
        "failed"  => "Gagal",
        "expired" => "Kedaluwarsa"
    ];

    return $statuses[strtolower($status)] ?? ucfirst($status);
}

// AMBIL PESANAN PELANGGAN
$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM orders
     WHERE user_id = ?
     ORDER BY id DESC"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$orders = [];

while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

mysqli_stmt_close($stmt);

$nama = $_SESSION["nama"] ?? "Pelanggan";

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Pesanan | Siruwa Store</title>

    <link rel="stylesheet" href="css/riwayat-pesanan.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>

<!-- HEADER -->

<header class="history-header">

    <a href="Beranda.php" class="brand">
        Siruwa<span>.</span>
    </a>

    <nav>
        <a href="Beranda.php">Beranda</a>
        <a href="Beranda.php#produk">Produk</a>
        <a href="keranjang.php">Keranjang</a>
    </nav>

    <a href="profil.php" class="account-link">
        Profil Saya
    </a>

</header>


<!-- MAIN -->

<main class="history-container">

    <div class="history-heading">

        <div>
            <span class="heading-label">AKUN PELANGGAN</span>

            <h1>Riwayat Pesanan</h1>

            <p>
                Halo, <?= e($nama) ?>.
                Berikut daftar transaksi belanja Anda di Siruwa Store.
            </p>
        </div>

        <a href="Beranda.php#produk" class="shopping-link">
            Lanjut Belanja
        </a>

    </div>


    <!-- DAFTAR PESANAN -->

    <section class="history-content">

        <div class="history-section-heading">

            <h2>Semua Pesanan</h2>

            <span><?= count($orders) ?> transaksi</span>

        </div>


        <?php if (count($orders) > 0): ?>

            <?php foreach ($orders as $order): ?>

                <?php

                $order_id = (int)$order["id"];

                $order_status = strtolower($order["order_status"] ?? "pending");
                $payment_status = strtolower($order["payment_status"] ?? "pending");

                $tanggal = "";

                if (!empty($order["created_at"])) {
                    $timestamp = strtotime($order["created_at"]);

                    if ($timestamp !== false) {
                        $tanggal = date("d M Y, H:i", $timestamp);
                    }
                }

                $paymentMethods = [
                    "transfer" => "Transfer Bank",
                    "qris" => "QRIS",
                    "cod" => "Cash on Delivery"
                ];

                $paymentMethod = $paymentMethods[
                    strtolower($order["payment_method"] ?? "")
                ] ?? ($order["payment_method"] ?? "-");

                // AMBIL DETAIL PRODUK
                $itemStmt = mysqli_prepare(
                    $conn,
                    "SELECT product_name, price, quantity, subtotal
                     FROM order_items
                     WHERE order_id = ?"
                );

                mysqli_stmt_bind_param($itemStmt, "i", $order_id);
                mysqli_stmt_execute($itemStmt);

                $itemResult = mysqli_stmt_get_result($itemStmt);

                $orderItems = [];

                while ($item = mysqli_fetch_assoc($itemResult)) {
                    $orderItems[] = $item;
                }

                mysqli_stmt_close($itemStmt);

                ?>

                <article class="order-card">

                    <!-- HEADER PESANAN -->

                    <div class="order-header">

                        <div class="order-identity">

                            <span class="order-label">
                                NOMOR PESANAN
                            </span>

                            <strong>
                                <?= e($order["order_number"]) ?>
                            </strong>

                            <small>
                                <?= e($tanggal ?: "Tanggal tidak tersedia") ?>
                            </small>

                        </div>

                        <span class="order-status status-<?= e($order_status) ?>">
                            <?= e(statusPesanan($order_status)) ?>
                        </span>

                    </div>


                    <!-- PRODUK PESANAN -->

                    <div class="order-product-section">

                        <div class="product-section-title">
                            Detail Produk
                        </div>

                        <?php foreach ($orderItems as $item): ?>

                            <div class="order-product">

                                <div class="product-placeholder">
                                    <?= e(strtoupper(substr($item["product_name"], 0, 1))) ?>
                                </div>

                                <div class="product-description">

                                    <strong>
                                        <?= e($item["product_name"]) ?>
                                    </strong>

                                    <small>
                                        <?= (int)$item["quantity"] ?> x
                                        <?= rupiah($item["price"]) ?>
                                    </small>

                                </div>

                                <strong class="product-subtotal">
                                    <?= rupiah($item["subtotal"]) ?>
                                </strong>

                            </div>

                        <?php endforeach; ?>

                    </div>


                    <!-- INFORMASI PEMBAYARAN -->

                    <div class="order-payment">

                        <div>
                            <span>Metode Pembayaran</span>
                            <strong><?= e($paymentMethod) ?></strong>
                        </div>

                        <div>
                            <span>Status Pembayaran</span>

                            <strong class="payment-status payment-<?= e($payment_status) ?>">
                                <?= e(statusPembayaran($payment_status)) ?>
                            </strong>
                        </div>

                    </div>


                    <!-- TOTAL -->

                    <div class="order-footer">

                        <div>
                            <span>Total Pesanan</span>

                            <strong>
                                <?= rupiah($order["total"]) ?>
                            </strong>
                        </div>

                        <a href="Beranda.php#produk" class="order-again">
                            Belanja Lagi
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        <?php else: ?>

            <!-- KERANJANG PESANAN KOSONG -->

            <div class="empty-history">

                <div class="empty-icon">▤</div>

                <h3>Belum Ada Pesanan</h3>

                <p>
                    Anda belum memiliki transaksi di Siruwa Store.
                    Yuk, temukan oleh-oleh khas Minangkabau favorit Anda.
                </p>

                <a href="Beranda.php#produk">
                    Mulai Belanja
                </a>

            </div>

        <?php endif; ?>

    </section>

</main>


<!-- FOOTER -->

<footer class="history-footer">

    <strong>Siruwa Store</strong>

    <p>
        Melestarikan cita rasa dan budaya Minangkabau.
        &copy; 2026 Siruwa Store.
    </p>

</footer>

</body>
</html>
