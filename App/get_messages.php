<?php
session_start();
include 'db_config.php'; //include database connection

if (!isset($_GET['origin_member_id']) || !isset($_GET['target_member_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

$origin_id = $_GET['origin_member_id'];
$target_id = $_GET['target_member_id'];

// Check if the target user is a friend
$sql = "SELECT * FROM member_relationships 
        WHERE (
            (origin_member_id = ? AND target_member_id = ?) 
            OR (origin_member_id = ? AND target_member_id = ?)
        )
        AND member_relationship_type = 'friend'
        AND member_relationship_status = 'approved'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$origin_id, $target_id, $target_id, $origin_id]);

if ($stmt->rowCount() === 0) {
    echo json_encode(['status' => 'error', 'message' => 'User is not your friend']);
    exit;
}

// Fetch messages
$sql = "SELECT * FROM member_messages 
        WHERE (origin_member_id = ? AND target_member_id = ?)
        OR (origin_member_id = ? AND target_member_id = ?) 
        ORDER BY member_message_id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$origin_id, $target_id, $target_id, $origin_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => 'success', 'messages' => $messages]);
?>
