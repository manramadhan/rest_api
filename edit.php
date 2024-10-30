<?php
include '../film-kartun-favorit/db/database.php';
include '../film-kartun-favorit/functions.php';

$id = $_GET['id'];
$db = new mysqli($hostname, $username, $password, "kartun");

$sql = "SELECT * FROM movies WHERE movie_id = $id";
$result = $db->query($sql);
$movie = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $rating = $_POST['rating']; 
    $release_year = $_POST['release_year'];

    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$image");
        updateMovie($db, $id, $title, $description, $rating, $release_year, $image); // Tambahkan $release_year
    } else {
        updateMovie($db, $id, $title, $description, $rating, $release_year); // Tambahkan $release_year
    }
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Film</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Film Kartun</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="title">Judul:</label>
        <input type="text" name="title" value="<?= $movie['title']; ?>" required>

        <label for="description">Deskripsi:</label>
        <textarea name="description" required><?= $movie['description']; ?></textarea>

        <label for="rating">Rating:</label>
        <input type="number" name="rating" value="<?= $movie['rating']; ?>" step="0.1" min="0" max="10" required>

        <label for="release_year">Tahun Rilis:</label> <!-- Tambahkan input Tahun Rilis -->
        <input type="number" name="release_year" value="<?= $movie['release_year']; ?>" min="1900" max="<?= date('Y'); ?>" required>

        <label for="image">Gambar:</label>
        <input type="file" name="image">

        <button type="submit">Update Film</button>
    </form>
        <a href="index.php" class="btn">Kembali ke Daftar Film</a>
    </div>
</body>
</html>
