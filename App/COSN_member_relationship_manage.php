<?php
session_start();
include("db_config.php");
include("header.php");
include('sidebar.php'); 

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
$logged_in_member_id = $_SESSION['member_id'];

// Check if the ID is set
if (!isset($_GET['target_member_id'])) {
    echo "<script>alert('No target Member_ID is specified!');</script>";
    echo "<script>window.location.href = 'COSN_members.php';</script>";
    exit;
}
$target_member_id = $_GET['target_member_id'];

$isAdmin = false;
// Check if the user is an admin
if ($_SESSION['privilege_level'] === 'administrator'){
    $isAdmin = true;
}

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
    echo "<script>alert('You have been blocked by this member!');</script>";
    echo "<script>window.location.href = 'COSN_members.php';</script>";
    exit;
}


//check if the target member is blocked by the logged in member
$sql = "SELECT 
            1
        FROM 
            kpc353_2.member_relationships as mr
        WHERE 
            mr.target_member_id = :target_member_id
            AND mr.origin_member_id = :logged_in_member_id
            AND mr.member_relationship_status = 'blocked'
        ";
$stmt = $pdo->prepare($sql);
$stmt->execute([':target_member_id' => $target_member_id, ':logged_in_member_id' => $logged_in_member_id]);
$result_target_blocked = $stmt->fetchAll(PDO::FETCH_ASSOC);

$targetMemberBlocked = false;
//if the $result_target_blocked is not empty, then the target member is blocked
if($result_target_blocked){
    $targetMemberBlocked = true;
}


// Get the target member's Username
// Target = the member to I want to be friends with (or block)
// Origin = me, the member who is logged in
$sql = "SELECT 
            m.username
        FROM 
            kpc353_2.members as m
        WHERE 
            m.member_id = :target_member_id 
        ";
$stmt = $pdo->prepare($sql);
$stmt->execute([':target_member_id' => $target_member_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$target_username = $result[0]['username'];

// Get the relationship status between the logged in member and the target member
// A logged in member can have 3 types of relationships with another member: friend, family, colleague
// if the relationshiop doesn't exist, then the status is void (forced with UNION ALL and NOT EXISTS)  
$sql ="SELECT 
mr.member_relationship_type, 
mr.member_relationship_status
FROM 
    kpc353_2.member_relationships AS mr
WHERE 
    mr.target_member_id = :target_member_id
    AND mr.origin_member_id = :logged_in_member_id
UNION ALL
SELECT 'friend', 'void'
WHERE NOT EXISTS (
    SELECT 1 
    FROM 
        kpc353_2.member_relationships 
    WHERE 
        member_relationship_type = 'friend'
        AND target_member_id = :target_member_id
        AND origin_member_id = :logged_in_member_id
)
UNION ALL
SELECT 'family', 'void'
WHERE NOT EXISTS (
    SELECT 1 
    FROM 
        kpc353_2.member_relationships 
    WHERE 
        member_relationship_type = 'family'
        AND target_member_id = :target_member_id
        AND origin_member_id = :logged_in_member_id
)
UNION ALL
SELECT 'colleague', 'void'
WHERE NOT EXISTS (
    SELECT 1 
    FROM 
        kpc353_2.member_relationships 
    WHERE 
        member_relationship_type = 'colleague'
        AND target_member_id = :target_member_id
        AND origin_member_id = :logged_in_member_id
)";
$stmt = $pdo->prepare($sql);
$stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id]);
$result_rel = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="COSN_member_manage.css" />
<head>
    <meta charset="UTF-8">
    <title>Manage relationship to member</title>
</head>
<body>
<div class="main-content">
    <p>Manage your relationship to member:&nbsp;&nbsp;&nbsp;   <u><b> <?php echo $target_username; ?></b></u></p>
        <?php

        //if the target member is not blocked, display the relationship options
        if(!$targetMemberBlocked){
            echo "<table border='1'>";
            echo "<tr>";
            echo "<th>Relationship type</th>";
            echo "<th>Relationship status</th>";
            echo "<th>Manage relationship</th>";
            echo "</tr>";
            // Output data of each row
            while($row = current($result_rel)) {
                //start row
                echo "<tr>";
                echo "<td>" . $row['member_relationship_type'] . "</td>";
                echo "<td>" . $row['member_relationship_status'] ."</td>";

                if($row['member_relationship_status'] === 'void'){
                    echo "<td><a href='COSN_member_relationship_change.php?target_member_id=" . $target_member_id . "&rel_type=". $row['member_relationship_type'] . "&rel_status=requested'><button style='background-color: green; color: black;'>Request to become ". $row['member_relationship_type'] ."</button></a></td>";
                }elseif($row['member_relationship_status'] === 'approved'){
                    echo "<td><a href='COSN_member_relationship_change.php?target_member_id=" . $target_member_id . "&rel_type=". $row['member_relationship_type'] . "&rel_status=remove'><button style='background-color: orange; color: black;' onclick='return confirm(\"Are you sure you want to remove this relationship?\");'>Remove relationship</button></a></td>";
                }elseif($row['member_relationship_status'] === 'rejected'){
                    echo "<td style='background-color: black; color: yellow;'>Your initial request has been rejected, you cannot send another one.</td>";
                }
                elseif($row['member_relationship_status'] === 'requested'){
                    echo "<td style='background-color: black; color: green;'>Waiting for approval.</td>";
                }

                echo "</tr>";
                next($result_rel);
                
            }
            echo "</table>";
            echo "<br><br><hr>";
        }
        if(!$targetMemberBlocked){
            echo "<p>This member is not blocked. They can see your homepage, send you messages and relationship requests.</p>";
            echo "<br>";
            echo "<a href='COSN_member_relationship_change.php?target_member_id=" . $target_member_id . "&rel_type=blocked&rel_status=blocked'><button style='background-color: pink; color: black;' onclick='return confirm(\"Are you sure you want to block this member? All of your relationship settings with this user will be deleted.\");' >Block user?</button></a>";
        }else{
            echo "<p>This member is blocked. They cannot see your homepage, send you messages or relationship requests.</p>";
            echo "<br>";
            echo "<a href='COSN_member_relationship_change.php?target_member_id=" . $target_member_id . "&rel_type=unblock&rel_status=unblock'><button style='background-color: orange; color: black;'onclick='return confirm(\"Are you sure you want to unblock this member?\");' >Unblock user?</button></a>";
        }
        ?>

</div>
</html>