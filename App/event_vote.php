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

$event_id = $_GET['group_event_id'];

$sql = "SELECT 
    geo.option_description,
    ge.event_name,
    geo.target_group_event_id,
    ge.group_event_id,
    geo.group_event_options_id,
    COALESCE(sum(option_voting_decision),0) as vote_count
    FROM 
        kpc353_2.group_event_options as geo
            INNER JOIN kpc353_2.group_event as ge
                ON geo.target_group_event_id = ge.group_event_id
            LEFT JOIN kpc353_2.group_event_option_vote as geo_v
                ON geo_v.target_group_event_option_id = geo.group_event_options_id
    WHERE
    geo.target_group_event_id = $event_id
    GROUP BY    
        geo.option_description,
        ge.event_name,
        geo.target_group_event_id,
        geo.group_event_options_id,
        ge.group_event_id;";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type = "text/css" href="event.css"/>
    <title>Vote on Event</title>
</head>
<body>
<div class="main-content">
<div class="view-content-container">
    <h1>Dates</h1>
    <table border="1">
        <tr>
            <th>Vote action</th>
            <th>Vote counter</th>
            <th>Option: Suggested Date, Time, Location</th>
            <th>Event Name</th>
            <th></th>
        </tr>
        
        <?php
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><a href='COSN_group_event_option_vote.php?group_event_id=" . $row['group_event_id'] . "&group_event_option_id=".$row['group_event_options_id'] ."'><button>Vote for this date</button></a></td>";
                echo "<td>" . $row['vote_count'] . "</td>";
                echo "<td>" . $row['option_description'] . "</td>";
                echo "<td>" . $row['event_name'] . "</td>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No Date found, try suggesting one</td></tr>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </table>
</div>
</div>
</body>
</html>