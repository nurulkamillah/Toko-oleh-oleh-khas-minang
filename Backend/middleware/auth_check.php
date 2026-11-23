
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function wajibLogin() {

    if (!isset($_SESSION["user_id"])) {

        header("Location: ../login.html?error=login_required");
        exit;

    }

}

function sudahLogin() {

    return isset($_SESSION["user_id"]);

}

?>
