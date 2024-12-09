<?php
    session_start();
    include("db.php");
    include("header.php");
    include('sidebar.php');

    if (!isset($_SESSION['loggedin']) || !isset($_GET['idea_id'])) {
        header("Location: index.php");
        exit;
    }

    $member_id = $_SESSION['member_id'];
    $idea_id = $_GET['idea_id'];

    //get gift details
    $sql = "SELECT gri.gift_registry_ideas_id, gri.target_gift_registry_id, gri.gift_idea_description, gr.gift_registry_name, gr.organizer_member_id, m.username as recipient_name
            FROM gift_registry_ideas gri
            JOIN gift_registry gr ON gri.target_gift_registry_id = gr.gift_registry_id
            JOIN members m ON gr.organizer_member_id = m.member_id
            WHERE gri.gift_registry_ideas_id = :idea_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['idea_id' => $idea_id]);
    $gift_idea = $stmt->fetch();

    if (!$gift_idea) {
        header("Location: gift_registry.php");
        exit;
    }

    //check if user is authorized to send gifts
    if ($gift_idea['organizer_member_id'] == $member_id) {
        header("Location: view_registry.php?id=" . $gift_idea['target_gift_registry_id']);
        exit;
    }

    //check for duplicate gifts
    $sql = "SELECT gift_id
            FROM gift_registry_gifts
            WHERE gift_registry_idea_id = :idea_id
            AND sender_member_id = :sender_id
            AND gift_status != 'received'";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'idea_id' => $idea_id,
        'sender_id' => $member_id
    ]);

    if ($stmt->fetch()) {
        echo "<script>alert('You have already sent this gift and it hasn\'t been received yet.');</script>";
        echo "<script>window.location.href='view_registry.php?id=" . $gift_idea['target_gift_registry_id'] . "';</script>";
        exit;
    }

    //handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sql = "INSERT INTO gift_registry_gifts
                (target_gift_registry_id, gift_registry_idea_id, sender_member_id, gift_status)
                VALUES (:registry_id, :idea_id, :sender_id, 'sent')";
        
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([
            'registry_id' => $gift_idea['target_gift_registry_id'],
            'idea_id' => $idea_id,
            'sender_id' => $member_id
        ])) {
            //notification message
            $message = "sent you a gift from your registry: " . $gift_idea['gift_idea_description'];
            $sql = "INSERT INTO member_messages
                    (origin_member_id, target_member_id, message_content)
                    VALUES (:sender_id, :recipient_id, :message)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'sender_id' => $member_id,
                'recipient_id' => $gift_idea['organizer_member_id'],
                'message' => $message 
            ]);

            header("Location: view_registry.php?id=" . $gift_idea['target_gift_registry_id']);
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Send Gift</title>
        <link rel="stylesheet" type="text/css" href="gift_registry.css">
    </head>
    <body>
        <div class="container">
            <h1>Send Gift</h1>

            <div class="gift-details">
                <p><strong>Registry:</strong> <?= htmlspecialchars($gift_idea['gift_registry_name']) ?></p>
                <p><strong>Recipient:</strong> <?= htmlspecialchars($gift_idea['recipient_name']) ?></p>
                <p><strong>Gift:</strong> <?= htmlspecialchars($gift_idea['gift_idea_description']) ?></p>
            </div>

            <form method="POST" class="gift-form">
                <input type="hidden" name="gift_registry_id" value="<?=gift_idea['target_gift_registry_id'] ?>">
                <div class="button-container">
                    <button type="submit">Confirm </button>
                    <button type="button" onclick="window.location.href='view_registry.php?id=<?= $gift_idea['target_gift_registry_id'] ?>" class="cancel-button">Cancel</button>
                </div>
            </form>
        </div>
    </body>
</html>