<?php
session_start();
include("db_config.php");

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
$logged_in_member_id = $_SESSION['member_id'];


// Check if the target_member_id is set
if (!isset($_GET['target_member_id'])) {
    echo "<script>alert('No target_member_id is specified!');</script>";
    echo "<script>window.location.href = 'COSN_group_admin.php?group_id=". $requested_group_id ."';</script>";
    exit;
}
$target_member_id = $_GET['target_member_id'];

// Check if the message is set
if (!isset($_GET['message'])) {
    echo "<script>alert('No message is specified!');</script>";
    echo "<script>window.location.href = 'COSN_group_admin.php?group_id=". $requested_group_id ."';</script>";
    exit;
}
$message = htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8');

// Check if the logged in member has been blocked by the target member
$sql = "SELECT 
            1
        FROM 
            kpc353_2.member_relationships as mr
        WHERE 
            mr.target_member_id = :logged_in_member_id
            AND mr.origin_member_id = :target_member_id
            AND mr.member_relationship_status = 'blocked'
        ";
$stmt = $pdo->prepare($sql);
$stmt->execute([':target_member_id' => $target_member_id, ':logged_in_member_id' => $logged_in_member_id]);
$result_blocked = $stmt->fetchAll(PDO::FETCH_ASSOC);

if($result_blocked){
    echo "<script>alert('You cannot send messages to this member as they have blocked you!');</script>";
    echo "<script>window.location.href = 'COSN_members.php';</script>";
    exit;
}

$sql = "INSERT INTO kpc353_2.member_messages
	(origin_member_id, target_member_id, message_content)
    VALUES
    (:logged_in_member_id, :target_member_id, :message_content');";

$stmt = $pdo->prepare($sql);
$stmt->execute(['target_member_id' => $target_member_id, 'logged_in_member_id' => $logged_in_member_id, 'message_content' => $message]);

echo "<script>alert('Message sent successfully!');</script>";
echo "<script>window.location.href = 'COSN_messages_view.php?';</script>";
?>