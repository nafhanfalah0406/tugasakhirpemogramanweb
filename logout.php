<?php
session_start();

// Menghapus semua data session
$_SESSION = array();

// Menghancurkan session yang ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Mengalihkan pengguna kembali ke halaman login
header("Location: login.php");
exit;
?>