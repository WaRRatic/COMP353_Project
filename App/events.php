<?php
session_start();
include("db_config.php");
include("header.php");
include('sidebar.php'); 


if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}


// Create a database connection
$conn = new mysqli($host, $user, $pass, $db);

$memberId = $_SESSION['member_id'];

// Query to get all events
$sql = "SELECT 
    ge.group_event_id,
    ge.target_group_id,
    ge.event_organizer_member_id,
    ge.event_name,
    g.group_name
FROM 
    kpc353_2.group_members AS gm
    LEFT JOIN kpc353_2.groups AS g
        ON g.group_id = gm.joined_group_id
    LEFT JOIN kpc353_2.group_event AS ge
        ON ge.target_group_id = g.group_id
WHERE 
    gm.participant_member_id = $memberId;";
$result_events = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="event.css" />
<head>
    <meta charset="UTF-8">
    <title>Events Page</title>
</head>
<body>
<div class="main-content">
<div class="view-content-container">
    <h1>Events accessible to you</h1>
    <div id="center_button">
    <button> <a href='Organize_event.php' >Organize an event</a></button>
    </div>
    <table border="1">
        <tr>
            <th>View existing time and location options</th>
            <th>Suggest a time and location option </th>
            <th>Event name</th>
            <th>Group</th>
        </tr>
        
        <?php
        // Check if there are any results
        if ($result_events->num_rows > 0) {
            // Output data of each row
            while($row = $result_events->fetch_assoc()) {
                echo "<tr>";
                echo "<td><a href='event_vote.php?group_event_id=" . $row['group_event_id'] . "'><button>View date & location options</button></a></td>";
                echo "<td><a href='event_suggest.php?group_event_id=" . $row['group_event_id'] . "'><button>Suggest an option</button></a></td>";
                echo "<td>" . $row['event_name'] . "</td>";
                echo "<td>" . $row['group_name'] . "</td>";
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