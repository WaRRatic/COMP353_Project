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
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$member_id = $_SESSION['member_id']; // Get the logged-in user's ID

// Create a database connection
$conn = new mysqli($host, $user, $pass, $db);

// Query to get all events
$sql = "SELECT 
    ge.group_event_id,
    ge.target_group_id,
    ge.event_organizer_member_id,
    ge.event_name,
    g.group_name
FROM 
    kpc353_2.group_members AS gm
    LEFT JOIN kpc353_2.groups AS g
        ON g.group_id = gm.joined_group_id
    LEFT JOIN kpc353_2.group_event AS ge
        ON ge.target_group_id = g.group_id
WHERE 
    gm.participant_member_id = $member_id;";
$result_events = $conn->query($sql);
$conn->close();

// Database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch groups the user is part of
$stmt = $conn->prepare("
    SELECT g.group_id, g.group_name 
    FROM kpc353_2.groups as g
        INNER JOIN kpc353_2.group_members as gm
            ON g.group_id = gm.joined_group_id
    WHERE gm.participant_member_id = ?
");
$stmt->bind_param('i', $member_id);
$stmt->execute();
$result = $stmt->get_result();

$groups = [];
while ($row = $result->fetch_assoc()) {
    $groups[] = $row;
}
$stmt->close();





// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_group_id = $_POST['target_group_id'];
    $event_organizer_member_id = $_POST['event_organizer_member_id'];
    $event_name = $_POST['event_name'];

    // Insert event into the database
    $stmt = $conn->prepare("INSERT INTO kpc353_2.group_event 
            (target_group_id, event_organizer_member_id, event_name) 
            VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $target_group_id, $event_organizer_member_id, $event_name);
    $stmt->execute();
    $conn->close();
    echo "<script>window.location.href = 'Organize_event.php';</script>";
    exit;
}

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
<div class="main-content">
<div class="view-content-container">
    <h1>Events accessible to you</h1>
    <div id="center_button">
    <button> <a href='Organize_event.php' >Organize an event</a></button>
    </div>
    <table border="1">
        <tr>
            <th>View existing time and location options</th>
            <th>Suggest a time and location option </th>
            <th>Event name</th>
            <th>Group</th>
        </tr>
        
        <?php
        // Check if there are any results
        if ($result_events->num_rows > 0) {
            // Output data of each row
            while($row = $result_events->fetch_assoc()) {
                echo "<tr>";
                echo "<td><a href='event_vote.php?group_event_id=" . $row['group_event_id'] . "'><button>View date & location options</button></a></td>";
                echo "<td><a href='event_suggest.php?group_event_id=" . $row['group_event_id'] . "'><button>Suggest an option</button></a></td>";
                echo "<td>" . $row['event_name'] . "</td>";
                echo "<td>" . $row['group_name'] . "</td>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No events found</td></tr>";
        }

        ?>
    </table>
        <br>

        <ul>
            <li><a href="homepage.php">Back to Homepage</a></li>
        </ul>

</div>
</div>

<hr>

    <h1>Organize Event</h1>
    
    <?php if($groups): ?>
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
    <?php else: ?>
    <p>You are not part of any groups. Please join a group to create an event.</p>
    <?php endif; ?>


    
</html>