
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

$user_id = (int)$_SESSION["user_id"];
$cart_id = filter_var($_POST["cart_id"] ?? null, FILTER_VALIDATE_INT);
$quantity = filter_var($_POST["quantity"] ?? null, FILTER_VALIDATE_INT);

if (!$cart_id || !$quantity || $quantity < 1) {
    header("Location: ../../Frontend/keranjang.php?error=invalid");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE cart_items c
     JOIN products p ON p.id = c.product_id
     SET c.quantity = ?
     WHERE c.id = ?
       AND c.user_id = ?
       AND p.is_active = 1
       AND p.stok >= ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "iiii",
    $quantity,
    $cart_id,
    $user_id,
    $quantity
);

mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) >= 0) {
    mysqli_stmt_close($stmt);
    header("Location: ../../Frontend/keranjang.php?success=updated");
    exit;
}

mysqli_stmt_close($stmt);

header("Location: ../../Frontend/keranjang.php?error=stock");
exit;

?>
