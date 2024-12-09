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

$isAdmin=false;
// Check if the user is a COSN admin
if ($_SESSION['privilege_level'] === 'administrator'){
    $isAdmin = true;
}

// Set member_id variable for both fetching and updating
$member_id = $_GET['member_id'];

// Check if the user is the account owner
$isAccountOwner = ($member_id == $_SESSION['member_id']) ? true : false;
// if the user is not an admin and is not the account owner, then notify them that can only manage their own account and redirect to their own modify account page
if(!$isAdmin){
    if(!$isAccountOwner){
        echo "<script>alert('You can only manage permissions on your own account!');</script>";
        echo "<script>window.location.href = 'COSN_member_manage.php?member_id=" . $_SESSION['member_id'] . "';</script>";
        exit;
    }
}


// Check the action on the permission of the content
if (!isset($_GET['action'])) {
    echo "<script>alert('No permission action specified!');</script>";
    echo "<script>window.location.href = 'homepage.php';</script>";
    exit;
}
$permisson_action = $_GET['action'];

// Check if the content_feed_type is set
if (!isset($_GET['level'])) {
    echo "<script>alert('No level of permission is specified!');</script>";
    echo "<script>window.location.href = 'homepage.php';</script>";
    exit;
}
$permisson_level = $_GET['level'];

// Check if the permission type is set
if (!isset($_GET['type'])) {
    echo "<script>alert('No permission type specified!');</script>";
    echo "<script>window.location.href = 'homepage.php';</script>";
    exit;
}
$permisson_type = $_GET['type'];

if($permisson_level==='private'){
    // Check if the Member ID is set
    if (!isset($_GET['target_member_id'])) {
        echo "<script>alert('No Authorized Member ID is specified!');</script>";
        echo "<script>window.location.href = 'homepage.php';</script>";
        exit;
    }
    $permisson_auth_member_id = $_GET['target_member_id'];

}else{
    $permisson_auth_member_id = null;
}

if($permisson_action === 'add'){
    if($permisson_level==='private'){
        //first remove the existing permission, as not to accumulate multiple permissions
        $sql_permission_modify = "
        DELETE FROM kpc353_2.personal_info_permissions
        WHERE owner_member_id = :member_id 
            AND authorized_member_id = :permission_member_id 
            AND personal_info_type = :permission_type;
        ";
        $modifyPermissionStmt = $pdo->prepare($sql_permission_modify);
        $modifyPermissionStmt->execute(['member_id' => $member_id, 'permission_member_id' => $permisson_auth_member_id, 'permission_type' => $permisson_type]);

        //Then add the permission
        $sql_permission_modify = "
        INSERT INTO kpc353_2.personal_info_permissions
        (owner_member_id, personal_info_type,authorized_member_id)
        VALUES(:member_id, :permission_type, :permission_member_id);
        ";
        $modifyPermissionStmt = $pdo->prepare($sql_permission_modify);
        $modifyPermissionStmt->execute(['member_id' => $member_id, 'permission_member_id' => $permisson_auth_member_id, 'permission_type' => $permisson_type]);

    }elseif($permisson_level==='public'){
        //first remove the existing public permission, as not to accumulate multiple public permissions
        $sql_permission_modify = "
        DELETE FROM kpc353_2.personal_info_public_permissions
        WHERE owner_member_id = :member_id 
            AND personal_info_type = :permission_type;
        ";

        $modifyPermissionStmt = $pdo->prepare($sql_permission_modify);
        $modifyPermissionStmt->execute(['member_id' => $member_id,'permission_type' => $permisson_type]);

        //Then add public permission
        $sql_permission_modify = "
        INSERT INTO kpc353_2.personal_info_public_permissions
        (owner_member_id, personal_info_type)
        VALUES(:member_id, :permission_type);
        ";
        
        $modifyPermissionStmt = $pdo->prepare($sql_permission_modify);
        $modifyPermissionStmt->execute(['member_id' => $member_id,'permission_type' => $permisson_type]);
    }
}elseif($permisson_action === 'remove'){
    if($permisson_level==='private'){
        $sql_permission_modify = "
        DELETE FROM kpc353_2.personal_info_permissions
        WHERE owner_member_id = :member_id 
            AND authorized_member_id = :permission_member_id 
            AND personal_info_type = :permission_type;
        ";
        $modifyPermissionStmt = $pdo->prepare($sql_permission_modify);
        $modifyPermissionStmt->execute(['member_id' => $member_id, 'permission_member_id' => $permisson_auth_member_id, 'permission_type' => $permisson_type]);
    }elseif($permisson_level==='public'){
        $sql_permission_modify = "
        DELETE FROM kpc353_2.personal_info_public_permissions
        WHERE owner_member_id = :member_id 
            AND personal_info_type = :permission_type;
        ";

        $modifyPermissionStmt = $pdo->prepare($sql_permission_modify);
        $modifyPermissionStmt->execute(['member_id' => $member_id,'permission_type' => $permisson_type]);
    }
}


echo "<script>alert('Member personal information permission modified succesfully!');</script>";
echo "<script>window.location.href = 'COSN_member_manage.php?member_id=". $member_id ."';</script>";

?>