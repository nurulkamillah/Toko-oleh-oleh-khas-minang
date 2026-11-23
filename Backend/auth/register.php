
<?php

session_start();

require_once __DIR__ . "/../config/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Metode tidak diizinkan.");
}

$nama = trim($_POST["nama"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";

if (
    $nama === "" ||
    $email === "" ||
    $password === "" ||
    $confirmPassword === ""
) {
    exit("Semua kolom wajib diisi.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Format email tidak valid.");
}

if (strlen($password) < 8) {
    exit("Password minimal 8 karakter.");
}

if ($password !== $confirmPassword) {
    exit("Konfirmasi password tidak sama.");
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id FROM users WHERE email = ? LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    mysqli_stmt_close($stmt);
    exit("Email sudah terdaftar.");
}

mysqli_stmt_close($stmt);

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO users (nama, email, password)
     VALUES (?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "sss",
    $nama,
    $email,
    $passwordHash
);

if (mysqli_stmt_execute($stmt)) {

    $userId = mysqli_insert_id($conn);

    session_regenerate_id(true);

    $_SESSION["user_id"] = $userId;
    $_SESSION["nama"] = $nama;
    $_SESSION["email"] = $email;

    mysqli_stmt_close($stmt);

    header("Location: ../../Frontend/Beranda.php");
    exit;

}

mysqli_stmt_close($stmt);

exit("Registrasi gagal. Silakan coba kembali.");

?>
