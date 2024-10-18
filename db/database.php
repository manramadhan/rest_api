<?php
$hostname = "localhost";
$username = "root"; // Ganti jika perlu
$password = "";     // Ganti jika perlu
$db_name = "kartun"; // Nama database yang digunakan

// Buat koneksi ke database
$conn = new mysqli($hostname, $username, $password);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Buat database jika belum ada
$sql_create_db = "CREATE DATABASE IF NOT EXISTS kartun";
$conn->query($sql_create_db);

// Pilih database
$conn->select_db('kartun');

// Buat tabel movies
$sql_create_movies_table = "CREATE TABLE IF NOT EXISTS movies (
    movie_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    release_year INT(4),
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
)";
$conn->query($sql_create_movies_table);

// Buat tabel ratings (jika ingin menggunakan tabel terpisah untuk rating)
$sql_create_ratings_table = "CREATE TABLE IF NOT EXISTS ratings (
    rating_id INT PRIMARY KEY AUTO_INCREMENT,
    movie_id INT,
    rating DECIMAL(2,1),
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE
)";
$conn->query($sql_create_ratings_table);

// Tutup koneksi
$conn->close();
?>
