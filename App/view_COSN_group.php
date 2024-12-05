<?php

include("header.php");
include('sidebar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/view_COSN_group.css" />
<head>
    <meta charset="UTF-8">
    <title>View CONS group</title>
</head>
<?php
session_start();

// Check if the user is logged in via Sessuin
if (!isset($_SESSION['loggedin'])) {
    // Redirect to homepage if not authorized
    echo "<script>alert('Log in first!');</script>";
    header("Location: index.php"); 
    exit;
}

// Check if the member_ID was passed in the URL
if (!isset($_SESSION['member_id'])) {
    echo "<script>alert('No member_ID is specified!');</script>";
    header("Location: homepage.php"); 
    exit;
}

// Check if the Group_id was passed in the URL
if (!isset($_GET['group_id'])) {
    echo "<script>alert('No group_ID is specified!');</script>";
    header("Location: homepage.php"); 
    exit;
}

$member_id = $_SESSION['member_id'];
$group_id = $_GET['group_id'];

// Database connection parameters
$host = 'localhost';
$db   = 'cosn';
$user = 'root';
$pass = '';


$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "cosn";

$conn = new mysqli($dbServername, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL statement
$sql = "SELECT 
    group_id, group_name, m.username, description, creation_date
    FROM groups
    inner join members as m on groups.owner_id = m.member_id
    where group_deleted_flag = false
    and group_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>COSN Group Page</title>
</head>
<body>
  <div class="main-content">
    <h1>COSN group</h1>

    <table border="1">
        <tr>

            <th>Group ID</th>
            <th>Group Name</th>
            <th>Owner username</th>
            <th>Description</th>
            <th>Creation Date</th>
        </tr>
        
        <?php
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['group_id'] . "</td>";
                echo "<td>" . $row['group_name'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['creation_date'] . "</td>";
                echo "<td>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No groups found</td></tr>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </table>
    <br>
        <br>
        <br>

    </div>
</html>