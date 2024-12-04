<?php
session_start();
include("db.php");

if (!isset($_SESSION['member_id'])) {
    echo "You must be logged in to join a group.";
    exit;
}

$member_id = $_SESSION['member_id'];

if (isset($_POST['group_id'])) {
    $group_id = $_POST['group_id'];

    // Insert the user into the group
    $stmt = $conn->prepare("INSERT INTO cosn.group_members (participant_member_id, joined_group_id, role_of_member) 
                            VALUES (?, ?, 'member')");
    $stmt->bind_param("ii", $member_id, $group_id);
    $stmt->execute();

    echo "You have successfully joined the group!";
}

$conn->close();
?>