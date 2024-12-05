<?php
session_start();

// Check if the user is logged in and has the "admin" role
if (!isset($_SESSION['loggedin']) || $_SESSION['privilege_level'] !== 'administrator') {
    header("Location: homepage.php");
    exit;
}

// Check if member_id is passed in the POST request
if (isset($_POST['member_id'])) {
    $member_id = $_POST['member_id'];

    // Validate member_id
    if (!is_numeric($member_id)) {
        die("Invalid member ID");
    }

//set db connections variables
$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "cosn";
// Create a database connection
$conn = new mysqli($dbServername, $dbUsername, $dbPassword, $dbName);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Step 1: Delete related records in `content_comment`
    $sql = "DELETE FROM content_comment WHERE commenter_member_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement for content_comment: " . $conn->error);
    }
    $stmt->bind_param("i", $member_id);
    if (!$stmt->execute()) {
        die("Error deleting related comments: " . $stmt->error);
    }
    $stmt->close();

    // Step 2: Delete the member from `Members`
    $sql = "DELETE FROM Members WHERE member_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement for Members: " . $conn->error);
    }
    $stmt->bind_param("i", $member_id);
    if ($stmt->execute()) {
        echo "Member deleted successfully.";
    } else {
        echo "Error deleting member: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to the members list
    header("Location: homepage.php");
    exit;
} else {
    echo "Invalid request.";
}