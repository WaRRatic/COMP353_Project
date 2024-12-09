<?php
    session_start();
    include("db_config.php");
    include("header.php");
    include('sidebar.php');

    if (!isset($_SESSION['loggedin'])) {
        header("Location: index.php");
        exit;
    }

    $member_id = $_SESSION['member_id'];

    //mark gift as received
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_received'])) {
        $gift_id = $_POST['gift_id'];

        $sql = "UPDATE kpc353_2.gift_registry_gifts
                SET gift_status = 'received'
                WHERE gift_id = :gift_id
                AND target_gift_registry_id IN (
                    SELECT gift_registry_id
                    FROM kpc353_2.gift_registry
                    WHERE organizer_member_id = :member_id
                )";

        $stmt = $pdo ->prepare($sql);
        $stmt->execute([
            'gift_id' => $gift_id,
            'member_id' => $member_id
        ]);
    }

//get received gifts
$sql = "SELECT grg.gift_id, grg.gift_status, gri.gift_idea_description, gr.gift_registry_name, m.username as sender_name, grg.gift_date
        FROM kpc353_2.gift_registry_gifts grg
        JOIN kpc353_2.gift_registry_ideas gri ON grg.gift_registry_idea_id = gri.gift_registry_ideas_id
        JOIN kpc353_2.gift_registry gr ON grg.target_gift_registry_id = gr.gift_registry_id
        JOIN kpc353_2.members m ON grg.sender_member_id = m.member_id
        WHERE gr.organizer_member_id = :member_id
        ORDER BY grg.gift_date DESC";


$stmt = $pdo->prepare($sql);
$stmt->execute(['member_id' => $member_id]);
$received_gifts = $stmt->fetchAll();

//get sent gifts
$sql = "SELECT grg.gift_id, grg.gift_status, gri.gift_idea_description, gr.gift_registry_name, m.username as recipient_name, grg.gift_date
        FROM kpc353_2.gift_registry_gifts grg
        JOIN kpc353_2.gift_registry_ideas gri ON grg.gift_registry_idea_id = gri.gift_registry_ideas_id
        JOIN kpc353_2.gift_registry gr ON grg.target_gift_registry_id = gr.gift_registry_id
        JOIN kpc353_2.members m ON gr.organizer_member_id = m.member_id
        WHERE grg.sender_member_id = :member_id
        ORDER BY grg.gift_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['member_id' => $member_id]);
$sent_gifts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>My Gifts</title>
        <link rel="stylesheet" type="text/css" href="gift_registry.css">
    </head>
    <body>
        <div class="container">
            <h1>My Gifts</h1>

            <div class="gifts-section">
                <h2>Gifts Received</h2>
                <?php if ($received_gifts): ?>
                    <?php foreach ($received_gifts as $gift): ?>
                        <div class="gift-item">
                            <div class="gift-info">
                                <p><strong>Gift:</strong> <?= htmlspecialchars($gift['gift_idea_description']) ?></p>
                                <p><strong>From:</strong> <?= htmlspecialchars($gift['sender_name']) ?></p>
                                <p><strong>Registry:</strong> <?= htmlspecialchars($gift['gift_registry_name']) ?></p>
                                <p><strong>Date:</strong> <?= htmlspecialchars($gift['gift_date']) ?></p>
                                <p><strong>Status:</strong> <?= htmlspecialchars($gift['gift_status']) ?></p>
                            </div>
                            <?php if ($gift['gift_status'] == 'pending'): ?>
                                <span class="status pending">Pending</span>
                            <?php elseif ($gift['gift_status'] == 'sent'): ?>
                                <span class="status sent">Sent</span>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="gift_id" value="<?= $gift['gift_id'] ?>">
                                    <button type="submit" name="mark_received" class="receive-button">Mark as Received</button>
                                </form>
                            <?php else: ?>
                                <span class="status received">Received</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No gifts received yet.</p>
                <?php endif; ?>
            </div>

            <div class="gifts-section">
                <h2>Gifts Sent</h2>
                <?php if ($sent_gifts): ?>
                    <?php foreach ($sent_gifts as $gift): ?>
                        <div class="gift-item">
                            <div class="gift-info">
                                <p><strong>Gift:</strong> <?= htmlspecialchars($gift['gift_idea_description']) ?></p>
                                <p><strong>To:</strong> <?= htmlspecialchars($gift['recipient_name']) ?></p>
                                <p><strong>Registry:</strong> <?= htmlspecialchars($gift['gift_registry_name']) ?></p>
                                <p><strong>Date:</strong> <?= htmlspecialchars($gift['gift_date']) ?></p>
                                <p><strong>Status:</strong> <?= htmlspecialchars($gift['gift_status']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No gifts sent yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>