<?php
session_start();
include 'db_config.php'; //include database connection

if (!isset($_SESSION['member_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$member_id = $_SESSION['member_id'];

$sql = "
    SELECT 
        u.member_id AS friend_id,
        u.name AS friend_name
    FROM 
        member_relationships r
    JOIN 
        users u
    ON 
        (u.member_id = r.target_member_id AND r.origin_member_id = ?)
        OR 
        (u.member_id = r.origin_member_id AND r.target_member_id = ?)
    WHERE 
        r.member_relationship_type = 'friend'
        AND r.member_relationship_status = 'approved'
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$member_id, $member_id]);

$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($friends) {
    echo json_encode(['status' => 'success', 'friends' => $friends]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No friends found']);
}
?>
