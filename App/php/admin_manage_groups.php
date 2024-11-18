<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin manage groups</title>
    <style>
        /* Night Mode Styles */
        body {
            background-color: #1a1a1a;
            color: #e0e0e0;
            font-family: Arial, sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        .container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            text-align: center;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #2a2a2a;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background-color: #333;
            color: #e0e0e0;
            border: 1px solid #555;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            border: none;
            background-color: #666;
            color: #ffffff;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #555;
        }
    </style>
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