<?php
$hostname = "localhost"; // Atur hostname
$username = "root"; // Atur username, defaultnya biasanya 'root'
$password = ""; // Atur password, defaultnya biasanya kosong untuk XAMPP
$db_name = "kartun"; // Ganti dengan nama database yang kamu gunakan

// Koneksi ke database
$db = new mysqli($hostname, $username, $password, $db_name);

// Cek koneksi
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>
