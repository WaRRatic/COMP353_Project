<?php
session_start();
include("db_config.php");

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

// Check if the Group ID is set
if (!isset($_GET['group_id'])) {
    echo "<script>alert('No Group ID is specified!');</script>";
    echo "<script>window.location.href = 'COSN_groups.php';</script>";
    exit;
}

$requested_group_id = $_GET['group_id'];

// Check if the Member ID is set
if (!isset($_GET['member_id'])) {
    echo "<script>alert('No Member ID is specified!');</script>";
    echo "<script>window.location.href = 'COSN_group_admin.php?group_id=". $requested_group_id ."';</script>";
    exit;
}

$target_member_id = $_GET['member_id'];
$logged_in_member_id = $_SESSION['member_id'];


// Check if the user still has "requested" status in the group
// Prepare the SQL statement
$sql = "
    SELECT 
        group_member_status
    FROM kpc353_2.group_members
    WHERE 
        joined_group_id = :requested_group_id
        AND participant_member_id = :target_member_id
        AND group_member_status = 'requested'
    ";

$stmt = $pdo->prepare($sql);
$stmt->execute([':target_member_id' => $target_member_id, ':requested_group_id' => $requested_group_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

//alert the user if their request is no longer valid
if(!$result){
    echo "<script>alert('Cannot accept user into group, user join request no longer valid.);</script>";
    echo "<script>window.location.href = 'COSN_groups.php';</script>";
    exit;
}

// Check user who initiated the request is the group admin/owner
// Prepare the SQL statement
$sql = "
    SELECT 
        group_member_status
    FROM kpc353_2.group_members
    WHERE 
        joined_group_id = :requested_group_id
        AND participant_member_id = :logged_in_member_id
        AND group_member_status = 'admin'
    ";

$stmt = $pdo->prepare($sql);
$stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':requested_group_id' => $requested_group_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

//alert the user if their request is no longer valid
if(!$result){
    echo "<script>alert('You must be the admin of the group to accept other members into this group!.);</script>";
    echo "<script>window.location.href = 'COSN_groups.php';</script>";
    exit;
}


$sql_request_group_access = "
UPDATE kpc353_2.group_members
	SET group_member_status = 'member'
    WHERE 
        joined_group_id = :requested_group_id
        AND participant_member_id = :target_member_id
";

$requestGroupAccessStmt = $pdo->prepare($sql_request_group_access);
$requestGroupAccessStmt->execute(['target_member_id' => $target_member_id, 'requested_group_id' => $requested_group_id]);
echo "<script>alert('User was succesfully accepted into the COSN group!');</script>";
echo "<script>window.location.href = 'COSN_group_admin.php?group_id=". $requested_group_id ."';</script>";

?>