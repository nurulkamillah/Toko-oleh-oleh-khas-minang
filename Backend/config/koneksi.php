
<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "siruwa_store";

$conn = mysqli_connect(
    $host,
    $username,
    $password,
    $database
);

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    http_response_code(500);
    exit("Terjadi gangguan koneksi database.");
}

mysqli_set_charset($conn, "utf8mb4");

?>
