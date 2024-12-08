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


    //check if user is administrator
    $sql = "SELECT privilege_level
            FROM kpc353_2.members
            WHERE member_id = :member_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['member_id' => $logged_in_member_id]);
    $user_data = $stmt->fetch();
    $is_admin = ($user_data['privilege_level'] == 'administrator');

    //check if user is owner of registry & get registry details
    $sql = "SELECT organizer_member_id, gift_registry_name, gift_registry_description
            FROM kpc353_2.gift_registry
            WHERE gift_registry_id = :registry_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['registry_id' => $registry_id]);
    $registry = $stmt->fetch();

    if ($registry['organizer_member_id'] != $logged_in_member_id && !$is_admin) {
        echo "<script>alert('You don't have appropriate permissions to edit this registry!');</script>";
        echo "<script>window.location.href = 'gift_registry.php';</script>";
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