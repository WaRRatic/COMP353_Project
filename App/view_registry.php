<?php
session_start();
include("db_config.php");
include('sidebar.php'); 
include("header.php");


// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
$logged_in_member_id = $_SESSION['member_id'];

// Check if the ID is set
if (!isset($_GET['id'])) {
    echo "<script>alert('No registry ID is specified!');</script>";
    echo "<script>window.location.href = 'gift_registry.php';</script>";
    exit;
}
$registry_id = $_GET['id'];

$conn = new mysqli($host, $user, $pass, $db);

//get registry details
$sql = "SELECT 
            gr.*, m.username
        FROM kpc353_2.gift_registry gr
            INNER JOIN kpc353_2.members as m 
                ON gr.organizer_member_id = m.member_id
        WHERE 
            gr.gift_registry_id = $registry_id";
$result = $conn->query($sql);
$registry = $result->fetch_assoc();

//check if user is admin
$sql = "SELECT 
            privilege_level 
        FROM kpc353_2.members 
        WHERE 
            member_id = $logged_in_member_id";
$result = $conn->query($sql);
$user_data = $result->fetch_assoc();
$is_admin = ($user_data['privilege_level'] == 'administrator');

// set is_participant to true if admin
$is_participant = $is_admin;

// only check regular participation if not admin
if (!$is_admin) {
    $sql = "SELECT * 
            FROM kpc353_2.gift_registry_participants 
            WHERE 
                participant_member_id = $logged_in_member_id 
                AND target_gift_registry_id = $registry_id
            UNION
            SELECT 
                grp.* 
            FROM kpc353_2.gift_registry_participants grp
                INNER JOIN kpc353_2.group_members as gm 
                    ON grp.participant_member_id = gm.participant_member_id
                INNER JOIN kpc353_2.group_members as gm2 
                    ON gm.joined_group_id = gm2.joined_group_id
            WHERE 
                gm2.participant_member_id = $logged_in_member_id
                AND grp.target_gift_registry_id = $registry_id";
    $result = $conn->query($sql);
    $is_participant = ($result->num_rows > 0);
}

//get gift ideas
$sql = "SELECT 
            gri.*, m.username as added_by
        FROM kpc353_2.gift_registry_ideas gri
            INNER JOIN kpc353_2.members as m
                ON gri.idea_owner_id = m.member_id
        WHERE 
            gri.target_gift_registry_id = $registry_id";
$result = $conn->query($sql);
$gift_ideas = $result->fetch_all(MYSQLI_ASSOC);

//handle new gift idea submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_participant) {
    if (!empty($_POST['gift_idea'])) {
        $gift_idea = $_POST['gift_idea'];
        
        $sql = "INSERT INTO kpc353_2.gift_registry_ideas
                    (target_gift_registry_id, idea_owner_id, gift_idea_description)
                VALUES 
                    ($registry_id, $logged_in_member_id, '$gift_idea')";
        if ($conn->query($sql)) {
            header("Location: view_registry.php?id=$registry_id");
            exit;
        }
    }
}

// Handle idea deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_idea'])) {
    $idea_id = $_POST['delete_idea'];
    // Only allow deletion by admin or registry owner
    if ($is_admin || $registry['organizer_member_id'] == $logged_in_member_id) {
        $stmt = $pdo->prepare("DELETE FROM kpc353_2.gift_registry_ideas 
                                WHERE 
                                    gift_registry_ideas_id = :idea_id 
                                    AND target_gift_registry_id = :registry_id");
        $stmt->execute([
            'idea_id' => $idea_id,
            'registry_id' => $registry_id
        ]);
        header("Location: view_registry.php?id=$registry_id");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <title>View Registry</title>
    <link rel="stylesheet" type="text/css" href="gift_registry.css">
</head>
<body>
    <div class="container"> 
        <div>Gift Registry Name : <?=htmlspecialchars($registry['gift_registry_name']) ?></div>
        <div>Gift Registry Description : <?=htmlspecialchars($registry['gift_registry_description']) ?></div>
        <div>Organized by : <?=htmlspecialchars($registry['username']) ?> </div>

        <div class="gift-list">
            <?php foreach ($gift_ideas as $gift): ?>
                <div class="gift-item">
                    <p class="description"><?=htmlspecialchars($gift['gift_idea_description']) ?></p>
                    <small>Added by: <?=htmlspecialchars($gift['added_by']) ?></small>
                    <?php if ($registry['organizer_member_id'] == $logged_in_member_id || $is_admin): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="delete_idea" value="<?= $gift['gift_registry_ideas_id'] ?>">
                            <button type="submit" class="delete-button" onclick="return confirm('Are you sure you want to delete this gift idea? ');">Delete Gift idea</button>
                        </form>
                    <?php endif; ?>
                    <?php if ($registry['organizer_member_id'] != $logged_in_member_id): ?>
                        <a href="send_gift.php?idea_id=<?= $gift['gift_registry_ideas_id'] ?>" class="send-button">Send Gift</a>
                    <?php endif; ?>
                    <?php if ($is_participant): ?>
                        <?php
                        //check if gift is already sent
                        $stmt = $pdo->prepare("SELECT gift_status FROM gift_registry_gifts
                                               WHERE gift_registry_idea_id = :idea_id
                                               AND sender_member_id = :sender_id");
                        $stmt->execute(['idea_id' => $gift['gift_registry_ideas_id'], 'sender_id' => $logged_in_member_id]);
                        $gift_status = $stmt->fetch();
                        if($gift_status): ?>
                            <span class="gift-status"><?= htmlspecialchars($gift_status['gift_status']) ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
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
            <?php if ($registry['organizer_member_id'] == $logged_in_member_id): ?>
                <button onclick="location.href='manage_participants.php?id=<?= $registry_id ?>'" class="secondary-button">Manage Participants</button>
            <?php endif; ?>
            <button onclick="location.href='gift_registry.php'">Back to Registries</button>
            </div>
    </div>
</body>
</html>