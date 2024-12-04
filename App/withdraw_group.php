<?php
session_start();
include("db.php");

// Ensure the user is logged in
if (!isset($_SESSION['member_id'])) 
{
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to withdraw from a group.']);
    exit;
}

$member_id = $_SESSION['member_id'];

// Check if the group ID is provided in the POST request
if (isset($_POST['group_id'])) 
{
    $group_id = $_POST['group_id'];

    // Prepare the SQL query to check if the user is a member of the group
    $stmt = $conn->prepare("SELECT * FROM cosn.group_members WHERE participant_member_id = ? AND joined_group_id = ?");
    $stmt->bind_param("ii", $member_id, $group_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the user is a member of the group, proceed with withdrawal
    if ($result->num_rows > 0) 
    {
        // SQL to remove the user from the group
        $stmt = $conn->prepare("DELETE FROM cosn.group_members WHERE participant_member_id = ? AND joined_group_id = ?");
        $stmt->bind_param("ii", $member_id, $group_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'You have successfully withdrawn from the group.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to withdraw from the group.']);
        }
    } 
    else 
    {
        echo json_encode(['status' => 'error', 'message' => 'You are not a member of this group.']);
    }

    $stmt->close();
} 
else 
{
    echo json_encode(['status' => 'error', 'message' => 'Group ID is missing.']);
}

$conn->close();
?>