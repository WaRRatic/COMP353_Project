<?php
    session_start();
    include("db.php");
    include("header.php");
    include('sidebar.php');

    if (!isset($_SESSION['loggedin']) || !isset($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    $member_id = $_SESSION['member_id'];
    $registry_id = $_GET['id'];

    //check if user is administrator
    $sql = "SELECT privilege_level
            FROM members
            WHERE member_id = :member_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['member_id' => $member_id]);
    $user_data = $stmt->fetch();
    $is_admin = ($user_data['privilege_level'] == 'administrator');

    //check if user is owner of registry & get registry details
    $sql = "SELECT organizer_member_id, gift_registry_name, gift_registry_description
            FROM gift_registry
            WHERE gift_registry_id = :registry_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['registry_id' => $registry_id]);
    $registry = $stmt->fetch();

    if ($registry['organizer_member_id'] != $member_id && !$is_admin) {
        header("Location: gift_registry.php");
        exit;
    }

    //handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_name = $_POST['registry_name'];
        $new_description = $_POST['registry_description'];

        $sql = $pdo->prepare($sql);
        if ($stmt->execute([
            'new_name' => $new_name,
            'new_description' => $new_description,
            'registry_id' => $registry_id
        ])) {
            header("Location: view_registry.php?id=" . $registry_id);
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset="UTF-8">
        <title>Edit Registry</title>
        <link rel="stylesheet" type="text/css" href="gift_registry.css">
    </head>
    <body>
        <div class="container">
            <h1>Edit Registry</h1>

            <form method="POST" class="registry-form">
                <div class="form-group">
                    <label for="registry_name">Registry Name:</label>
                    <input type="text"
                                id="registry_name"
                                name="registry_name"
                                value="<?=htmlspecialchars($registry['gift_registry_name']) ?>"
                                required>
                </div>

                <div class="form-group">
                    <label for="registry_description">Description:</label>
                    <textarea id="registry_description"
                              name="registry_description"
                              required><?= htmlspecialchars($registry['gift_registry_description']) ?></textarea>
                </div>

                <div class="button-container">
                    <button type="submit">Update Registry</button>
                    <button type="button" onclick="window.location.href='view_registry.php?id=<?= $registry_id ?>'" class="cancel-button">Cancel</button>
                </div>
            </form>
        </div>
    </body>
</html>