<?php
include("db_config.php");
include("header.php");
include('sidebar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/view_COSN_group_public_page.css" />
<head>
    <meta charset="UTF-8">
    <title>COSN Group Public Page</title>
</head>


<?php
session_start();

// Check if the user is logged in via Sessuin
if (!isset($_SESSION['loggedin'])) {
    // Redirect to homepage if not authorized
    echo "<script>alert('Log in first!');</script>";
    header("Location: index.php"); 
    exit;
}

// Check if the member_ID was passed in the URL
if (!isset($_SESSION['member_id'])) {
    echo "<script>alert('No member_ID is specified!');</script>";
    header("Location: homepage.php"); 
    exit;
}

// Check if the Group_id was passed in the URL
if (!isset($_GET['group_id'])) {
    echo "<script>alert('No group_ID is specified!');</script>";
    header("Location: homepage.php"); 
    exit;
}

$member_id = $_SESSION['member_id'];
$group_id = $_GET['group_id'];


$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to get the Group details
$sql = "SELECT 
    group_id, group_name, m.username, description, creation_date
    FROM groups
    inner join members as m on groups.owner_id = m.member_id
    where group_deleted_flag = false
    and group_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();


// The query checks the membership status of a specific participant in a specific group
// Here's the breakdown:
//     Uses COALESCE to handle null values
//     Subquery looks in the group_members table
//     If no membership record is found, returns 'outsider'
//     Returns a single column named group_member_status
$sql_status = "SELECT 
    COALESCE((
        SELECT gm.group_member_status
        FROM group_members as gm 
        WHERE joined_group_id = ?
        AND gm.participant_member_id = ?
    ), 'outsider') AS group_member_status;
";

$stmt_status = $conn->prepare($sql_status);
$stmt_status->bind_param("ii", $group_id, $logged_in_member_id);
$stmt_status->execute();
$status_result = $stmt_status->get_result();
$row_status = $status_result->fetch_assoc();


//handle new gift idea submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    if (!empty($_POST['gift_idea'])) {
        $gift_idea = $_POST['gift_idea'];
        $sql = "INSERT INTO gift_registry_ideas
                (target_gift_registry_id, idea_owner_id, gift_idea_description)
                VALUES ($registry_id, $member_id, '$gift_idea')";
        if ($conn->query($sql)) {
            header("Location: view_registry.php?id=$registry_id");
            exit;
        }
    }
}

?>


<body>
  <div class="main-content">
    <h1>COSN Group Public Page</h1>
    <h2>Your status within this group: <?php echo $row_status['group_member_status']; ?> </h2>

    <table border="1">
        <tr>

            <th>Group ID</th>
            <th>Group Name</th>
            <th>Owner username</th>
            <th>Description</th>
            <th>Creation Date</th>
        </tr>
        
        <?php
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['group_id'] . "</td>";
                echo "<td>" . $row['group_name'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['creation_date'] . "</td>";
                echo "<td>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No groups found</td></tr>";
        }

        // Close the database connection
        $conn->close();
        ?>
        </table>

        <?php if ($row_status['group_member_status'] == 'owner'): ?>
            <button onclick="location.href='manage_COSN_group_participants.php?id=<?= $group_id ?>'" class="secondary-button">Manage Group Participants</button>
            <br>
        <?php endif; ?>
        
        <?php if ($row_status['group_member_status'] == 'member'): ?>
            <button onclick="location.href='view_COSN_group_internal_page.php?id=<?= $group_id ?>'" class="secondary-button">Visit internal COSN page</button>
            <br>
        <?php endif; ?>
        
        <?php if ($row_status['group_member_status'] == 'outsider'): ?>
            <button onclick="location.href='view_COSN_group_internal_page.php?id=<?= $group_id ?>'" class="secondary-button">Request admin to join COSN page</button>
            <br>
        <?php endif; ?>

        <?php if ($row_status['group_member_status'] == 'requested'): ?>
            <p>Waiting for approval from the COSN group admin</p>
            <br>
        <?php endif; ?>

        <?php if ($row_status['group_member_status'] == 'ousted'): ?>
            <p>You have been ousted from the group... OUCH!</p>
            <br>
        <?php endif; ?>

        


    </div>
</html>