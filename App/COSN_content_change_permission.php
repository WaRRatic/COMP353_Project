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

// Check if the Content ID is set
if (!isset($_GET['content_id'])) {
    echo "<script>alert('No content_id is specified!');</script>";
    echo "<script>window.location.href = 'homepage.php';</script>";
    exit;
}
$content_id = $_GET['content_id'];

$sql = $pdo->prepare("
    SELECT
        content_id, m.username, content_type, content_data, content_creation_date, content_title, moderation_status, 
        cpp.content_public_permission_type AS content_permission_type, 'public' as content_feed_type, NULL as post_group_name
    FROM
        kpc353_2.content as cont
    INNER JOIN kpc353_2.content_public_permissions as cpp
        ON cont.content_id = cpp.target_content_id
    INNER JOIN kpc353_2.members as m
        ON cont.creator_id = m.member_id
        AND content_id = :content_id 
    UNION
    SELECT
        content_id, m.username, content_type, content_data, content_creation_date, content_title, moderation_status, 
        cmp.content_permission_type AS content_permission_type, 'private' as content_feed_type, NULL as post_group_name
    FROM
        kpc353_2.content as cont
    INNER JOIN kpc353_2.content_member_permission as cmp
        ON cont.content_id = cmp.target_content_id
    INNER JOIN kpc353_2.members as m
        ON cont.creator_id = m.member_id
    WHERE
        cmp.authorized_member_id = :logged_in_member_id
        AND content_id = :content_id 
    UNION
    SELECT
        content_id, m.username, content_type, content_data, content_creation_date, content_title, moderation_status, 
        cgp.content_group_permission_type AS content_permission_type, 'group' as content_feed_type, g.group_name as post_group_name
    FROM
        kpc353_2.content as cont
    INNER JOIN kpc353_2.content_group_permissions as cgp
        ON cont.content_id = cgp.target_content_id
    INNER JOIN kpc353_2.groups as g
        ON g.group_id = cgp.target_group_id
    INNER JOIN kpc353_2.group_members as gm
        on gm.joined_group_id = g.group_id
    INNER JOIN kpc353_2.members as m
        ON m.member_id = gm.participant_member_id
    WHERE 
        m.member_id = :logged_in_member_id
        AND content_id = :content_id 
    ORDER BY content_creation_date desc
    ");

$sql->execute(['logged_in_member_id' => $logged_in_member_id, 'content_id'=>$content_id]);
$contentPermissions_user = $sql->fetchAll(PDO::FETCH_ASSOC);


$content_edit_permission = false;
while($row = current($contentPermissions_user)) {
    // Check for edit permission or allow it if the user is an admin
    if ((!$content_edit_permission && $row['content_permission_type'] === 'edit') || $isAdmin) {
        $content_edit_permission = true;
    }
    next($contentPermissions_user);
}
    
// Redirect the user if they don't have permission to edit the permission of content or not an admin
if (!$content_edit_permission) {
    echo "<script>alert('You don't have permission to edit the permission of this content');</script>";
    echo "<script>window.location.href = 'homepage.php';</script>";
    exit;
}



// Check the action on the permission of the content
if (!isset($_GET['action'])) {
    echo "<script>alert('No permission action specified!');</script>";
    echo "<script>window.location.href = 'homepage.php';</script>";
    exit;
}
$content_action = $_GET['action'];

// Check if the content_feed_type is set
if (!isset($_GET['level'])) {
    echo "<script>alert('No level of permission is specified!');</script>";
    echo "<script>window.location.href = 'homepage.php';</script>";
    exit;
}
$content_permission_level = $_GET['level'];

// Check if the permission type is set
if (!isset($_GET['type'])) {
    echo "<script>alert('No permission type specified!');</script>";
    echo "<script>window.location.href = 'homepage.php';</script>";
    exit;
}
$content_permission_type = $_GET['type'];

if($content_permission_level==='private'){
    // Check if the Member ID is set
    if (!isset($_GET['member_id'])) {
        echo "<script>alert('No Member ID is specified!');</script>";
        echo "<script>window.location.href = 'homepage.php';</script>";
        exit;
    }
    $content_permission_member_id = $_GET['member_id'];

}elseif($content_permission_level==='group'){
    // Check if the Member ID is set
    if (!isset($_GET['group_id'])) {
        echo "<script>alert('No group_id is specified!');</script>";
        echo "<script>window.location.href = 'homepage.php';</script>";
        exit;
    }
    $content_permission_group_id = $_GET['group_id'];
}else{
    $content_permission_member_id = null;
    $content_permission_group_id = null;
}

if($content_action === 'add'){
    if($content_permission_level==='private'){
        //First remove the existing private permission, as not to accumulate multiple private permissions
        $sql_request_content_access = "
        DELETE FROM kpc353_2.content_member_permission
        WHERE 
            target_content_id = :content_id
            AND authorized_member_id = :content_permission_member_id
            AND content_permission_type = :content_permission_type
        ";
        $requestContentAccessStmt = $pdo->prepare($sql_request_content_access);
        $requestContentAccessStmt->execute(['content_id' => $content_id, 'content_permission_member_id' => $content_permission_member_id, 'content_permission_type' => $content_permission_type]);
        
        //Then add the new private permission
        $sql_request_content_access = "
        INSERT INTO kpc353_2.content_member_permission (target_content_id, authorized_member_id, content_permission_type)
        VALUES (:content_id, :content_permission_member_id, :content_permission_type)
        ";
        $requestContentAccessStmt = $pdo->prepare($sql_request_content_access);
        $requestContentAccessStmt->execute(['content_id' => $content_id, 'content_permission_member_id' => $content_permission_member_id, 'content_permission_type' => $content_permission_type]);
    }elseif($content_permission_level==='group'){
        //First remove the existing group permission, as not to accumulate multiple group permissions
        $sql_request_content_access = "
        DELETE FROM kpc353_2.content_group_permissions
        WHERE 
            target_content_id = :content_id
            AND target_group_id = :content_permission_group_id
            AND content_group_permission_type = :content_permission_type
        ";
        $requestContentAccessStmt = $pdo->prepare($sql_request_content_access);
        $requestContentAccessStmt->execute(['content_id' => $content_id, 'content_permission_group_id' => $content_permission_group_id, 'content_permission_type' => $content_permission_type]);
        
        //Then add the new group permission
        $sql_request_content_access = "
        INSERT INTO kpc353_2.content_group_permissions (target_content_id, target_group_id, content_group_permission_type)
        VALUES (:content_id, :content_permission_group_id, :content_permission_type)
        ";
        $requestContentAccessStmt = $pdo->prepare($sql_request_content_access);
        $requestContentAccessStmt->execute(['content_id' => $content_id, 'content_permission_group_id' => $content_permission_group_id, 'content_permission_type' => $content_permission_type]);
    }elseif($content_permission_level==='public'){
        
        //First remove the existing public permission, as not to accumulate multiple public permissions
        $sql_request_content_access = "
        DELETE FROM kpc353_2.content_public_permissions
        WHERE 
            target_content_id = :content_id
            AND content_public_permission_type = :content_permission_type
        ";
        $requestContentAccessStmt = $pdo->prepare($sql_request_content_access);
        $requestContentAccessStmt->execute(['content_id' => $content_id, 'content_permission_type' => $content_permission_type]);
        
        //Then add the new public permission
        $sql_request_content_access = "
        INSERT INTO kpc353_2.content_public_permissions (target_content_id, content_public_permission_type)
        VALUES (:content_id, :content_permission_type)
        ";
        $requestContentAccessStmt = $pdo->prepare($sql_request_content_access);
        $requestContentAccessStmt->execute(['content_id' => $content_id, 'content_permission_type' => $content_permission_type]);
    }
}elseif($content_action === 'remove'){
    if($content_permission_level==='private'){
        $sql_request_content_access = "
        DELETE FROM kpc353_2.content_member_permission
        WHERE 
            target_content_id = :content_id
            AND authorized_member_id = :content_permission_member_id
            AND content_permission_type = :content_permission_type
        ";
        $requestContentAccessStmt = $pdo->prepare($sql_request_content_access);
        $requestContentAccessStmt->execute(['content_id' => $content_id, 'content_permission_member_id' => $content_permission_member_id, 'content_permission_type' => $content_permission_type]);
    }elseif($content_permission_level==='group'){
        $sql_request_content_access = "
        DELETE FROM kpc353_2.content_group_permissions
        WHERE 
            target_content_id = :content_id
            AND target_group_id = :content_permission_group_id
            AND content_group_permission_type = :content_permission_type
        ";
        $requestContentAccessStmt = $pdo->prepare($sql_request_content_access);
        $requestContentAccessStmt->execute(['content_id' => $content_id, 'content_permission_group_id' => $content_permission_group_id, 'content_permission_type' => $content_permission_type]);
    }elseif($content_permission_level==='public'){
        $sql_request_content_access = "
        DELETE FROM kpc353_2.content_public_permissions
        WHERE 
            target_content_id = :content_id
            AND content_public_permission_type = :content_permission_type
        ";
        $requestContentAccessStmt = $pdo->prepare($sql_request_content_access);
        $requestContentAccessStmt->execute(['content_id' => $content_id, 'content_permission_type' => $content_permission_type]);
    }
}


echo "<script>alert('Content permission modified succesfully!');</script>";
echo "<script>window.location.href = 'COSN_content_edit.php?content_id=". $content_id ."';</script>";

?>