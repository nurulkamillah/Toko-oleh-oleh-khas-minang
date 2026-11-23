
<?php

session_start();

require_once __DIR__ . "/../config/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Metode tidak diizinkan.");
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    header("Location: ../../Frontend/login.html?error=empty");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, nama, email, password
     FROM users
     WHERE email = ?
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if ($user && password_verify($password, $user["password"])) {

    session_regenerate_id(true);

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["nama"] = $user["nama"];
    $_SESSION["email"] = $user["email"];

    header("Location: ../../Frontend/Beranda.php");
    exit;

}

header("Location: ../../Frontend/login.html?error=1");
exit;

?>
