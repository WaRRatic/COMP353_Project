<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/admin_manage_users.css" />
<head>
    <meta charset="UTF-8">
    <title>Admin manage users</title>
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
$sql = "SELECT member_id,username,password,email,first_name,last_name,address,date_of_birth,privilege_level,pseudonym,status FROM members;";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
</head>
<body>
    <h1>Choose a Member to edit</h1>
    <p>This section is only visible to admin users.</p>
    <div id="center_button">
    <button><a href="admin_create_member.php">Create member</a></button>
    </div>
    <table border="1">
        <tr>
            <th></th>
            <th>Member_ID</th>
            <th>Username</th>
            <th>Password</th>
            <th>Email</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Address</th>
            <th>Date of Birth</th>
            <th>Privilege level</th>
            <th>Pseudonym</th>
            <th>Status</th>
        </tr>
        
        <?php
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><a href='admin_edit_member.php?member_id=" . $row['member_id'] . "'><button>Edit member</button></a></td>";
                echo "<td>" . $row['member_id'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['password'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['first_name'] . "</td>";
                echo "<td>" . $row['last_name'] . "</td>";
                echo "<td>" . $row['address'] . "</td>";
                echo "<td>" . $row['date_of_birth'] . "</td>";
                echo "<td>" . $row['privilege_level'] . "</td>";
                echo "<td>" . $row['pseudonym'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>";
                echo "<td><a href='admin_view_member.php?member_id=" . $row['member_id'] . "'><button>View member</button></a></td>";
                echo "<td><form action='admin_delete_member.php' method='POST' style='display:inline;'>";
                echo "<input type='hidden' name='member_id' value='" . $row['member_id'] . "'>";
                echo "<button type='submit' onclick='return confirm(\"Are you sure you want to delete this member?\");'>Delete member</button>";
                echo "</td></form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No members found</td></tr>";
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
</body>
</html>