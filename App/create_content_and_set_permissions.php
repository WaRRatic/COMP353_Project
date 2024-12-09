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

//check if the form has been submitted    
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publicPost'])) {
    // Get the form data for Content creation
    $content_type = $_POST['content_type'];
    $content_title = $_POST['content_title'];
    $content_data = $_POST['content_data'];
    $creator_member_id = $logged_in_member_id;

    // Begin transaction
    $pdo->beginTransaction();

    // Insert into content table
    $stmt = $pdo->prepare('
        INSERT INTO kpc353_2.content (
            creator_id, content_type, content_data, content_creation_date, content_title, moderation_status
        ) VALUES (
            :creator_id, :content_type, :content_data, NOW(), :content_title, "pending"
        )
    ');
    $stmt->execute([
        'creator_id'    => $creator_member_id,
        'content_type'  => $content_type,
        'content_data'  => $content_data,
        'content_title' => $content_title
    ]);

    // Get the ID of the inserted content
    $content_id = $pdo->lastInsertId();

    // Insert into edit permission for the member
    $stmt = $pdo->prepare('
    INSERT INTO kpc353_2.content_member_permission (
        target_content_id, authorized_member_id, content_permission_type
    ) VALUES (
        :content_id, :member_id, "edit")');
    $stmt->execute([
        'member_id'    => $logged_in_member_id,
        'content_id'  => $content_id
    ]);

    // Commit the transaction
    $pdo->commit();

    echo "<script>alert('Content created successfully, EDIT permission set for the creator. You can set futher permissions on this content in the next screen. Content will become visible when admin will approve/moderate it.');</script>";
    echo "<script>window.location.href = 'COSN_content_edit.php?content_id=" . $content_id . "';</script>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="create_content_and_set_permissions.css" />
<head>
    <meta charset="UTF-8">
    <title>Create conetent</title>
</head>
<body>

<div class="main-content">

<div class="container">

<!-- First part of the form to verify existing member -->
<form id="publicPost" method="POST" >
    <h1>Hello <?php echo $_SESSION['member_username'];?> !</h1>
    <h2>Create content (permissions can be set in the next screen)</h2>
    <hr>
    <label for="type">Choose type of content to post:</label>
    <select id="content-type" name="content_type">
        <option value="text">Text</option>
        <option value="video">Video</option>
        <option value="image">Image</option>
    </select>

    <label for="content_title">Content Title:</label>
    <input type="text" id="content_title" name="content_title"  ><br>
        
    <label for="content_data">Content Data:</label>
    <input type="text" id="content_data" name="content_data"  ><br>
        
    <br><br>
    <button type="submit" name="publicPost">Create content</button>
</form>

<br><br>
    <hr>
<a href="Homepage.php">Back to Homepage</a>

</div>
</body>
</html>
