<?php
session_start();
include 'db_config.php'; //include database connection

if (isset($_POST['receiver_id'])) {
    $sender_id = $_SESSION['user_id']; // Current logged-in user
    $receiver_id = $_POST['receiver_id'];

    // Check if there's already a pending or approved relationship
    $stmt = $conn->prepare("SELECT * FROM cosn.member_relationships 
                            WHERE (origin_member_id = ? AND target_member_id = ? 
                            OR origin_member_id = ? AND target_member_id = ?) 
                            AND member_relationship_type = 'friend'");
    $stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there's already a pending or approved request
    if ($result->num_rows > 0) {
        echo "Friend request already sent or already friends!";
    } else {
        // Insert a new friend request
        $stmt = $conn->prepare("INSERT INTO cosn.member_relationships 
                                (origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
                                VALUES (?, ?, 'friend', 'requested')");
        $stmt->bind_param("ii", $sender_id, $receiver_id);
        $stmt->execute();
        echo "Friend request sent!";
    }
}
?>
