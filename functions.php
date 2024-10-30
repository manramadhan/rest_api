<?php
include '../film-kartun-favorit/db/database.php';

// Fetch all movies
function fetchMovies($db) {
    $sql = "SELECT movies.*, categories.category_name AS category_name FROM movies LEFT JOIN categories ON movies.category_id = categories.category_id";
    $result = $db->query($sql);
    
    if (!$result) {
        die("Error fetching movies: " . $db->error); // Debugging error
    }
    
    return $result;
}

// Fetch all categories
function fetchCategories($db) {
    $sql = "SELECT * FROM categories";
    return $db->query($sql);
}

// Tambah Film
function addMovie($db, $title, $description, $rating, $release_year, $image) {
    $imagePath = 'uploads/' . $image; 

    $stmt = $db->prepare("INSERT INTO movies (title, description, image, rating, release_year) VALUES (?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        echo "Prepare statement failed: " . $db->error; 
        return false; 
    }

    $stmt->bind_param("ssssi", $title, $description, $imagePath, $rating, $release_year);
    
    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->error; 
        return false;
    }

    return true; 
}

// Update Film
function updateMovie($db, $id, $title, $description, $rating, $release_year, $image = null) {
    if ($image) {
        $sql = "UPDATE movies SET title=?, description=?, image=?, rating=?, release_year=? WHERE movie_id=?";
        $stmt = $db->prepare($sql);
        $imagePath = 'uploads/' . $image;
        $stmt->bind_param("sssisi", $title, $description, $imagePath, $rating, $release_year, $id);
    } else {
        $sql = "UPDATE movies SET title=?, description=?, rating=?, release_year=? WHERE movie_id=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssisi", $title, $description, $rating, $release_year, $id);
    }
    
    if (!$stmt) {
        echo "Prepare statement failed: " . $db->error; 
        return false; 
    }

    
    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->error; 
        return false; 
    }

    return true; 
}

// Hapus Film
function deleteMovie($db, $id) {
    $sql = "DELETE FROM movies WHERE movie_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
