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
    group_event_options.option_description,
    group_event_options.target_group_event_id,
    group_event.group_event_id
FROM 
    group_event_options
INNER JOIN
    group_event
ON 
    group_event_options.target_group_event_id = group_event.group_event_id
WHERE
    group_event_options.target_group_event_id = $event_id
;";
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
            <th></th>
            <th>Suggested Date, Time, Location</th>
        </tr>
        
        <?php
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><a href='event_suggest.php?group_event_id=" . $row['group_event_id'] . "'><button>Vote for this date</button></a></td>";
                echo "<td>" . $row['option_description'] . "</td>";
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