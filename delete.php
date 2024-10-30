<?php
include '../film-kartun-favorit/db/database.php';
include '../film-kartun-favorit/functions.php';

$id = $_GET['id'];

$db = new mysqli($hostname, $username, $password, "kartun");
deleteMovie($db, $id);

header("Location: index.php");
?>
