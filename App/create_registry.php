<?php
session_start();
include("db_config.php");
include("header.php");
include("sidebar.php");

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}


$logged_in_member_id = $_SESSION['member_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        //begin the transaction
        $pdo->beginTransaction();

        // Insert into gift_registry
        $stmt = $pdo->prepare("
            INSERT INTO 
                kpc353_2.gift_registry 
                    (organizer_member_id, gift_registry_name, gift_registry_description) 
                VALUES 
                    (:member_id, :gift_registry_name, :gift_registry_description)");
        
        $stmt->execute([
            'member_id' => $logged_in_member_id,
            'gift_registry_name' => $_POST['registry_name'],
            'gift_registry_description' => $_POST['registry_description']
        ]);

        $registry_id = $pdo->lastInsertId();

        // Add creator as participant
        $stmt = $pdo->prepare("
            INSERT INTO 
                kpc353_2.gift_registry_participants
                    (participant_member_id, target_gift_registry_id)
                VALUES 
                    (:member_id, :registry_id)");
        $stmt->execute([
            'member_id' => $logged_in_member_id,
            'registry_id' => $registry_id
        ]);

        // Add gift ideas
        if (!empty($_POST['gift_ideas'])) {
            $stmt = $pdo->prepare("
                INSERT INTO 
                    kpc353_2.gift_registry_ideas
                        (target_gift_registry_id, idea_owner_id, gift_idea_description)
                    VALUES 
                        (:registry_id, :member_id, :idea)");
            
            foreach ($_POST['gift_ideas'] as $idea) {
                if (!empty($idea)) {
                    $stmt->execute([
                        'registry_id' => $registry_id,
                        'member_id' => $logged_in_member_id,
                        'idea' => $idea
                    ]);
                }
            }
        }
        
        //commit the transaction
        $pdo->commit();

        echo "<script>alert('Registry created successfully!');</script>";
        echo "<script>window.location.href = 'gift_registry.php';</script>";
        exit;

    } catch(PDOException $e) {
        $pdo->rollBack();
        echo "<script>alert('Error creating registry: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Gift Registry</title>
    <link rel="stylesheet" type="text/css" href="gift_registry.css">
</head>

<body>
    <div class="container">
        <h1>Create New Gift Registry</h1>

        <form method="POST" id="registryForm">
            <div class="registry-info">
                <h3>Registry Details</h3>
                <div class="form-group">
                    <label for="registry_name">Registry Name:</label>
                    <input type="text" name="registry_name" id="registry_name" required>
                </div>
                <div class="form-group">
                    <label for="registry_description">Description:</label>
                    <textarea name="registry_description" id="registry_description"></textarea>
                </div>
            </div>
            
            <div id="giftIdeas">
                <h3>Add Gift Ideas</h3>
                <div id="giftList">
                    <div class="gift-idea-input">
                        <input type="text" name="gift_ideas[]" placeholder="Gift idea" required>
                    </div>
                </div>
            <button type="button" onclick="addGiftIdea()" class="add-button">Add Another Gift Idea</button>
    </div>

    <div class="button-container">
        <button type="submit" class="primary-button">Create Registry</button>
        <button type="button" onclick="location.href='gift_registry.php'" class="cancel-button">Cancel</button>
    </div>
        </form>
</div>

<script>
    function addGiftIdea() {
        const giftList = document.getElementById('giftList');
        const div = document.createElement('div');
        div.className = 'gift-idea-input';
        div.innerHTML = `
            <input type="text" name="gift_ideas[]" placeholder="Gift Idea">
            <button type="button" onclick="this.parentElement.remove()" class="remove-button">Remove</button>
            `;
            giftList.appendChild(div);
    }
    </script>
</body>
</html>