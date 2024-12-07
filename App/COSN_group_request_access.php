<?php
session_start();
include("db_config.php");


// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

// Check if the ID is set
if (!isset($_GET['group_id'])) {
    echo "<script>alert('No group_ID is specified!');</script>";
    echo "<script>window.location.href = 'COSN_groups.php';</script>";
    exit;
}


$logged_in_member_id = $_SESSION['member_id'];
$requested_group_id = $_GET['group_id'];


// Check if the user is already a member of the group and alert that they cannot join the group, indicating their current status
// Prepare the SQL statement
$sql = "
    SELECT 
        group_member_status
    FROM kpc353_2.group_members
    WHERE 
        joined_group_id = :requested_group_id
        AND participant_member_id = :logged_in_member_id
    ";

$stmt = $pdo->prepare($sql);
$stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':requested_group_id' => $requested_group_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

//alert the user that they are already a member of the group, and indicate their current status
if($result){
    echo "<script>alert('Cannot join group! Your status in this group is already:'".$result[0]['group_member_status'].");</script>";
    echo "<script>window.location.href = 'COSN_groups.php';</script>";
    exit;
}


$sql_request_group_access = "
INSERT INTO kpc353_2.group_members
	(participant_member_id, joined_group_id, date_joined, group_member_status) 
    VALUES 
    (:logged_in_member_id, :requested_group_id, NOW(), 'requested')
";

$requestGroupAccessStmt = $pdo->prepare($sql_request_group_access);
$requestGroupAccessStmt->execute(['logged_in_member_id' => $logged_in_member_id, 'requested_group_id' => $requested_group_id]);
echo "<script>alert('Your group access request was sent to the admin. Wait for approval!');</script>";
echo "<script>window.location.href = 'COSN_groups.php';</script>";

?>