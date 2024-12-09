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

// Check if the user is admin
if (!$_SESSION['privilege_level'] === 'administrator'){
    echo "<script>alert('Access denied - you must be admin!');</script>";
    echo "<script>window.location.href = 'homepage.php';</script>";
    exit;
}

$logged_in_member_id = $_SESSION['member_id'];

$conn = new mysqli($host, $user, $pass, $db);

// Prepare the SQL statement
$sql = "SELECT
        content_id, creator_id, content_type, content_data, content_creation_date, content_title, moderation_status
        FROM
        kpc353_2.content";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/admin_manage_content.css" />
<head>
    <meta charset="UTF-8">
    <title>COSN content moderation</title>
</head>

<body>
  <div class="main-content">
    <h1>COSN content moderation</h1>

    <table border="1">
        <tr>
            <th>Content ID</th>
            <th>Content Type</th>
            <th>Creator Data</th>
            <th>Content Title</th>
            <th>Moderation Status</th>
        </tr>
        
        <?php
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                 echo "<tr>";
                echo "<td>" . $row['content_id'] . "</td>";
                echo "<td>" . $row['content_type'] . "</td>";
                echo "<td>" . $row['content_data'] . "</td>";
                echo "<td>" . $row['content_title'] . "</td>";
                echo "<td>" . $row['moderation_status'] . "</td>";
                echo "<td>";
                echo "<td><a href='COSN_content_edit.php?content_id=" . $row['content_id'] . "'><button>Moderate</button></a></td>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No content found</td></tr>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </table>
    <br>
        <br>
        <br>

    </div>
</body>
</html>