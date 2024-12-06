<?php
require_once 'create_content_post_function.php';
//include("header.php");
//include('sidebar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/cosn_sign_up.css" />
<head>
    <meta charset="UTF-8">
    <title>Create conetent</title>
</head>
<script>
        function updateContentField() {
            const dropdown = document.getElementById("content-type");
            const contentLabel = document.querySelector("label[for='content']");
            const contentField = document.getElementById("content");

            if (dropdown.value === "video") {
                contentLabel.textContent = "Input link to video:";
                contentField.outerHTML = `<input type="text" id="content" name="content" required>`;
            } else if (dropdown.value === "text") {
                contentLabel.textContent = "Text content of the post:";
                contentField.outerHTML = `<input type="text" id="content" name="content" required>`;
            } 
            else if (dropdown.value === "image") {
                contentLabel.textContent = "Input link to image:";
                contentField.outerHTML = `<input type="text" id="content" name="content" required>`;
            }
        }
</script>

<body>

<div class="container">

<!-- First part of the form to verify existing member -->
<form id="publicPost" method="POST">
        <h2>Create a COSN Post</h2>

        <label for="type">Choose content type to post:</label>
        <select id="content-type" name="content_type" onchange="updateContentField()">
            <option value="text">Text</option>
            <option value="video">Video</option>
            <option value="image">Image</option>
        </select>
        <br><br>

        <label for="title">Title of the post:</label>
        <input type="text" id="title" name="title" required>
        
        <label for="content">Content of the post:</label>
        <input type="text" id="content" name="content" required>
        <br><br>

        <label for="permission">Permissions on this post will be set in the next screen</label>
        <br><br>
		

        <button type="submit" name="publicPost" value="sumbit">Create public post</button>
</form>


<?php
    //catch PHP errors
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

	session_start();

//check if the form has been submitted    
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publicPost'])) {
    // Store form data into session variables
    $_SESSION['content_type'] = $_POST['content_type'];
    $_SESSION['content_title'] = $_POST['title'];
    $_SESSION['content_data'] = $_POST['content'];
    // Ensure member_id is correctly stored
    if (isset($_SESSION['member_id'])) {
        $_SESSION['member_id'] = $_SESSION['member_id'];
    } else {
        // Handle the case where member_id is not set
        echo "<script>alert('Member ID is not set. Please log in.');</script>";
        exit();
    }

    // Redirect to set the content permissions
    header('Location: set_content_permissions.php');
    exit();
    }
?>


</body>
</html>
