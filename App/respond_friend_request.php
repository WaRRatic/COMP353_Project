<?php
session_start();
include 'db.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    echo "You need to log in first!";
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['request_id'], $_POST['status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status']; // 'approved' or 'rejected'

    // Fetch the request to get the origin member ID (sender) and target member ID (receiver)
    $stmt = $conn->prepare("SELECT origin_member_id, target_member_id FROM kpc353_2.member_relationships WHERE relationship_id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $sender_id = $row['origin_member_id'];
        $receiver_id = $row['target_member_id'];

        // Check if the request is for the logged-in user (receiver)
        if ($receiver_id !== $user_id) {
            echo "You are not authorized to respond to this request!";
            exit;
        }

        // Update the status of the friend request
        $stmt = $conn->prepare("UPDATE kpc353_2.member_relationships SET member_relationship_status = ? WHERE relationship_id = ?");
        $stmt->bind_param("si", $status, $request_id);
        $stmt->execute();

        // If the request is accepted, create the reverse relationship (mutual friendship)
        if ($status == 'approved') {
            $stmt = $conn->prepare("INSERT INTO kpc353_2.member_relationships (origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
                                    VALUES (?, ?, 'friend', 'approved')");
            $stmt->bind_param("ii", $sender_id, $receiver_id);
            $stmt->execute();
        }

        echo "Request $status!";
    } else {
        echo "Invalid friend request!";
    }
} else {
    echo "Missing request ID or status!";
}

$conn->close();
?>
