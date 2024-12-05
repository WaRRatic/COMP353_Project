<?php //catch PHP errors
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

	session_start();
    //set the logged in member id
    $logged_in_member_id = $_SESSION['member_id'];
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/create_content_and_set_permissions.css" />
<head>
    <meta charset="UTF-8">
    <title>Create conetent</title>
</head>
<script src="js/updateContentField.js"></script>
<script src="js/addPublicPermission.js"></script>
<script src="js/addMemberPermission.js"></script>
<script src="js/addGroupPermission.js"></script>
<script src="js/setPermissions.js"></script>
<script>
    // Add event listeners to the buttons
    document.getElementById('addPublicPermissionButton').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default action of submitting the form
        addPublicPermission(); // we only want the button to add a new row to the table
    });

    document.getElementById('addMemberPermissionButton').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default action of submitting the form
        addMemberPermission(); // we only want the button to add a new row to the table
    });
    document.getElementById('addGroupPermissionButton').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default action of submitting the form
        addGroupPermission(); // we only want the button to add a new row to the table
    });
</script>

<body>



<div class="container">

<!-- First part of the form to verify existing member -->
<form id="publicPost" method="POST" onsubmit="return setPermissions();" >
    <h1>Hello <?php echo $_SESSION['member_username'];?> !</h1>
    <h2>Create a COSN post and set permissions using the forms below</h2>
    <hr>
    <label for="type">Choose type of content to post:</label>
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
    <hr>

    <h2>Set Content Permissions -- PUBLIC</h2>
    <table id="public-permissions-table">
        <thead>
            <tr>
                <th>[Permission Level]</th>
                <th>[Permission Type]</th>
                <th>[Remove permission?]</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <button id="addPublicPermissionButton" type="button" class="add-button" onclick="addPublicPermission()">Add Public Permission</button>
    <hr>

    <h2>Set Content Permissions -- MEMBERS</h2>

    <table id="member-permissions-table">
        <thead>
            <tr>
                <th>[Permission Level]</th>
                <th>[Authorized Member (ID)]</th>
                <th>[Permission Type]</th>
                <th>[Remove permission?]</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <button id="addMemberPermissionButton" type="button" class="add-button" onclick="addMemberPermission()">Add Member Permission</button>
    <br><br>
    <hr>

    <h2>Set Content Permissions -- GROUPS</h2>
    <table id="group-permissions-table">
        <thead>
            <tr>
                <th>[Permission Level]</th>
                <th>[Authorized Group (ID)]</th>
                <th>[Permission Type]</th>
                <th>[Remove permission?]</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <button id="addGroupPermissionButton" type="button" class="add-button" onclick="addGroupPermission()">Add Group Permission</button>
    <br><br>
    <hr>

    <!-- Hidden Input Fields that will hold the serialized permission data. -->
    <input type="hidden" id="publicPermissions" name="publicPermissions">
    <input type="hidden" id="memberPermissions" name="memberPermissions">
    <input type="hidden" id="groupPermissions" name="groupPermissions">

    <button type="submit" name="publicPost">Create public post and set permissions</button>
</form>

<br><br>
    <hr>
<a href="Homepage.php">Back to Homepage</a>




<?php

//check if the form has been submitted    
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publicPost'])) {
    // Get the form data for Content creation
    $content_type = $_POST['content_type'];
    $content_title = $_POST['title'];
    $content_data = $_POST['content'];
    $creator_member_id = $logged_in_member_id;
  
    // Get the permissions data from hidden fields
    $publicPermissionsJson = $_POST['publicPermissions'] ?? '[]';
    $memberPermissionsJson = $_POST['memberPermissions'] ?? '[]';
    $groupPermissionsJson = $_POST['groupPermissions'] ?? '[]';

    // Decode the JSON strings into PHP arrays
    $publicPermissions = json_decode($publicPermissionsJson, true);
    $memberPermissions = json_decode($memberPermissionsJson, true);
    $groupPermissions = json_decode($groupPermissionsJson, true);

    try {
        //set DB connection
        $host = "localhost";
        $user = "root";
        $pass= "";
        $db= "cosn";
        
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Begin transaction
        $pdo->beginTransaction();

        // Insert into content table
        $stmt = $pdo->prepare('
            INSERT INTO content (
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

        // Insert into content_public_permissions table
        if (!empty($publicPermissions)) {
            $stmtPublic = $pdo->prepare('
                INSERT INTO content_public_permissions (target_content_id, content_public_permission_type) VALUES (:target_content_id, :content_public_permission_type)
            ');
            foreach ($publicPermissions as $permission) {
                $stmtPublic->execute([
                    'target_content_id' => $content_id,
                    'content_public_permission_type' => $permission
                ]);
            }
        }

        // Insert into content_member_permission table
        if (!empty($memberPermissions)) {
            $stmtMember = $pdo->prepare('
                INSERT INTO content_member_permission (target_content_id, authorized_member_id, content_permission_type) VALUES (:target_content_id, :authorized_member_id, :content_permission_type)
            ');
            foreach ($memberPermissions as $memberPermission) {
                $stmtMember->execute([
                    'target_content_id'  => $content_id,
                    'authorized_member_id'   => $memberPermission['member_id'],
                    'content_permission_type'  => $memberPermission['permission']
                ]);
            }
        }
        
        // Insert into group_member_permission table
        if (!empty($groupPermissions)) {
            $stmtMember = $pdo->prepare('
                INSERT INTO content_group_permissions (target_content_id, target_group_id, content_group_permission_type) VALUES (:target_content_id, :target_group_id, :content_group_permission_type)
            ');
            foreach ($groupPermissions as $groupPermission) {
                $stmtMember->execute([
                    'target_content_id'  => $content_id,
                    'target_group_id'   => $groupPermission['group_id'],
                    'content_group_permission_type'  => $groupPermission['permission']
                ]);
            }
        }

        // Commit the transaction
        $pdo->commit();

        echo "<script>alert('Content and permissions have been set successfully.');</script>";

        exit();

    } catch(PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();

        // alert the user of the error
        echo "<script>
            alert('A database error occurred: ' + " . json_encode($e->getMessage()) . ");
            console.error('Database Error: ' + " . json_encode($e->getMessage()) . ");
        </script>";
        
        // Redirect to the same page, to allow the user to try again
        header('Location: create_content_and_set_permissions.php');
        exit();
    }

}
?>


</body>
</html>
