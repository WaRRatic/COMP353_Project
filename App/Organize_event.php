<?php
session_start(); // Start the session
include("db_config.php");
include("header.php");
include('sidebar.php'); 


// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
// Database connection
$conn = new mysqli('localhost', 'root', '', 'kpc353_2');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_event_id = $_POST['group_event_id'];
    $target_group_id = $_POST['target_group_id'];
    $event_organizer_member_id = $_POST['event_organizer_member_id'];
    $event_name = $_POST['event_name'];

    // Insert event into the database
    $stmt = $conn->prepare("INSERT INTO group_event (group_event_id, target_group_id, event_organizer_member_id, event_name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiis', $group_event_id, $target_group_id, $event_organizer_member_id, $event_name);

    if ($stmt->execute()) {
        echo "Event created successfully!";
        echo "<script>window.location.href = 'events.php';</script>"; 
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();

$member_id = $_SESSION['member_id']; // Get the logged-in user's ID

// Database connection
$conn = new mysqli('localhost', 'root', '', 'kpc353_2');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch groups the user is part of
$stmt = $conn->prepare("
    SELECT groups.group_id, groups.group_name 
    FROM groups 
    INNER JOIN group_members group_members ON groups.group_id = group_members.group_membership_id
    WHERE group_members.group_membership_id = ?
");
$stmt->bind_param('i', $member_id);
$stmt->execute();
$result = $stmt->get_result();

$groups = [];
while ($row = $result->fetch_assoc()) {
    $groups[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="event.css" />
<head>
<meta charset="UTF-8">
    <title>Create Event</title>
</head>
</head>
<body>
    <h1>Organize Event</h1>
    <form action="Organize_event.php" method="post">
    <div class="form-group">
        <label for="organizer_id">Organizer Member ID:</label>
        <input type="text" id="organizer_id" name="event_organizer_member_id" value="<?php echo htmlspecialchars($member_id); ?>" readonly>
    </div> 
        <div class="form-group">
        <label for="group_id">Target Group:</label>
        <select id="group_id" name="target_group_id" required>
            <option value="" disabled selected>Select a group</option>
            <?php foreach ($groups as $group): ?>
                <option value="<?php echo htmlspecialchars($group['group_id']); ?>">
                    <?php echo htmlspecialchars($group['group_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        </div>

    <div class="form-group">
        <label for="event_name">Event Name:</label>
        <input type="text" id="event_name" name="event_name" required>
    </div>

    <div class="form-group">
        <button type="submit">Create Event</button>
    </div>
</form>
</html>