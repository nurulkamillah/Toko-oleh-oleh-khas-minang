
<?php

session_start();

require_once __DIR__ . "/../config/koneksi.php";

// 1. Periksa login
if (!isset($_SESSION["user_id"])) {
    header("Location: ../../Frontend/login.html?error=login_required");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Metode tidak diizinkan.");
}

$user_id = (int) $_SESSION["user_id"];

// 2. Validasi token keamanan
$csrf = $_POST["csrf_token"] ?? "";

if (
    empty($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $csrf)
) {
    exit("Permintaan tidak valid. Silakan kembali ke checkout.");
}

// 3. Ambil data pelanggan
$nama = trim($_POST["recipient_name"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$alamat = trim($_POST["address"] ?? "");
$kecamatan = trim($_POST["kecamatan"] ?? "");
$kota = trim($_POST["kota"] ?? "");
$provinsi = trim($_POST["provinsi"] ?? "");
$kode_pos = trim($_POST["kode_pos"] ?? "");
$catatan = trim($_POST["notes"] ?? "");
$payment = $_POST["payment_method"] ?? "";

if (
    $nama === "" ||
    $phone === "" ||
    $alamat === "" ||
    $kecamatan === "" ||
    $kota === "" ||
    $provinsi === ""
) {
    header("Location: ../../Frontend/checkout.php?error=required");
    exit;
}

// 4. Validasi metode pembayaran
$metodeValid = ["transfer", "qris", "cod"];

if (!in_array($payment, $metodeValid, true)) {
    header("Location: ../../Frontend/checkout.php?error=payment");
    exit;
}

// 5. Biaya pengiriman sementara
// Nanti dapat disesuaikan dengan sistem ongkir.
$shipping_cost = 15000;

mysqli_begin_transaction($conn);

try {

    // 6. Ambil keranjang dan kunci data produk
    $stmt = mysqli_prepare(
        $conn,
        "SELECT
            c.product_id,
            c.quantity,
            p.nama,
            p.harga,
            p.stok,
            p.is_active
         FROM cart_items c
         JOIN products p ON p.id = c.product_id
         WHERE c.user_id = ?
         FOR UPDATE"
    );

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $items = [];
    $subtotal = 0;

    while ($row = mysqli_fetch_assoc($result)) {

        if (
            (int)$row["is_active"] !== 1 ||
            (int)$row["quantity"] < 1 ||
            (int)$row["stok"] < (int)$row["quantity"]
        ) {
            throw new Exception(
                "Stok produk " . $row["nama"] . " tidak mencukupi."
            );
        }

        $items[] = $row;

        $subtotal +=
            (float)$row["harga"] * (int)$row["quantity"];
    }

    mysqli_stmt_close($stmt);

    if (count($items) === 0) {
        throw new Exception("Keranjang belanja masih kosong.");
    }

    $total = $subtotal + $shipping_cost;

    // 7. Buat nomor pesanan
    $order_number = "SRW-" . date("YmdHis") .
                    "-" . strtoupper(bin2hex(random_bytes(3)));

    // 8. Simpan pesanan
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO orders (
            user_id,
            order_number,
            recipient_name,
            phone,
            address,
            kecamatan,
            kota,
            provinsi,
            kode_pos,
            notes,
            subtotal,
            shipping_cost,
            total,
            payment_method,
            payment_status,
            order_status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending'
        )"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "isssssssssddds",
        $user_id,
        $order_number,
        $nama,
        $phone,
        $alamat,
        $kecamatan,
        $kota,
        $provinsi,
        $kode_pos,
        $catatan,
        $subtotal,
        $shipping_cost,
        $total,
        $payment
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Gagal menyimpan pesanan.");
    }

    $order_id = mysqli_insert_id($conn);

    mysqli_stmt_close($stmt);

    // 9. Simpan detail produk pesanan
    foreach ($items as $item) {

        $product_id = (int)$item["product_id"];
        $product_name = $item["nama"];
        $price = (float)$item["harga"];
        $quantity = (int)$item["quantity"];
        $item_subtotal = $price * $quantity;

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO order_items (
                order_id,
                product_id,
                product_name,
                price,
                quantity,
                subtotal
            ) VALUES (?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iisdid",
            $order_id,
            $product_id,
            $product_name,
            $price,
            $quantity,
            $item_subtotal
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal menyimpan detail pesanan.");
        }

        mysqli_stmt_close($stmt);

        // 10. Kurangi stok produk
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE products
             SET stok = stok - ?
             WHERE id = ? AND stok >= ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iii",
            $quantity,
            $product_id,
            $quantity
        );

        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) !== 1) {
            throw new Exception("Gagal memperbarui stok produk.");
        }

        mysqli_stmt_close($stmt);
    }

    // 11. Kosongkan keranjang pelanggan
    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM cart_items WHERE user_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // 12. Simpan seluruh transaksi
    mysqli_commit($conn);

    header(
        "Location: ../../Frontend/checkout.php?success=1&order=" .
        urlencode($order_number)
    );
    exit;

} catch (Throwable $e) {

    mysqli_rollback($conn);

    header(
        "Location: ../../Frontend/checkout.php?error=" .
        urlencode($e->getMessage())
    );
    exit;
}

?>
