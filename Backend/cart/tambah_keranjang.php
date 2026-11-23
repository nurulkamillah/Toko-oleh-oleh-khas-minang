
<?php

session_start();

require_once __DIR__ . "/../config/koneksi.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../Frontend/login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Metode tidak diizinkan.");
}

$user_id = (int) $_SESSION["user_id"];
$slug = trim($_POST["slug"] ?? "");
$quantity = filter_var($_POST["quantity"] ?? 1, FILTER_VALIDATE_INT);

if ($slug === "" || $quantity === false || $quantity < 1) {
    header("Location: ../../Frontend/Beranda.php?error=invalid");
    exit;
}

mysqli_begin_transaction($conn);

try {

    // Cari produk dan periksa stok
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, stok, is_active
         FROM products
         WHERE slug = ?
         FOR UPDATE"
    );

    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$product || (int)$product["is_active"] !== 1) {
        throw new Exception("Produk tidak tersedia.");
    }

    if ((int)$product["stok"] < 1) {
        throw new Exception("Stok produk sedang habis.");
    }

    $product_id = (int)$product["id"];

    // Periksa apakah produk sudah ada di keranjang
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, quantity
         FROM cart_items
         WHERE user_id = ? AND product_id = ?
         FOR UPDATE"
    );

    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $cart = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    $new_quantity = $quantity;

    if ($cart) {
        $new_quantity += (int)$cart["quantity"];
    }

    if ($new_quantity > (int)$product["stok"]) {
        throw new Exception("Jumlah melebihi stok yang tersedia.");
    }

    if ($cart) {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE cart_items
             SET quantity = ?
             WHERE id = ? AND user_id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iii",
            $new_quantity,
            $cart["id"],
            $user_id
        );

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO cart_items
             (user_id, product_id, quantity)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iii",
            $user_id,
            $product_id,
            $quantity
        );
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Gagal menambahkan produk.");
    }

    mysqli_stmt_close($stmt);

    mysqli_commit($conn);

    header("Location: ../../Frontend/keranjang.php?success=added");
    exit;

} catch (Throwable $e) {

    mysqli_rollback($conn);

    header(
        "Location: ../../Frontend/keranjang.php?error=" .
        urlencode($e->getMessage())
    );
    exit;
}
