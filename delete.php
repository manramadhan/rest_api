<?php
include 'db/database.php';
include 'functions.php';

$id = $_GET['id'];

$db = new mysqli($hostname, $username, $password, "kartun");
deleteMovie($db, $id);

header("Location: index.php");
?>
