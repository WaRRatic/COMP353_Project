<?php
//File to connect to the database using PDO

$host = "localhost";
$user = "root";
$pass = "";
$db = "cosn";

try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
