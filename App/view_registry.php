<?php
session_start();
include("db.php");

if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: gift_registry.php");
    exit;
}

$member_id = $_SESSION['member_id'];
$registry_id = $_GET['id'];

$host = 'localhost';
$db   = 'cosn';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

//get registry details
$sql = "SELECT gr.*, m.username
        FROM gift_registry gr
        JOIN members m ON gr.organizer_member_id = m.member_id
        WHERE gr.gift_registry_id = $registry_id";
$result = $conn->query($sql);
$registry = $result->fetch_assoc();

//check if user is admin
$sql = "SELECT privilege_level FROM members WHERE member_id = $member_id";
$result = $conn->query($sql);
$user_data = $result->fetch_assoc();
$is_admin = ($user_data['privilege_level'] == 'administrator');

// set is_participant to true if admin
$is_participant = $is_admin;

// only check regular participation if not admin
if (!$is_admin) {
    $sql = "SELECT * FROM gift_registry_participants 
            WHERE participant_member_id = $member_id 
            AND target_gift_registry_id = $registry_id
            UNION
            SELECT grp.* FROM gift_registry_participants grp
            JOIN group_members gm ON grp.participant_member_id = gm.participant_member_id
            JOIN group_members gm2 ON gm.joined_group_id = gm2.joined_group_id
            WHERE gm2.participant_member_id = $member_id
            AND grp.target_gift_registry_id = $registry_id";
    $result = $conn->query($sql);
    $is_participant = ($result->num_rows > 0);
}

//get gift ideas
$sql = "SELECT gri.*, m.username as added_by
        FROM gift_registry_ideas gri
        JOIN members m ON gri.idea_owner_id = m.member_id
        WHERE gri.target_gift_registry_id = $registry_id";
$result = $conn->query($sql);
$gift_ideas = $result->fetch_all(MYSQLI_ASSOC);

//handle new gift idea submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_participant) {
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

<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <title>View Registry</title>
    <link rel="stylesheet" type="text/css" href="./css/gift_registry.css">
</head>
<body>
    <div class="container">
        <h1>Gift Registry</h1>
        <p>Organized by: <?=htmlspecialchars($registry['username']) ?> </p>

        <div class="gift-list">
            <?php foreach ($gift_ideas as $gift): ?>
                <div class="gift-item">
                    <p class="description"><?=htmlspecialchars($gift['gift_idea_description']) ?></p>
                    <small>Added by: <?=htmlspecialchars($gift['added_by']) ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($is_participant): ?>
            <form method="POST" class="add-gift-form">
                <h3>Add New Gift Idea</h3>
                <input type="text" name="gift_idea" placeholder="Enter gift idea" required>
                <button type="submit" class="add-button">Add Gift</button>
            </form>
        <?php endif; ?>

        <div class="button-container">
            <?php if ($registry['organizer_member_id'] == $member_id): ?>
                <button onclick="location.href='manage_participants.php?id=<?= $registry_id ?>'" class="secondary-button">Manage Participants</button>
            <?php endif; ?>
            <button onclick="location.href='gift_registry.php'">Back to Registries</button>
            </div>
    </div>
</body>
</html>