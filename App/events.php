<?php
include("db_config.php");
include("header.php");
include('sidebar.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/admin_manage_users.css" />
<head>
    <meta charset="UTF-8">
    <title>Events</title>
</head>
<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}


// Create a database connection
$conn = new mysqli($host, $user, $pass, $db);

$memberId = $_SESSION['member_id'];

// Query to get all members
$sql = "SELECT 
    group_event.group_event_id,
    group_event.target_group_id,
    group_event.event_organizer_member_id,
    group_event.event_name,
    members.username AS organizer_name,
    groups.group_name
FROM 
    group_event
LEFT JOIN 
    members 
ON 
    group_event.event_organizer_member_id = members.member_id
LEFT JOIN 
    groups 
ON 
    group_event.target_group_id = groups.group_id
LEFT JOIN 
    group_members
ON 
    group_event.target_group_id = group_members.group_membership_id
WHERE 
    group_members.group_membership_id = $memberId
;";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events Page</title>
</head>
<body>
<div class="main-content">
<div class="view-content-container">
    <h1>Events</h1>
    <div id="center_button">
    <button> <a href='Organize_event.php' >Organize an event</a></button>
    </div>
    <table border="1">
        <tr>
            <th></th>
            <th>Event name</th>
            <th>Group</th>
            <th>Event organizer</th>

        </tr>
        
        <?php
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><a href='event_vote.php?group_event_id=" . $row['group_event_id'] . "'><button>Vote on date</button></a></td>";
                echo "<td>" . $row['event_name'] . "</td>";
                echo "<td>" . $row['group_name'] . "</td>";
                echo "<td>" . $row['organizer_name'] . "</td>";
                echo "<td><a href='event_suggest.php?group_event_id=" . $row['group_event_id'] . "'><button>Suggest date</button></a></td>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No events found</td></tr>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </table>
        <br>
        <br>
        <br>
        <ul>
            <li><a href="homepage.php">Back to Homepage</a></li>
        </ul>

</div>
</div>
</body>
</body>
</html>