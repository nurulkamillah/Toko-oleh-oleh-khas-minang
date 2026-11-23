
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

$userId = $_SESSION["user_id"];

$nama = trim($_POST["nama"] ?? "");
$email = trim($_POST["email"] ?? "");

if ($nama === "" || $email === "") {
    header("Location: ../../Frontend/edit-profil.php?error=empty");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../../Frontend/edit-profil.php?error=email");
    exit;
}

// Periksa apakah email digunakan akun lain
$stmt = mysqli_prepare(
    $conn,
    "SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "si", $email, $userId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    mysqli_stmt_close($stmt);

    header("Location: ../../Frontend/edit-profil.php?error=exists");
    exit;
}

mysqli_stmt_close($stmt);

// Update data pengguna
$stmt = mysqli_prepare(
    $conn,
    "UPDATE users SET nama = ?, email = ? WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ssi",
    $nama,
    $email,
    $userId
);

if (mysqli_stmt_execute($stmt)) {

    $_SESSION["nama"] = $nama;
    $_SESSION["email"] = $email;

    mysqli_stmt_close($stmt);

    header("Location: ../../Frontend/profil.php?success=updated");
    exit;
}

mysqli_stmt_close($stmt);

header("Location: ../../Frontend/edit-profil.php?error=failed");
exit;

?>
