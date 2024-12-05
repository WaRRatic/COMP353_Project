<?php
session_start();
include("db.php");

//check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

$member_id = $_SESSION['member_id'];

if ($_SERVER['REQUEST_METHOD'] ==='POST') {
    //insert into gift_registry 
    $sql = "INSERT INTO gift_registry (organizer_member_id) VALUES ($member_id)";
    $result = $conn->query($sql);

    if ($result) {
        $registry_id = $conn->insert_id;

        //add creator as a participant
        $sql = "INSERT INTO gift_registry_participant
                (participant_member_id, target_gift_registry_id)
                VALUES ($member_id, $registry_id)";
        $conn->query($sql);

        //add gift ideas
        if (!empty($_POST['gift_ideas'])) {
            foreach ($_POST['gift_ideas'] as $idea) {
                if (!empty($idea)) {
                    $sql = "INSERT INTO gift_registry_ideas
                            (target_gift_registry_id, idea_owner_id, gift_idea_description)
                            VALUES ($registry_id, $member_id, '$idea')";
                    $conn->query($sql);
                }
            }
        }

    header("Location: gift_registry.php");
    exit;
} else {
    echo "<script>alert('Error creating registry');</script>";
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Gift Registry</title>
    <link rel="stylesheet" type="text/css" href="./css/gift_registry.css">
</head>

<body>
    <div class="container">
        <h1>Create New Gift Registry</h1>

        <form method="POST" id="registryForm">
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