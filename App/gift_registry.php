<?php
session_start();
include("db_config.php");
include("header.php");
include('sidebar.php');

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

$logged_in_member_id = $_SESSION['member_id'];

//get registries where user is organizer
$sql = "SELECT 
            gr.gift_registry_id, gr.gift_registry_name, gr.gift_registry_description, m.username, COUNT(gri.gift_registry_ideas_id) as idea_count 
        FROM kpc353_2.gift_registry as gr
            INNER JOIN 
                kpc353_2.members as m 
                    ON gr.organizer_member_id = m.member_id
            LEFT JOIN 
                kpc353_2.gift_registry_ideas as gri 
                    ON gr.gift_registry_id = gri.target_gift_registry_id
        WHERE 
            gr.organizer_member_id = :logged_in_member_id
        GROUP BY 
            gr.gift_registry_id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['logged_in_member_id' => $logged_in_member_id]);
$my_registries = $stmt->fetchAll(PDO::FETCH_ASSOC);

//get registries where user is participant
$sql = "SELECT 
            gr.gift_registry_id, gr.gift_registry_name, gr.gift_registry_description, m.username, COUNT(gri.gift_registry_ideas_id) as idea_count 
        FROM kpc353_2.gift_registry as gr 
            INNER JOIN 
                kpc353_2.members as m 
                    ON gr.organizer_member_id = m.member_id
            LEFT JOIN 
                kpc353_2.gift_registry_ideas as gri 
                    ON gr.gift_registry_id = gri.target_gift_registry_id
            INNER JOIN 
                kpc353_2.gift_registry_participants as grp 
                    ON gr.gift_registry_id = grp.target_gift_registry_id
        WHERE 
            grp.participant_member_id = :logged_in_member_id
        OR EXISTS 
            (SELECT 
                1 
            FROM 
                kpc353_2.group_members as gm
                INNER JOIN 
                    kpc353_2.group_members as gm2 
                        on gm.joined_group_id = gm2.joined_group_id
            WHERE 
                gm.participant_member_id = gr.organizer_member_id 
                AND gm2.participant_member_id = :logged_in_member_id    
            )
        OR 
            (SELECT 
                privilege_level 
            FROM kpc353_2.members 
            WHERE 
                member_id = :logged_in_member_id) = 'administrator'
        GROUP BY 
            gr.gift_registry_id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['logged_in_member_id' => $logged_in_member_id]);
$participating_registries = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type="text/css" href="gift_registry.css">    
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