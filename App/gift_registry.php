<?php
session_start();
include 'db.php'; //include database connection
include('sidebar.php'); 
include("header.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

$member_id = $_SESSION['member_id'];
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type="text/css" href="./css/gift_registry.css">    
<head>
    <meta charset="UTF-8">
    <title>Gift Registry</title>
</head>
<body>
    <div class="container"> 
        <h1>Gift Registries</h1>

        <div class="button-container">
            <button onclick="location.href='create_registry.php'" class="add-button">Create New Registry</button>
        </div>
        
        <?php
        // Database connection
$host = 'localhost';
$db   = 'cosn';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $user, $pass, $options);


        //get registries where user is organizer
        $sql = "SELECT gr.gift_registry_id, gr.gift_registry_name, gr.gift_registry_description, m.username, COUNT(gri.gift_registry_ideas_id) as idea_count 
                FROM gift_registry gr
                JOIN members m ON gr.organizer_member_id = m.member_id
                LEFT JOIN gift_registry_ideas gri ON gr.gift_registry_id = gri.target_gift_registry_id
                WHERE gr.organizer_member_id = $member_id
                GROUP BY gr.gift_registry_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $my_registries = $stmt->fetchAll(PDO::FETCH_ASSOC);


        //get registries where user is participant
        $sql = "SELECT gr.gift_registry_id, gr.gift_registry_name, gr.gift_registry_description, m.username, COUNT(gri.gift_registry_ideas_id) as idea_count 
                FROM gift_registry gr 
                JOIN members m ON gr.organizer_member_id = m.member_id
                LEFT JOIN gift_registry_ideas gri ON gr.gift_registry_id = gri.target_gift_registry_id
                JOIN gift_registry_participants grp ON gr.gift_registry_id = grp.target_gift_registry_id
                WHERE grp.participant_member_id = $member_id
                OR EXISTS (
                    SELECT 1 FROM group_members gm
                    JOIN group_members gm2 on gm.joined_group_id = gm2.joined_group_id
                    WHERE gm.participant_member_id = gr.organizer_member_id 
                    AND gm2.participant_member_id = $member_id    
                )
                OR (SELECT privilege_level FROM members WHERE member_id = $member_id) = 'administrator'
                GROUP BY gr.gift_registry_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $participating_registries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if ($my_registries): ?>
            <h2>My Registries</h2>
            <?php foreach ($my_registries as $registry): ?>
                <div class="registry-item">
                    <h3><?= htmlspecialchars($registry['gift_registry_name']) ?></h3>
                    <p><?= htmlspecialchars($registry['gift_registry_description']) ?></p>
                    <p>Organizer: <?= htmlspecialchars($registry['username']) ?></p>
                    <p>Number of Items: <?= $registry['idea_count'] ?></p>
                    <div class="button-container">
                        <a href="view_registry.php?id=<?=$registry['gift_registry_id']?>" class="view-button">View Registry</a>
                        <a href="edit_registry.php?id=<?=$registry['gift_registry_id'] ?>" class="edit-button">Edit Registry</a>
                        <a href="manage_participants.php?id=<?= $registry['gift_registry_id'] ?>" class="edit-button">Manage Participants</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!$my_registries && !$participating_registries): ?>
            <p> You aren't part of any gift registries yet. Create one or ask to be added to an existing registry! </p>
        <?php endif; ?>
    </div>
</body>
</html>

