
<?php
session_start();
include 'db.php'; //include database connection

if (isset($_POST['request_id'], $_POST['status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status']; // accepted or rejected

    // Update the status of the request
    $stmt = $conn->prepare("UPDATE cosn.member_relationships 
                            SET member_relationship_status = ? 
                            WHERE relationship_id = ?");
    $stmt->bind_param("si", $status, $request_id);
    $stmt->execute();

    // If accepted, change the relationship status to 'approved'
    if ($status == 'approved') {
        // Insert the reverse relationship (to represent friendship)
        $stmt = $conn->prepare("INSERT INTO cosn.member_relationships 
                                (origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
                                VALUES (?, ?, 'friend', 'approved')");
        $stmt->bind_param("ii", $receiver_id, $sender_id);
        $stmt->execute();
    }

    echo "Request " . $status;
}
?>