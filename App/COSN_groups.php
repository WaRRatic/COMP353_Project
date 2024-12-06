<?php
include("db_config.php");
include("header.php");
include('sidebar.php');

session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: homepage.php"); 
    exit;
}

$logged_in_member_id = $_SESSION['member_id'];


$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL statement
$sql = "SELECT 
    group_id, group_name, owner_id, description, creation_date,
    CASE WHEN (owner_id = ?) || (1=?) THEN 'admin' ELSE 'member' END AS group_role
    FROM groups
    where group_deleted_flag = false";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $logged_in_member_id, $logged_in_member_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/COSN_groups.css" />
<head>
    <meta charset="UTF-8">
    <title>COSN groups</title>
</head>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>COSN Groups Page</title>
</head>
<body>
  <div class="main-content">
    <h1>COSN groups</h1>

    <table border="1">
        <tr>
            <th></th>
            <th>Group ID</th>
            <th>Group Name</th>
            <th>Owner ID</th>
            <th>Description</th>
            <th>Creation Date</th>
        </tr>
        
        <?php
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                $isAdmin = ($row['group_role'] === 'admin') ? 'admin-only' : 'hidden';
                echo "<tr>";
                echo "<td><a href='edit_COSN_group.php?group_id=" . $row['group_id'] . "'><button class='" . $isAdmin . "'>Edit group </button></a></td>";
                echo "<td>" . $row['group_id'] . "</td>";
                echo "<td>" . $row['group_name'] . "</td>";
                echo "<td>" . $row['owner_id'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['creation_date'] . "</td>";
                echo "<td>";
                echo "<td><a href='view_COSN_group_public_page.php?group_id=" . $row['group_id'] . "'><button>View group</button></a></td>";
                echo "</form>";
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
</body>
</html>