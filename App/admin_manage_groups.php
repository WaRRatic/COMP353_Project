<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin manage groups</title>
</head>

<?php
session_start();

// Check if the user is logged in and has the "admin" role
if (!isset($_SESSION['loggedin']) || $_SESSION['privilege_level'] !== 'administrator') {
    // Redirect to an error page or homepage if not authorized
    header("Location: homepage.php"); 
    exit;
}

//set db values for connections
$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "cosn";

// Create a database connection
$conn = new mysqli($dbServername, $dbUsername, $dbPassword, $dbName);

// Query to get all members
$sql = "SELECT group_id, group_name, owner_id, description, creation_date FROM groups;";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
</head>
<body>
    <h1>Manage COSN groups</h1>
    <p>Only accessible by users with the admin role.</p>
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
                echo "<tr>";
                echo "<td><a href='admin_edit_groups.php?group_id=" . $row['group_id'] . "'><button>Edit group</button></a></td>";
                echo "<td>" . $row['group_id'] . "</td>";
                echo "<td>" . $row['group_name'] . "</td>";
                echo "<td>" . $row['owner_id'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['creation_date'] . "</td>";
                echo "<td>";
                echo "<td><a href='admin_view_group.php?group_id=" . $row['group_id'] . "'><button>View group</button></a></td>";
                echo "<form action='admin_delete_group.php' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this group?\");'>";
                echo "<input type='hidden' name='group_id' value='" . $row['group_id'] . "'>";
                echo "<button type='submit'>Delete group</button>";
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
        <ul>
            <li><a href="homepage.php">Back to Homepage</a></li>
        </ul>
</body>
</html>