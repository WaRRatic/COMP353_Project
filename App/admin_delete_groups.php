<?php
// Check if the user is logged in and has the "admin" role
if (!isset($_SESSION['loggedin']) || $_SESSION['privilege_level'] !== 'administrator') {
    // Redirect to an error page or homepage if not authorized
    header("Location: homepage.php"); 
    exit;
}
// Check if 'member_id' is set in the POST request
if (isset($_POST['group_id'])) {
    $group_id = $_POST['group_id'];

    // Prepare the DELETE query
    $sql = "DELETE FROM groups WHERE group_id = ?";
    $stmt = $conn->prepare($sql);

    // Bind the parameter and execute the statement
    $stmt->bind_param("i", $group_id);

    if ($stmt->execute()) {
        echo "Group deleted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to the members list
    header("Location: homepage.php");
    exit;
} else {
    echo "Invalid request.";
}
?>