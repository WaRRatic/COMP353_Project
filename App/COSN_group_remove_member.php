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


// Check if the Group ID is set
if (!isset($_GET['group_id'])) {
    echo "<script>alert('No Group_ID is specified!');</script>";
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

// Check if the user is a COSN admin
$isAdmin=false;
if ($_SESSION['privilege_level'] === 'administrator'){
    $isAdmin = true;
}

// Check if user who initiated the request is the group member who wants to leave voluntarily
// Prepare the SQL statement
$isLeavingVoluntary = false;
if($logged_in_member_id == $target_member_id){
    $isLeavingVoluntary = true;
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

//terminate the transaction if the request does not have the appropriate privilege
if(!$result && !$isAdmin && !$isLeavingVoluntary){
    echo "<script>alert('You don't have the privilege to remove this member from this group!.);</script>";
    echo "<script>window.location.href = 'COSN_groups.php';</script>";
    exit;
}

// Check if the user is the "admin" of the group
// Prepare the SQL statement
$sql = "
    SELECT 
        group_member_status
    FROM kpc353_2.group_members
    WHERE 
        joined_group_id = :requested_group_id
        AND participant_member_id = :target_member_id
        AND group_member_status = 'admin'
    ";

$stmt = $pdo->prepare($sql);
$stmt->execute([':target_member_id' => $target_member_id, ':requested_group_id' => $requested_group_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

//alert the user that they cannot ban the admin of the group
if($result){
    echo "<script>alert('Can't touch the admin of the group!.);</script>";
    echo "<script>window.location.href = 'COSN_group_admin.php?group_id=". $requested_group_id ."';</script>";
    exit;
}


$sql_request_group_access = "
DELETE FROM  kpc353_2.group_members
    WHERE 
        joined_group_id = :requested_group_id
        AND participant_member_id = :target_member_id
";

$requestGroupAccessStmt = $pdo->prepare($sql_request_group_access);
$requestGroupAccessStmt->execute(['target_member_id' => $target_member_id, 'requested_group_id' => $requested_group_id]);
echo "<script>alert('User was succesfully removed from the COSN group!');</script>";
echo "<script>window.location.href = 'COSN_groups.php';</script>";
?>