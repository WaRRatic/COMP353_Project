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


// Create a database connection
$conn = new mysqli($host, $user, $pass, $db);

// Query to get all members
$sql = "SELECT member_id,username,password,email,first_name,last_name,address,date_of_birth,privilege_level,pseudonym,status 
FROM members
where member_deleted_flag = false
;";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
</head>
<body>
<div class="main-content">
<div class="view-content-container">
    <h1>Choose a Member to edit</h1>
    <p>This section is only visible to admin user.</p>
    <div id="center_button">
    <button onclick="location.href='admin_create_member.php'">Create member</button>
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
                echo "</form>";
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

</div>
</div>
</body>
</body>
</html>