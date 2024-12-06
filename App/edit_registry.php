<?php
session_start();
include("db.php");
include("header.php");
include("sidebar.php");

if (!isset($_SESSION['loggedin']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$member_id = $_SESSION['member_id'];
$registry_id = $_GET['id'];

// Check if user is organizer or admin
$stmt = $pdo->prepare("SELECT gr.*, m.privilege_level 
    FROM gift_registry gr
    JOIN members m ON m.member_id = :member_id
    WHERE gr.gift_registry_id = :registry_id");
$stmt->execute([
    'member_id' => $member_id,
    'registry_id' => $registry_id
]);
$registry = $stmt->fetch();

if ($registry['organizer_member_id'] != $member_id && $registry['privilege_level'] != 'administrator') {
    header("Location: gift_registry.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE gift_registry 
            SET gift_registry_name = :name, gift_registry_description = :description 
            WHERE gift_registry_id = :registry_id");
        $stmt->execute([
            'name' => $_POST['registry_name'],
            'description' => $_POST['registry_description'],
            'registry_id' => $registry_id
        ]);
        header("Location: view_registry.php?id=$registry_id");
        exit;
    } catch(PDOException $e) {
        echo "<script>alert('Error updating registry: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Registry</title>
    <link rel="stylesheet" type="text/css" href="./css/gift_registry.css">
</head>
<body>
    <div class="container">
        <h1>Edit Registry</h1>
        <form method="POST">
            <div class="form-group">
                <label for="registry_name">Registry Name:</label>
                <input type="text" name="registry_name" id="registry_name" 
                    value="<?= htmlspecialchars($registry['gift_registry_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="registry_description">Description:</label>
                <textarea name="registry_description" id="registry_description"><?= htmlspecialchars($registry['gift_registry_description']) ?></textarea>
            </div>
            <div class="button-container">
                <button type="submit" class="primary-button">Update Registry</button>
                <button type="button" onclick="location.href='view_registry.php?id=<?= $registry_id ?>'" 
                    class="cancel-button">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>