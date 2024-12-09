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




//get the logged in member's confirmed relationships 
$sql = "SELECT 
            m.username, mr.member_relationship_type, mr.member_relationship_status, m.member_id
        FROM kpc353_2.member_relationships as mr
            LEFT JOIN kpc353_2.members as m
                ON mr.target_member_id = m.member_id
        WHERE 
            mr.origin_member_id = :logged_in_member_id
            AND mr.member_relationship_status in ('approved');";

$stmt = $pdo->prepare($sql);
$stmt->execute([':logged_in_member_id' => $logged_in_member_id]);
$result_friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

//get the logged in member's still unaccepted relationships requests
$sql = "SELECT 
            m.username, mr.member_relationship_type, mr.member_relationship_status, m.member_id
        FROM kpc353_2.member_relationships as mr
            LEFT JOIN kpc353_2.members as m
                ON mr.target_member_id = m.member_id
        WHERE 
            mr.origin_member_id = :logged_in_member_id
            AND mr.member_relationship_status in ('requested');";

$stmt = $pdo->prepare($sql);
$stmt->execute([':logged_in_member_id' => $logged_in_member_id]);
$result_pending_friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

//get friend requests that target the logged in member
$sql = "SELECT 
            m.username, mr.member_relationship_type, mr.member_relationship_status, m.member_id
        FROM kpc353_2.member_relationships as mr
            LEFT JOIN kpc353_2.members as m
                ON mr.origin_member_id = m.member_id
        WHERE 
            mr.target_member_id = :logged_in_member_id
            AND mr.member_relationship_status in ('requested');";

$stmt = $pdo->prepare($sql);
$stmt->execute([':logged_in_member_id' => $logged_in_member_id]);
$result_friend_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="COSN_member_manage.css" />
<head>
    <meta charset="UTF-8">
    <title>View your relationships</title>
</head>
<body>
<div class="main-content">
        <?php
            if(empty($result_friends)){
                echo "<h1>You don't have any confirmed relationships yet.</h1>";
            }
            else{
                // confirmed relationships table
                echo "<h1>Your confirmed relationships:</h1>";
                echo "<table border='1'>";
                echo "<tr>";
                echo "<th>Username</th>";
                echo "<th>Relationship type</th>";
                echo "<th>Manage relationship</th>";
                echo "</tr>";
                // Output data of each row
                while($row = current($result_friends)) {
                    //start row
                    echo "<tr>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['member_relationship_type'] ."</td>";
                    echo "<td><a href='COSN_member_relationship_manage.php?target_member_id=" . $row['member_id'] . "'><button style='background-color: grey; color: white;' >Manage relationship</button></a></td>";
                    echo "</tr>";
                    next($result_friends);
                }
                echo "</table>";
                echo "<br><br><hr>";
            }
            
            if(empty($result_pending_friends)){
                echo "<h1>You don't have any pending relationship requests that you've sent out.</h1>";}
            else{
                // Pending relationships table
                echo "<h1>Your pending relationships requests:</h1>";
                echo "<small>Wait until the requested member confirmes this relationship...</small>";
                echo "<br><br>";
                echo "<table border='1'>";
                echo "<tr>";
                echo "<th>Username</th>";
                echo "<th>Relationship type</th>";
                echo "<th>Manage relationship</th>";
                echo "</tr>";
                // Output data of each row
                while($row = current($result_pending_friends)) {
                    //start row
                    echo "<tr>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['member_relationship_type'] ."</td>";
                    echo "<td><a href='COSN_member_relationship_manage.php?target_member_id=" . $row['member_id'] . "'><button style='background-color: grey; color: white;' >Manage relationship</button></a></td>";
                    echo "</tr>";
                    next($result_pending_friends);
                }
                echo "</table>";
                echo "<br><br><hr>";
            }


            if(empty($result_friend_requests)){
                echo "<h1>You have not received any relationship requests yet.</h1>";
            }else{
                echo "<h1>Your received relationship requests:</h1>";
                echo "<table border='1'>";
                echo "<tr>";
                echo "<th>Username</th>";
                echo "<th>Relationship type</th>";
                echo "<th>Accept request</th>";
                echo "<th>Reject request</th>";
                echo "<th>Manage relationship</th>";
                echo "</tr>";
                
                // Friend requests table
                while($row = current($result_friend_requests)) {
                    //start row
                    echo "<tr>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['member_relationship_type'] ."</td>";
                    echo "<td><a href='COSN_member_relationship_change.php?target_member_id=" . $row['member_id'] . "&rel_type=". $row['member_relationship_type'] . "&rel_status=approved'><button style='background-color: green; color: black;' >Accept request</button></a></td>";
                    echo "<td><a href='COSN_member_relationship_change.php?target_member_id=" . $row['member_id'] . "&rel_type=". $row['member_relationship_type'] . "&rel_status=rejected'><button style='background-color: orange; color: black;' >Reject request</button></a></td>";
                    echo "<td><a href='COSN_member_relationship_manage.php?target_member_id=" . $row['member_id'] . "'><button style='background-color: grey; color: white;' >Manage relationship</button></a></td>";

                    echo "</tr>";
                    next($result_friend_requests);
                }
                echo "</table>";
                echo "<br><br><hr>";
            }


        ?>

</div>
</html>