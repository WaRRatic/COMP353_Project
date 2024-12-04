<?php
//File to connect to the database using PDO

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "root";
$dbName = "cosn";

try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$dbServername;dbname=$dbName", $dbUsername, $dbPassword);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
