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

// Check if the ID is set
if (!isset($_GET['group_id'])) {
    echo "<script>alert('No group_ID is specified!');</script>";
    echo "<script>window.location.href = 'COSN_groups.php';</script>";
    exit;
}

// Set group_id variable for both fetching and updating
$group_id = $_GET['group_id'];

//get the logged in member id
$logged_in_member_id = $_SESSION['member_id'];

// Check if the user is an admin
$isAdmin = false;
if ($_SESSION['privilege_level'] === 'administrator'){
    $isAdmin = true;
}

if(!$isAdmin){
    // Get the group data from the database
    $sql = "
    SELECT 
        group_membership_id
    FROM 
        kpc353_2.group_members
    WHERE 
        joined_group_id = :group_id
        AND participant_member_id = :logged_in_member_id
        AND group_member_status = 'admin'
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':group_id' => $group_id]);
    $isGroupOwner = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!$isGroupOwner){
        echo "<script>alert('You don't have admin privileges of this group!');</script>";
        echo "<script>window.location.href = 'COSN_groups.php';</script>";
        exit;
    }
}


// Get the group data from the database
$sql = "
SELECT 
    group_id,group_name,owner_id,description,creation_date,category
FROM 
    kpc353_2.groups 
WHERE 
    group_id = :group_id
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':group_id' => $group_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

//load the variables which will be used to populate the form
$group_id = $result[0]['group_id'];
$group_name = $result[0]['group_name'];
$owner_id = $result[0]['owner_id'];
$description = $result[0]['description'];
$creation_date = $result[0]['creation_date'];
$category = $result[0]['category'];

// Get the group member data from database
$sql = "
SELECT 
    m.member_id, m.username, gm.group_member_status 
FROM 
    kpc353_2.groups as g
        LEFT JOIN kpc353_2.group_members as gm
            ON g.group_id = gm.joined_group_id
        LEFT JOIN kpc353_2.members as m
            ON gm.participant_member_id = m.member_id
WHERE 
    group_id = :group_id
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':group_id' => $group_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If the form is submitted, update the group's data
if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    if (isset($_POST['delete_group'])) {
        try {
            $sql_delete = '
            DELETE FROM
                kpc353_2.groups 
            WHERE
                group_id = :group_id';

            $deleteStmt = $pdo->prepare($sql_delete);
            $deleteStmt->execute(['group_id' => $group_id]);

            echo "<script>alert('Group deleted successfully!');";
            echo "window.location.href = 'COSN_groups.php';</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Error deleting group: " . addslashes($e->getMessage()) . "');</script>";
            exit;
        }
    // update the variables from the form, when the "Update Group" button is click and a POST request is sent
    }else{    
        $group_name = $_POST['group_name'];
        $owner_id = $_POST['owner_id'];
        $description = $_POST['description'];
        $category = $_POST['category'];

        try{
            // Fetch the selected group data from the database
            $sql = "
            UPDATE 
                kpc353_2.groups
            SET
                group_name = :group_name,
                owner_id = :owner_id,
                description = :description,
                category = :category

            WHERE 
                group_id = :group_id
            ";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([':group_id' => $group_id, ':group_name' => $group_name, ':owner_id' => $owner_id, ':description' => $description,':category' => $category]);

            echo "<script>alert('Group updated successfully!');</script>";
            echo "<script>window.location.href = 'COSN_groups.php';</script>"; 
            exit;
        } catch (Exception $e) {
            // Output an alert and use JavaScript for redirection
            echo "<script>alert('Error updating the group! Check your datatypes and try again: " . addslashes($e->getMessage()) . "');</script>";
            echo "<script>window.location.href = 'edit_COSN_group.php?group_id=" . $group_id . "&error=" . urlencode($e->getMessage()) . "';</script>";
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="COSN_group_admin.css" />
<head>
    <meta charset="UTF-8">
    <title>Edit COSN group</title>
</head>
<body>
<div class="main-content">
    <h1>Manage COSN Group</h1>

    <form method="POST">

        <label for="group_name">Group name:</label>
        <input type="text" id="group_name" name="group_name" value="<?php echo $group_name; ?>" required><br>
        
        <label for="owner_id">Group owner ID (cannot be changed):</label>
        <input type="text" id="owner_id" name="owner_id" value="<?php echo $owner_id; ?>" readonly><br>

        <label for="description">Description:</label>
        <input type="text" id="description" name="description" value="<?php echo $description; ?>" required><br>
        
        <label for="description">Category:</label>
        <input type="text" id="Category" name="Category" value="<?php echo $category; ?>" ><br>

        <button type="submit">Update Group</button>
        <button type="submit" name="delete_group" onclick="return confirm('Are you sure you want to delete this group? ');" style="background-color: #ff4444; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;"> Delete COSN Group
        </button>
    </form>

    <br><br><hr>

    <h1>Manage members of COSN Group</h1>
    <table border="1">
        <tr>
            <th>Member username</th>
            <th>Member status in the group</th>
            <th>Accept request</th>
            <th>Ban member</th>
        </tr>
        
        <?php
        // Check if there are any results
        if ($row = $result) {
            // Output data of each row
            while($row = current($result)) {
                echo "<tr>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['group_member_status'] ."</td>";

                //Accept request
                if($row['group_member_status'] === 'requested'){
                    echo "<td><a href='COSN_group_accept_request.php?group_id=" . $group_id . "&member_id=". $row['member_id'] ."'><button style='background-color: green; color: black;'>Accept join request</button></a></td>";
                } elseif($row['group_member_status'] === 'member' ){
                    echo "<td style='background-color: gray; color: white;'>Already a member</td>";
                } elseif($row['group_member_status'] === 'ousted'){
                    echo "<td style='background-color: gray; color: white;'>Member was banned!</td>";
                } elseif($row['group_member_status'] === 'admin' || $row['member_id'] === 1){
                    echo "<td style='background-color: gray; color: white;'>Already admin</td>";
                }

                //Ban (oust) member
                if($row['group_member_status'] === 'requested'){
                    echo "<td><a href='COSN_group_ban_member.php?group_id=" . $group_id . "&member_id=". $row['member_id'] ."'><button style='background-color: orange; color: black;'>Reject join request and ban</button></a></td>";
                } elseif($row['group_member_status'] === 'member' ){
                    echo "<td><a href='COSN_group_ban_member.php?group_id=" . $group_id . "&member_id=". $row['member_id'] ."'><button style='text-align: center; vertical-align: middle; background-color: red; color: black;'>Ban member</button></a></td>";
                } elseif($row['group_member_status'] === 'ban'){
                    echo "<td style='background-color: gray; color: white;'>Member is already banned</td>";
                } elseif($row['group_member_status'] === 'admin' || $row['member_id'] === 1){
                    echo "<td style='background-color: gray; color: white;'>Cannot ban admin or owner!</td>";
                }

                echo "</tr>";
                next($result);
                
            }
        } else {
            echo "<tr><td colspan='4'>No groups found</td></tr>";
        }

        ?>

</div>
</html>