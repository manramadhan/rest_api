<?php
include '../film-kartun-favorit/db/database.php';
include '../film-kartun-favorit/functions.php';

// Menghubungkan ke database
$db = new mysqli($hostname, $username, $password, $db_name);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $rating = (float)$_POST['rating']; 
    $release_year = $_POST['release_year']; 
    $image = $_FILES['image']['name']; 

    if (move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image)) {
        echo "Gambar berhasil diupload.<br>";

        if (addMovie($db, $title, $description, $rating, $release_year, $image)) { 
            header('Location: index.php'); 
            exit; 
        } else {
            echo "Gagal menambahkan film.";
        }
    } else {
        echo "Gagal mengupload gambar.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Film</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Tambah Film</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="title">Judul:</label>
        <input type="text" name="title" autofocus autocomplete="off" required>

        <label for="description">Deskripsi:</label>
        <textarea name="description" required></textarea>

        <label for="rating">Rating:</label>
        <input type="number" name="rating" step="0.1" min="0" max="10" required>

        <label for="release_year">Tahun Rilis:</label> <!-- Tambahkan input Tahun Rilis -->
        <input type="number" name="release_year" min="1900" max="<?= date('Y'); ?>" required>

        <label for="image">Gambar:</label>
        <input type="file" name="image" required>

        <button type="submit">Tambah Film</button>
    </form>
        <a href="index.php" class="btn-btn">Kembali ke Daftar Film</a>
    </div>
</body>
</html>
