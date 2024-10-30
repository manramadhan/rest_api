<?php
<<<<<<< HEAD
include '../film-kartun-favorit/db/database.php';  
include '../film-kartun-favorit/functions.php';
=======
include '../db/database.php';  
include 'functions.php';
>>>>>>> cc7d696d4007fa85a008c64e920145611c5a1a5c

// Menghubungkan ke database
$db = new mysqli($hostname, $username, $password, $db_name);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Mengambil data film
$movies = fetchMovies($db);

// Fungsi untuk mengatur ulang nomor urut
function resetMovieIds($db) {
    $sql = "SET @count = 0; UPDATE movies SET movie_id = @count := (@count + 1);";
    $db->query($sql);
}

// Menangani penghapusan film
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    if (deleteMovie($db, $id)) {
        resetMovieIds($db); 
        header('Location: index.php'); 
        exit;
    }
}

// Menangani penilaian film
if (isset($_POST['rating']) && isset($_POST['movie_id'])) {
    $rating = intval($_POST['rating']);
    $movie_id = intval($_POST['movie_id']);
    
    // Update rating di database
    $sql = "UPDATE movies SET rating = ? WHERE movie_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $rating, $movie_id);
    $stmt->execute();
    
    // Menutup koneksi
    $stmt->close();
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Film Favorit</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Daftar Film Kartun Favorit</h1>
        <a href="add.php" class="btn">Tambah Film</a>
        <table class="table-responsive">
            <tr>
                <th>Nomer</th>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Tahun Rilis</th> 
                <th>Rating</th>
                <th>Foto</th>
                <th>Aksi</th>
            </tr>
            <?php $no = 1; while ($movie = $movies->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $movie['title']; ?></td>
                    <td><?= $movie['description']; ?></td>
                    <td><?= $movie['release_year']; ?></td> 
                    <td class="rating"> <!-- Tampilan Rating menggunakan bintang -->
                        <form method="POST" action="index.php">
                            <?php
                            // Menampilkan bintang sesuai rating
                            for ($i = 1; $i <= 5; $i++) {
                                echo '<label class="star" style="cursor:pointer;">';
                                echo '<input type="radio" name="rating" value="' . $i . '" ' . ($i == $movie['rating'] ? 'checked' : '') . ' onchange="this.form.submit();" style="display:none;">'; // Hidden radio input
                                echo ($i <= $movie['rating'] ? '★' : '☆'); // Menampilkan bintang
                                echo '</label>';
                            }
                            ?>
                            <input type="hidden" name="movie_id" value="<?= $movie['movie_id']; ?>">
                        </form>
                    </td>
                    <td><img src="<?= $movie['image']; ?>" alt="<?= $movie['title']; ?>" width="100"></td>
                    <td>
                        <a href="edit.php?id=<?= $movie['movie_id']; ?>" class="btn">Edit</a>
                        <a href="?action=delete&id=<?= $movie['movie_id']; ?>" class="btn" onclick="return confirm('Anda yakin ingin menghapus film ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
