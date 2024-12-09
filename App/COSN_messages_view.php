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




//get the logged in member's received messages
$sql = "SELECT 
            m.username, mm.message_content, mm.message_datetime, m.member_id
        FROM kpc353_2.member_messages as mm
            LEFT JOIN kpc353_2.members as m
                ON mm.origin_member_id = m.member_id
        WHERE 
            mm.target_member_id = :logged_in_member_id;";

$stmt = $pdo->prepare($sql);
$stmt->execute([':logged_in_member_id' => $logged_in_member_id]);
$result_received_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

//get the logged in member's sent messages
$sql = "SELECT 
            m.username, mm.message_content, mm.message_datetime, m.member_id
        FROM kpc353_2.member_messages as mm
            LEFT JOIN kpc353_2.members as m
                ON mm.target_member_id = m.member_id
        WHERE 
            mm.origin_member_id = :logged_in_member_id;";

$stmt = $pdo->prepare($sql);
$stmt->execute([':logged_in_member_id' => $logged_in_member_id]);
$result_sent_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

//get COSN users to which the logged in user can send messages
$sql = "SELECT 
            m.username, mr.member_relationship_type, mr.member_relationship_status, m.member_id
        FROM kpc353_2.member_relationships as mr
            LEFT JOIN kpc353_2.members as m
                ON mr.origin_member_id = m.member_id
        WHERE 
            mr.target_member_id = :logged_in_member_id
            AND mr.member_relationship_status in ('approved');";

$stmt = $pdo->prepare($sql);
$stmt->execute([':logged_in_member_id' => $logged_in_member_id]);
$members_friends = $stmt->fetchAll(PDO::FETCH_ASSOC);


// If the form is submitted, update the group's data
if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $message_target_member = $_POST['target_member'];
    $message_content = $_POST['message'];


    $sql = "INSERT INTO kpc353_2.member_messages
        (origin_member_id, target_member_id, message_content)
        VALUES
        (:logged_in_member_id, :target_member_id, :message_content)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['target_member_id' => $message_target_member, 'logged_in_member_id' => $logged_in_member_id, 'message_content' => $message_content]);
    

    echo "<script>alert('Message sent successfully!');";
    echo "window.location.href = 'COSN_messages_view.php';</script>";
    exit;

}
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
            if(empty($result_received_messages)){
                echo "<h1>You did not receive any messages yet.</h1>";
            }
            else{
                // confirmed relationships table
                echo "<h1>Your received messages:</h1>";
                echo "<table border='1'>";
                echo "<tr>";
                echo "<th>Username</th>";
                echo "<th>Message content</th>";
                echo "<th>Message datetime</th>";

                echo "</tr>";
                // Output data of each row
                while($row = current($result_received_messages)) {
                    //start row
                    echo "<tr>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['message_content'] ."</td>";
                    echo "<td>" . $row['message_datetime'] ."</td>";
                    echo "</tr>";
                    next($result_received_messages);
                }
                echo "</table>";
                echo "<br><br><hr>";
            }
            
            if(empty($result_sent_messages)){
                echo "<h1>You did not send any messages yet.</h1>";
            }
            else{
                // confirmed relationships table
                echo "<h1>Your sent out messages:</h1>";
                echo "<table border='1'>";
                echo "<tr>";
                echo "<th>Username</th>";
                echo "<th>Message content</th>";
                echo "<th>Message datetime</th>";

                echo "</tr>";
                // Output data of each row
                while($row = current($result_sent_messages)) {
                    //start row
                    echo "<tr>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['message_content'] ."</td>";
                    echo "<td>" . $row['message_datetime'] ."</td>";
                    echo "</tr>";
                    next($result_sent_messages);
                }
                echo "</table>";
                echo "<br><br><hr>";
            }
            
        ?>

    <form method="POST">
        <label for="target_member"> Target member:</label>
            <select id="target_member" name="target_member" required>
                <?php foreach($members_friends as $member_friend): ?>
                    <option value= <?php echo $member_friend['member_id']; ?> > <?php echo $member_friend['username']; ?> </option>
                <?php endforeach; ?>
            </select>
            <br>
        
        <label for="message">Your message:</label>
        <input type="text" id="message" name="message" required><br>

        <br>
        <br>
        <br>

        <button type="submit" style="background-color: green; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Send Message</button>

        </button>
    </form>
        
</div>
</html>