
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

if (!$cart_id) {
    header("Location: ../../Frontend/keranjang.php?error=invalid");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM cart_items
     WHERE id = ? AND user_id = ?"
);

mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);
mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

header("Location: ../../Frontend/keranjang.php?success=deleted");
exit;

?>
