<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to perform this action.";
    exit;
}

$logged_in_user_id = $_SESSION['user_id'];

if (isset($_POST['blocked_id'])) {
    $blocked_id = $_POST['blocked_id'];

    // Delete the blocking relationship from the database
    $stmt = $conn->prepare("
        DELETE FROM cosn.member_realtionships
        WHERE origin_member_id = ? AND target_member_id = ? 
        AND member_relationship_type = 'blocked' AND member_relationship_status = 'approved'
    ");
    $stmt->bind_param("ii", $logged_in_user_id, $blocked_id);
    $stmt->execute();

    echo "User has been unblocked.";
}

$stmt->close();
$conn->close();
?>