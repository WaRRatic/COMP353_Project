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

// If the form is submitted, create a new group
if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {

    $group_name = $_POST['group_name'];
    $description = $_POST['description'];
    $category = $_POST['category'];

    try{
        // Create new group 
        
        // Begin the transaction
        $pdo->beginTransaction();

        $sql = "
        INSERT INTO kpc353_2.groups (group_name, description, owner_id, creation_date, category)
        VALUES (:group_name, :description, :logged_in_member_id, NOW(), :category)
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':group_name' => $group_name,':description' => $description, ':logged_in_member_id' => $logged_in_member_id, ':category' => $category]);
        
        //get the id of the newly created group
        $newId = $pdo->lastInsertId();

        $sql = "
        INSERT INTO kpc353_2.group_members (participant_member_id, joined_group_id, group_member_status,date_joined)
        VALUES (:logged_in_member_id,:new_group_id,'admin', NOW())
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':new_group_id' => $newId]);

        // Commit the transaction
        $pdo->commit();

        //alert the user that the group was created successfully
        echo "<script>alert('Group created successfully!');</script>";
        echo "<script>window.location.href = 'COSN_groups.php';</script>"; 
        exit;
    } catch (Exception $e) {
        // Output an alert and use JavaScript for redirection
        echo "<script>alert('Error creating COSN group! Check your datatypes and try again: " . addslashes($e->getMessage()) . "');</script>";
        echo "<script>window.location.href = 'COSN_groups.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="COSN_groups.css" />
<head>
    <meta charset="UTF-8">
    <title>Create new COSN group</title>
</head>
<body>
<div class="main-content">
    <h1>Create new COSN Group</h1>

    <form method="POST">
        <label for="group_name">Group name:</label>
        <input type="text" id="group_name" name="group_name" required><br>

        <label for="description">Group Description:</label>
        <input type="text" id="description" name="description" required><br>
        
        <label for="category">Group Category:</label>
        <input type="text" id="category" name="category"><br>

        <button type="submit" style="background-color: green; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Create new COSN Group</button>
    </form>

</div>
</html>