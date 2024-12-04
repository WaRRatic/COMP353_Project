<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

if (!isset($_POST['target_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$origin_member_id = $_SESSION['member_id'];
$target_member_id = $_POST['target_id'];

$stmt = $conn->prepare("DELETE FROM member_relationships 
                        WHERE (origin_member_id = ? AND target_member_id = ?) 
                           OR (origin_member_id = ? AND target_member_id = ?)
                           AND member_relationship_type = 'friend'");
$stmt->bind_param("iiii", $origin_member_id, $target_member_id, $target_member_id, $origin_member_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Friend removed successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to unfriend user']);
}
?>