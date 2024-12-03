<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="../css/admin_edit_member.css" />
<head>
    <meta charset="UTF-8">
    <title>Admin view users</title>
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
if (!isset($_GET['member_id'])) {
    echo "<script>alert('No member_ID is specified!');</script>";
    header("Location: homepage.php"); 
    exit;
}

$member_id = $_GET['member_id'];

//set db connections variables
$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "cosn";

// Create a database connection
$conn = new mysqli($dbServername, $dbUsername, $dbPassword, $dbName);

// Fetch member details for the form
$stmt = $conn->prepare("SELECT member_id,username,password,email,first_name,last_name,address,date_of_birth,privilege_level,pseudonym,status FROM members WHERE member_id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$stmt->bind_result($member_id, $username, $password, $email, $first_name, $last_name, $address, $date_of_birth, $privilege_level, $pseudonym, $status);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Member</title>
</head>
<body>
    <p>This section is only visible to admin users.</p>
        <label for="member_id">Member ID:</label>
        <input type="text" id="member_id" name="member_id" value="<?php echo $member_id; ?>" readonly><br>
        
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo $username; ?>" readonly><br>
        
        <label for="password">Password:</label>
        <input type="text" id="password" name="password" value="<?php echo $password; ?>" readonly><br>

        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="<?php echo $email; ?>" readonly><br>
        
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" readonly><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" readonly><br>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo $address; ?>" readonly><br>

        <label for="date_of_birth">Date Of Birth:</label>
        <input type="text" id="date_of_birth" name="date_of_birth" value="<?php echo $date_of_birth; ?>" readonly><br>

        <label for="privilege_level">Privilege Level:</label>
        <input type="text" id="privilege_level" name="privilege_level" value="<?php echo $privilege_level; ?>" readonly><br>

        <label for="pseudonym">Pseudonym:</label>
        <input type="text" id="pseudonym" name="pseudonym" value="<?php echo $pseudonym; ?>" readonly><br>
        
        <label for="status">Status:</label>
        <input type="text" id="status" name="status" value="<?php echo $status; ?>" readonly><br>
</html>