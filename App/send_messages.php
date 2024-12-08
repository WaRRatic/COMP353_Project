<?php
session_start();
include 'db_config.php'; //include database connection

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['origin_member_id'], $data['target_member_id'], $data['message_content'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

$origin_id = $data['origin_member_id'];
$target_id = $data['target_member_id'];
$content = $data['message_content'];

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

// Insert the message
$sql = "INSERT INTO member_messages (origin_member_id, target_member_id, message_content) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$origin_id, $target_id, $content]);

echo json_encode(['status' => 'success', 'message_id' => $pdo->lastInsertId()]);
?>
