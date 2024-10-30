<?php
include '../db/database.php'; // Pastikan ini diimpor sebelum fungsi
include '../api/helper.php';

// Fetch all movies
function fetchMovies($db) {
    $sql = "SELECT movies.*, categories.category_name AS category_name 
            FROM movies 
            LEFT JOIN categories ON movies.category_id = categories.category_id";
    
    $result = $db->query($sql);
    
    if (!$result) {
        die("Error fetching movies: " . $db->error); // Debugging error
    }
    
    return $result->fetch_all(MYSQLI_ASSOC); // Mengembalikan hasil query sebagai array asosiatif
}

// Fetch all categories
function fetchCategories($db) {
    $sql = "SELECT * FROM categories";
    $result = $db->query($sql);
    
    if (!$result) {
        die("Error fetching categories: " . $db->error); // Debugging error
    }
    
    return $result->fetch_all(MYSQLI_ASSOC); // Mengembalikan hasil query sebagai array asosiatif
}

// Tambah Film
function addMovie($db, $title, $description, $rating, $release_year, $image) {
    // Tentukan jalur untuk menyimpan gambar
    $imagePath = '../uploads/' . $image; 

    // Pindahkan file gambar ke folder uploads
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
        return [
            'status' => false,
            'message' => 'Gagal meng-upload gambar'
        ];
    }

    $stmt = $db->prepare("INSERT INTO movies (title, description, image, rating, release_year) VALUES (?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        die("Prepare statement failed: " . $db->error); 
    }

    $stmt->bind_param("ssssi", $title, $description, $imagePath, $rating, $release_year);
    
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error); 
    }

    return [
        'status' => true,
        'message' => 'Film berhasil ditambahkan'
    ];
}

// Update Film
function updateMovie($db, $id, $title, $description, $rating, $release_year, $image = null) {
    if ($image) {
        $imagePath = '../uploads/' . $image;
        $sql = "UPDATE movies SET title=?, description=?, image=?, rating=?, release_year=? WHERE movie_id=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssisi", $title, $description, $imagePath, $rating, $release_year, $id);
    } else {
        $sql = "UPDATE movies SET title=?, description=?, rating=?, release_year=? WHERE movie_id=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssisi", $title, $description, $rating, $release_year, $id);
    }
    
    if (!$stmt) {   
        die("Prepare statement failed: " . $db->error); 
    }

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error); 
    }

    return true; 
}

// Hapus Film
function deleteMovie($db, $id) {
    $sql = "DELETE FROM movies WHERE movie_id = ?";
    $stmt = $db->prepare($sql);
    
    if (!$stmt) {
        die("Prepare statement failed: " . $db->error);
    }
    
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error); 
    }

    return true; // Mengembalikan true jika berhasil dihapus
}

// Menangani permintaan untuk mengambil film
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['type'])) {
    header('Content-Type: application/json'); // Set header JSON
    $movies = fetchMovies($db);
    $response = [
        'status' => true,
        'data' => $movies
    ];
    echo json_encode($response);
}

// Menangani permintaan untuk mengambil kategori
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type']) && $_GET['type'] === 'categories') {
    header('Content-Type: application/json'); // Set header JSON
    $categories = fetchCategories($db);
    $response = [
        'status' => true,
        'data' => $categories
    ];
    echo json_encode($response);
}

// Menangani permintaan untuk menambahkan film
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan ada file gambar yang diupload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $rating = $_POST['rating'];
        $release_year = $_POST['release_year'];
        $image = $_FILES['image']['name']; // Ambil nama file gambar
        
        $result = addMovie($db, $title, $description, $rating, $release_year, $image);
        echo json_encode($result);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'File gambar tidak valid'
        ]);
    }
}

// Menangani permintaan untuk memperbarui film
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data); // Mengambil data dari request body

    // Siapkan array untuk menampung pesan kesalahan
    $missingFields = [];

    // Cek setiap parameter yang diperlukan dan tambahkan ke $missingFields jika tidak ada
    if (empty($data['movie_id'])) {
        $missingFields[] = 'movie_id';
    }
    if (empty($data['title'])) {
        $missingFields[] = 'title';
    }
    if (empty($data['description'])) {
        $missingFields[] = 'description';
    }
    if (empty($data['rating'])) {
        $missingFields[] = 'rating';
    }
    if (empty($data['release_year'])) {
        $missingFields[] = 'release_year';
    }

    // Jika ada field yang hilang, kembalikan respons dengan daftar field yang hilang
    if (!empty($missingFields)) {
        $response = [
            'status' => false,
            'message' => 'Field yang hilang: ' . implode(', ', $missingFields)
        ];
    } else {
        $movie_id = $data['movie_id'];
        $title = $data['title'];
        $description = $data['description'];
        $rating = $data['rating'];
        $release_year = $data['release_year'];
        $image = null;

        // Cek apakah ada file gambar yang diupload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image']['name']; // Ambil nama file gambar
        }

        if (updateMovie($db, $movie_id, $title, $description, $rating, $release_year, $image)) {
            // Pindahkan file gambar jika ada
            if ($image) {
                $imagePath = '../uploads/' . $image;
                move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
            }

            $response = [
                'status' => true,
                'message' => 'Film berhasil diperbarui'
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Gagal memperbarui film'
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

// Menangani permintaan untuk menghapus film
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data); // Mengambil data dari request body
    if (isset($data['movie_id'])) {
        $movie_id = $data['movie_id']; // Mendapatkan ID film dari data

        // Panggil fungsi deleteMovie
        if (deleteMovie($db, $movie_id)) {
            $response = [
                'status' => true,
                'message' => 'Film berhasil dihapus'
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Gagal menghapus film'
            ];
        }
    } else {
        $response = [
            'status' => false,
            'message' => 'ID film tidak diberikan'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}
