<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

$member_id = $_SESSION['member_id'];  // Assume member ID is stored in the session


//set db connections variables
$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "cosn";
// Connect to the database
$conn = new mysqli($dbServername, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define the fields to update
$fields = ['first_name', 'last_name', 'email', 'address'];  // Add more fields as necessary

foreach ($fields as $field) {
    if (isset($_POST["{$field}_visibility"])) {
        $visibility = $_POST["{$field}_visibility"];
        
        // If visibility is 'group', you can optionally specify a group ID in the form
        $group_id = ($visibility == 'group' && isset($_POST["{$field}_group_id"])) ? $_POST["{$field}_group_id"] : null;

        // Update the privacy settings for each field
        $stmt = $conn->prepare("INSERT INTO member_privacy (member_id, field, visibility, group_id) VALUES (?, ?, ?, ?) 
                                ON DUPLICATE KEY UPDATE visibility = ?, group_id = ?");
        $stmt->bind_param("isssisi", $member_id, $field, $visibility, $group_id, $visibility, $group_id);
        
        if (!$stmt->execute()) {
            echo "Error updating privacy settings: " . $stmt->error;
        }
    }
}

$stmt->close();
$conn->close();

echo "Privacy settings updated successfully!";

// Fetch privacy settings from the database
$conn = new mysqli($dbServername, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$member_id = $_GET['member_id'];

// Query to get privacy settings for each field
$sql = "SELECT field, visibility, group_id FROM member_privacy WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

$privacy_settings = [];
while ($row = $result->fetch_assoc()) {
    $privacy_settings[$row['field']] = [
        'visibility' => $row['visibility'],
        'group_id' => $row['group_id']
    ];
}

$stmt->close();

// Fetch the actual member data
$sql = "SELECT first_name, last_name, email, address FROM Members WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$member_data = $stmt->get_result()->fetch_assoc();

$stmt->close();
$conn->close();

// Now display the information based on privacy settings
function get_visible_data($field, $member_data, $privacy_settings) {
    // Check visibility
    if ($privacy_settings[$field]['visibility'] == 'public') {
        return $member_data[$field];
    } elseif ($privacy_settings[$field]['visibility'] == 'group') {
        // Check if the member is in the correct group
        $group_id = $privacy_settings[$field]['group_id'];
        // Replace this with actual group check
        $user_group = 1;  // Assuming user is in group 1 for this example
        if ($user_group == $group_id) {
            return $member_data[$field];
        } else {
            return "Group members only";
        }
    } else {
        return "Private";
    }
}

// Display member's data with privacy check
echo "First Name: " . get_visible_data('first_name', $member_data, $privacy_settings) . "<br>";
echo "Last Name: " . get_visible_data('last_name', $member_data, $privacy_settings) . "<br>";
echo "Email: " . get_visible_data('email', $member_data, $privacy_settings) . "<br>";
echo "Address: " . get_visible_data('address', $member_data, $privacy_settings) . "<br>";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>User info permission</title>
</head>
<body>
    <h1>User info permission</h1>
<form method="POST" action="update_privacy.php">
    <label for="first_name">First Name</label>
    <select name="first_name_visibility">
        <option value="public" <?php echo $first_name == 'public' ? 'selected' : ''; ?>>Public</option>
        <option value="private" <?php echo $first_name == 'private' ? 'selected' : ''; ?>>Private</option>
        <option value="group" <?php echo $first_name == 'group' ? 'selected' : ''; ?>>Group</option>
    </select><br>

    <label for="last_name">Last Name</label>
    <select name="last_name_visibility">
        <option value="public" <?php echo $last_name == 'public' ? 'selected' : ''; ?>>Public</option>
        <option value="private" <?php echo $last_name == 'private' ? 'selected' : ''; ?>>Private</option>
        <option value="group" <?php echo $last_name == 'group' ? 'selected' : ''; ?>>Group</option>
    </select><br>

    <!-- Repeat this for other fields such as email, address, etc. -->
    
    <input type="submit" value="Save Privacy Settings">
</form>
</html>