<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/admin_edit_groups.css" />
<head>
    <meta charset="UTF-8">
    <title>Admin View groups</title>
</head>
<?php
session_start();

// Check if the user is logged in and has the "admin" role
if (!isset($_SESSION['loggedin']) || $_SESSION['privilege_level'] !== 'administrator') {
    // Redirect to an error page or homepage if not authorized
    header("Location: homepage.php"); 
    exit;
}

// Check if the ID is set
if (!isset($_GET['group_id'])) {
    echo "<script>alert('No group_ID is specified!');</script>";
    header("Location: homepage.php"); 
    exit;
}
// Set group_id variable for both fetching and updating
$group_id = $_GET['group_id'];

//set db connections variables
$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "cosn";

// Create a database connection
$conn = new mysqli($dbServername, $dbUsername, $dbPassword, $dbName);

// Fetch group details for the form
$stmt = $conn->prepare("SELECT group_id,group_name,owner_id,description,creation_date FROM groups WHERE group_id = ?");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$stmt->bind_result($group_id, $group_name, $owner_id, $description, $creation_date);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Group</title>
</head>
<body>
    <h1>View Group</h1>
    <p>This section is only visible to admin users.</p>
        <label for="group_id">Group ID:</label>
        <input type="text" id="group_id" name="group_id" value="<?php echo $group_id; ?>" readonly><br>
        
        <label for="group_name">Group Name:</label>
        <input type="text" id="group_name" name="group_name" value="<?php echo $group_name; ?>" readonly><br>
        
        <label for="owner_id">Owner ID:</label>
        <input type="text" id="owner_id" name="owner_id" value="<?php echo $owner_id; ?>" readonly><br>

        <label for="description">Description:</label>
        <input type="text" id="description" name="description" value="<?php echo $description; ?>" readonly><br>
        
        <label for="creation_date">Creation Date:</label>
        <input type="text" id="creation_date" name="creation_date" value="<?php echo $creation_date; ?>" readonly><br>
</html>