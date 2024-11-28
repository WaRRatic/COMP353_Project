<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/admin_edit_member.css" />
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

// If the form is submitted, update the member's data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // update the variables from the form, when the "Update Member" button is click and a POST request is sent
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $date_of_birth = $_POST['date_of_birth'];
    $privilege_level = $_POST['privilege_level'];
    $pseudonym = $_POST['pseudonym'];
    $status = $_POST['status'];

    // Begin a transaction, as not to lose the member data if an error due to data type mismatch is produced 
    $conn->begin_transaction();


    $stmt = $conn->prepare("UPDATE Members SET username = ?, password = ?, email = ?, first_name = ?, last_name = ?, address = ?, date_of_birth = ?, privilege_level = ?, pseudonym = ?, status = ? WHERE member_id = ?");
    //'i' is for integer, 's' is for string, each "letter" must match the data types of the parameter, thefore 12 letters = 12 parameters
    $stmt->bind_param("ssssssssssi", $username, $password, $email, $first_name, $last_name, $address, $date_of_birth, $privilege_level, $pseudonym, $status,$member_id);
    try{
        if ($stmt->execute()) {
            $conn->commit(); //if the update goes through without error, commit the transaction therefore saving the data
            echo "<script>alert('Member updated successfully!');</script>";
            header("Location: admin_manage_users.php"); // Redirect to the members list upon successful update
        } else {
            // Rollback the transaction if there's an error
            $conn->rollback();
            echo "<script>alert('Error updating the member! Check your datatypes and try again... $conn->error;');</script>";
        }
    } catch (Exception $e) {
        // Rollback in case of ENUM validation error or any other failure
        $conn->rollback();
        // Output an alert and use JavaScript for redirection
        echo "<script>alert('Error updating the member! Check your datatypes and try again: " . addslashes($e->getMessage()) . "');</script>";
        echo "<script>window.location.href = 'admin_edit_member.php?member_id=" . $member_id . "&error=" . urlencode($e->getMessage()) . "';</script>";
        exit;
    }

    $stmt->close();
    $conn->close();
    exit;
}

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
    <title>Edit Member</title>
</head>
<body>
    <h1>Edit Member</h1>
    <p>This section is only visible to admin users.</p>
    <form method="POST">
        <label for="member_id">Member ID:</label>
        <input type="text" id="member_id" name="member_id" value="<?php echo $member_id; ?>" required><br>
        
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo $username; ?>" required><br>
        
        <label for="password">Password:</label>
        <input type="text" id="password" name="password" value="<?php echo $password; ?>" required><br>

        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="<?php echo $email; ?>" required><br>
        
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" required><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" required><br>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo $address; ?>" required><br>

        <label for="date_of_birth">Date Of Birth:</label>
        <input type="text" id="date_of_birth" name="date_of_birth" value="<?php echo $date_of_birth; ?>" required><br>

        <label for="privilege_level">Privilege Level:</label>
        <input type="text" id="privilege_level" name="privilege_level" value="<?php echo $privilege_level; ?>" required><br>

        <label for="pseudonym">Pseudonym:</label>
        <input type="text" id="pseudonym" name="pseudonym" value="<?php echo $pseudonym; ?>" required><br>
        
        <label for="status">Status:</label>
        <input type="text" id="status" name="status" value="<?php echo $status; ?>" required><br>

        <button type="submit">Update Member</button>
    </form>

</html>