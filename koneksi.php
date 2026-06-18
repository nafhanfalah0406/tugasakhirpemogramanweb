<?php
$host = "localhost";
$user = "root";     // Default user Laragon
$pass = "";         // Default password Laragon (kosong)
$db   = "db_manajemen_kontrak"; // Ganti dengan nama database Anda yang berisi tabel 'users' dan 'contracts'

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek jika koneksi gagal
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>