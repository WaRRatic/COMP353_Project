<?php
session_start();
include("db.php");

if (!isset($_SESSION['loggedin']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$member_id = $_SESSION['member_id'];
$registry_id = $_GET['id'];

$host = 'localhost';
$db   = 'cosn';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
// check if user is organizer or admin
$sql = "SELECT gr.*, m.privilege_level 
        FROM gift_registry gr
        JOIN members m ON m.member_id = $member_id
        WHERE gr.gift_registry_id = $registry_id";

$result = $conn->query($sql);
$registry = $result->fetch_assoc();

if ($registry['organizer_member_id'] != $member_id && $registry['privilege_level'] != 'administrator') {
    header("Location: gift_registry.php");
    exit;
}

// adding new participant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_participant'])) {
    $new_participant = $_POST['participant_id'];
    $sql = "INSERT INTO gift_registry_participants 
            (participant_member_id, target_gift_registry_id) 
            VALUES ($new_participant, $registry_id)";
    $conn->query($sql);
}

// removing participant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_participant'])) {
    $remove_id = $_POST['remove_id'];
    $sql = "DELETE FROM gift_registry_participants 
            WHERE participant_member_id = $remove_id 
            AND target_gift_registry_id = $registry_id";
    $conn->query($sql);
}

// get current participants
$sql = "SELECT grp.*, m.username 
        FROM gift_registry_participants grp
        JOIN members m ON grp.participant_member_id = m.member_id
        WHERE grp.target_gift_registry_id = $registry_id";
$result = $conn->query($sql);
$participants = $result->fetch_all(MYSQLI_ASSOC);

// get potential participants
$sql = "SELECT member_id, username FROM members 
        WHERE member_id NOT IN (
            SELECT participant_member_id 
            FROM gift_registry_participants
            WHERE target_gift_registry_id = $registry_id
        )";
$result = $conn->query($sql);
$potential_participants = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Participants</title>
    <link rel="stylesheet" type="text/css" href="./css/gift_registry.css">
</head>
<body>
    <div class="container">
        <h1>Manage Registry Participants</h1>

        <div class="current-participants">
            <h2>Current Participants</h2>
            <?php foreach ($participants as $participant): ?>
                <div class="participant-item">
                    <?= htmlspecialchars($participant['username']) ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="remove_id" value="<?= $participant['participant_member_id'] ?>">
                        <button type="submit" name="remove_participant" class="remove-button">Remove</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" class="add-participant-form">
            <h2>Add Participant</h2>
            <select name="participant_id" required>
                <option value="">Select member</option>
                <?php foreach ($potential_participants as $member): ?>
                    <option value="<?= $member['member_id'] ?>">
                        <?= htmlspecialchars($member['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="add_participant" class="add-button">Add Participant</button>
        </form>

        <div class="button-container">
            <button onclick="location.href='view_registry.php?id=<?= $registry_id ?>'" 
                    class="secondary-button">Back to Registry</button>
        </div>
    </div>
</body>
</html>