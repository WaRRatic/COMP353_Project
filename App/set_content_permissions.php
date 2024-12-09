<?php
//include("header.php");
//include('sidebar.php'); ?>
<!-- 
CREATE  TABLE kpc353_2.content_member_permission ( 
	content_member_permission_id INT UNSIGNED   NOT NULL   PRIMARY KEY,
	target_content_id    INT UNSIGNED      ,
	authorized_member_id INT UNSIGNED      ,
	content_permission_type ENUM('read','edit','comment','share','modify-permission','moderate','link')       
 ) engine=InnoDB; -->

 <!-- CREATE  TABLE kpc353_2.content_public_permissions ( 
	content_public_permission_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_content_id    INT UNSIGNED      ,
	content_public_permission_type ENUM('read','comment','share','link')       
 ) engine=InnoDB; -->



<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/cosn_sign_up.css" />
<head>
    <meta charset="UTF-8">
    <title>Set content permission</title>


    <script>
    function addPublicPermission() {
        const tableBody = document.querySelector('#public-permissions-table tbody');
        const row = document.createElement('tr');

        // Permission Level
        const permissionLevelCell = document.createElement('td');
        permissionLevelCell.textContent = 'public';
        row.appendChild(permissionLevelCell);

        // Permission Type
        const permissionTypeCell = document.createElement('td');
        const permissionTypeSelect = document.createElement('select');
        ['read', 'write', 'comment'].forEach(type => {
            const option = document.createElement('option');
            option.value = type;
            option.text = type;
            permissionTypeSelect.add(option);
        });
        permissionTypeCell.appendChild(permissionTypeSelect);
        row.appendChild(permissionTypeCell);

        // Action
        const actionCell = document.createElement('td');
        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Remove';
        deleteButton.onclick = () => row.remove();
        actionCell.appendChild(deleteButton);
        row.appendChild(actionCell);

        tableBody.appendChild(row);
    }

    function addMemberPermission() {
        const tableBody = document.querySelector('#member-permissions-table tbody');
        const row = document.createElement('tr');

        // Permission Level
        const permissionLevelCell = document.createElement('td');
        permissionLevelCell.textContent = 'member';
        row.appendChild(permissionLevelCell);

        // Authorized Member
        const authorizedMemberCell = document.createElement('td');
        const memberInput = document.createElement('input');
        memberInput.type = 'text';
        authorizedMemberCell.appendChild(memberInput);
        row.appendChild(authorizedMemberCell);

        // Permission Type
        const permissionTypeCell = document.createElement('td');
        const permissionTypeSelect = document.createElement('select');
        ['read', 'write', 'comment', 'share'].forEach(type => {
            const option = document.createElement('option');
            option.value = type;
            option.text = type;
            permissionTypeSelect.add(option);
        });
        permissionTypeCell.appendChild(permissionTypeSelect);
        row.appendChild(permissionTypeCell);

        // Action
        const actionCell = document.createElement('td');
        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Remove';
        deleteButton.onclick = () => row.remove();
        actionCell.appendChild(deleteButton);
        row.appendChild(actionCell);

        tableBody.appendChild(row);
    }

    function setPermissions() {
        const publicPermissions = [];
        const memberPermissions = [];

        // Collect Public Permissions
        document.querySelectorAll('#public-permissions-table tbody tr').forEach(row => {
            const permissionLevel = 'public';
            const permissionType = row.cells[1].querySelector('select').value;
            publicPermissions.push({ permission_level: permissionLevel, permission_type: permissionType });
        });

        // Collect Member Permissions
        document.querySelectorAll('#member-permissions-table tbody tr').forEach(row => {
            const permissionLevel = 'member';
            const authorizedMember = row.cells[1].querySelector('input').value;
            const permissionType = row.cells[2].querySelector('select').value;
            memberPermissions.push({
                permission_level: permissionLevel,
                authorized_member: authorizedMember,
                permission_type: permissionType
            });
        });

        // Print the arrays
        // console.log('Public Permissions:', publicPermissions);
        // console.log('Member Permissions:', memberPermissions);

        // // Optional: Display the arrays on the page
        // alert('Public Permissions:\n' + JSON.stringify(publicPermissions, null, 2) +
        //       '\n\nMember Permissions:\n' + JSON.stringify(memberPermissions, null, 2));
    }
</script>
</head>
<?php //catch PHP errors
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

	session_start();
?>


<h1>Content will be created AFTER you set permissions!</h1>
<h3>Public Content Permissions</h3>
<table id="public-permissions-table">
    <thead>
        <tr>
            <th>[Permission Level]</th>
            <th>[Permission Type]</th>
            <th>[Action]</th>
        </tr>
    </thead>
    <tbody>
        <!-- Public permissions will be added here -->
    </tbody>
</table>
<button class="add-button" onclick="addPublicPermission()">Add Public Permission</button>

<h3>Member Content Permissions</h3>
<table id="member-permissions-table">
    <thead>
        <tr>
            <th>[Permission Level]</th>
            <th>[Authorized Member]</th>
            <th>[Permission Type]</th>
            <th>[Action]</th>
        </tr>
    </thead>
    <tbody>
        <!-- Member permissions will be added here -->
    </tbody>
</table>
<button class="add-button" onclick="addMemberPermission()">Add Member Permission</button>
<br>
<br>
<button class="set-button" onclick="setPermissions()">SET PERMISSIONS</button>


<?php

   // Retrieve form data from session variables
   if (
    isset($_SESSION['content_type']) &&
    isset($_SESSION['content_title']) &&
    isset($_SESSION['content_data']) &&
    isset($_SESSION['member_id'])
) {
    $content_type = $_SESSION['content_type'];
    $content_title = $_SESSION['content_title'];
    $content_data = $_SESSION['content_data'];
    $creator_id = $_SESSION['member_id'];
} else {
    // Handle missing session variables
    echo "<script>alert('Session data is missing. Please start over. ');</script>";
    // echo "<script>alert('Session data is missing. Please start over.". " \n content_type:".$content_type ." ');</script>";
    // echo $content_type;
    // echo $content_title;
    // echo $content_data;
    // echo $creator_id;
    // header('Location: index.php');
}


// Retrieve permissions arrays from POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publicPermissions']) && isset($_POST['memberPermissions'])) {
    $publicPermissions = $_POST['publicPermissions']; // Should be an array
    $memberPermissions = $_POST['memberPermissions']; // Should be an array of arrays with 'member_id' and 'permission'

    try {
        $dbServername = "localhost";
        $dbUsername = "root";
        $dbPassword = "";
        $dbName = "cosn";
        
        $pdo = new PDO("mysql:host=$dbServername;dbname=$dbName", $dbUsername, $dbPassword);
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
            'creator_id'    => $creator_id,
            'content_type'  => $content_type,
            'content_data'  => $content_data,
            'content_title' => $content_title
        ]);

        // Get the ID of the inserted content
        $content_id = $pdo->lastInsertId();

        // Insert into content_public_permissions table
        if (!empty($publicPermissions)) {
            $stmtPublic = $pdo->prepare('
                INSERT INTO content_public_permissions (content_id, permission) VALUES (:content_id, :permission)
            ');
            foreach ($publicPermissions as $permission) {
                $stmtPublic->execute([
                    'content_id' => $content_id,
                    'permission' => $permission
                ]);
            }
        }

        // Insert into content_member_permission table
        if (!empty($memberPermissions)) {
            $stmtMember = $pdo->prepare('
                INSERT INTO content_member_permission (content_id, member_id, permission) VALUES (:content_id, :member_id, :permission)
            ');
            foreach ($memberPermissions as $memberPermission) {
                $stmtMember->execute([
                    'content_id'  => $content_id,
                    'member_id'   => $memberPermission['member_id'],
                    'permission'  => $memberPermission['permission']
                ]);
            }
        }

        // Commit the transaction
        $pdo->commit();

        echo "<script>alert('Content and permissions have been set successfully.');</script>";
        header('Location: success.php');
        exit();

    } catch (Exception $e) {
        // Roll back the transaction if something failed
        $pdo->rollBack();
        echo "<script>alert('An error occurred: " . $e->getMessage() . "');</script>";
        header('Location: error.php');
        exit();
    }
} 
// else {
//     echo "<script>alert('Permissions data is missing. Please start over.');</script>";
//     // header('Location: index.php');
//     exit();
// }



?>


</body>
</html>
