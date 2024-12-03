<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/admin_edit_groups.css" />
<head>
    <meta charset="UTF-8">
    <title>Admin edit groups</title>
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

// If the form is submitted, update the group's data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // update the variables from the form, when the "Update Group" button is click and a POST request is sent
    $group_id = $_POST['group_id'];
    $group_name = $_POST['group_name'];
    $owner_id = $_POST['owner_id'];
    $description = $_POST['description'];
    $creation_date = $_POST['creation_date'];

    // Begin a transaction, as not to lose the group data if an error due to data type mismatch is produced 
    $conn->begin_transaction();


    $stmt = $conn->prepare("UPDATE Groups SET group_id = ?, group_name = ?, owner_id = ?, description = ?, creation_date = ? WHERE group_id = ?");
    //'i' is for integer, 's' is for string, each "letter" must match the data types of the parameter, thefore 12 letters = 12 parameters
    $stmt->bind_param("isiss", $group_id, $group_name, $owner_id, $description, $creation_date);
    try{
        if ($stmt->execute()) {
            $conn->commit(); //if the update goes through without error, commit the transaction therefore saving the data
            echo "<script>alert('Group updated successfully!');</script>";
            header("Location: admin_manage_groups.php"); // Redirect to the members list upon successful update
        } else {
            // Rollback the transaction if there's an error
            $conn->rollback();
            echo "<script>alert('Error updating the group! Check your datatypes and try again... $conn->error;');</script>";
        }
    } catch (Exception $e) {
        // Rollback in case of ENUM validation error or any other failure
        $conn->rollback();
        // Output an alert and use JavaScript for redirection
        echo "<script>alert('Error updating the group! Check your datatypes and try again: " . addslashes($e->getMessage()) . "');</script>";
        echo "<script>window.location.href = 'admin_edit_gropus.php?group_id=" . $group_id . "&error=" . urlencode($e->getMessage()) . "';</script>";
        exit;
    }

    $stmt->close();
    $conn->close();
    exit;
}

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
    <title>Edit Group</title>
</head>
<body>
    <h1>Edit Group</h1>
    <p>This section is only visible to admin users.</p>
    <form method="POST">
        <label for="group_id">Group ID:</label>
        <input type="text" id="group_id" name="group_id" value="<?php echo $group_id; ?>" required><br>
        
        <label for="group_name">Group Name:</label>
        <input type="text" id="group_name" name="group_name" value="<?php echo $group_name; ?>" required><br>
        
        <label for="owner_id">Owner ID:</label>
        <input type="text" id="owner_id" name="owner_id" value="<?php echo $owner_id; ?>" required><br>

        <label for="description">Description:</label>
        <input type="text" id="description" name="description" value="<?php echo $description; ?>" required><br>
        
        <label for="creation_date">Creation Date:</label>
        <input type="text" id="creation_date" name="creation_date" value="<?php echo $creation_date; ?>" required><br>

        <button type="submit">Update Group</button>
    </form>

</html>