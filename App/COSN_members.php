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

// Check if the user is an admin
if ($_SESSION['privilege_level'] === 'administrator'){
    $isAdmin = true;
}else{
    $isAdmin = false;
}

$logged_in_member_id = $_SESSION['member_id'];

// Prepare the SQL statement
if($isAdmin){
    //the admin can see all members (not only active)
    $sql = "
        SELECT 
            member_id,username,password,email,first_name,last_name,address,date_of_birth,privilege_level,status 
        FROM 
            kpc353_2.members
        ;";
}else{
    //non-admin user can see only active members
    $sql = "
        SELECT 
            member_id,username,password,email,first_name,last_name,address,date_of_birth,privilege_level,status 
        FROM 
            kpc353_2.members
        WHERE
            status = 'active'
        ;";
}

$stmt = $pdo->prepare($sql);
$stmt->execute();
// Fetch all rows
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="COSN_members.css" />
<head>
    <meta charset="UTF-8">
    <title>COSN members</title>
</head>

<body>
  <div class="main-content">
    <h1>COSN members</h1>
    <br>
    <?php
    // Display the admin panel if the user is an administrator
    if ($isAdmin) {
        echo "<div style='border: 2px solid teal; padding: 10px; margin-bottom: 20px; display: flex; flex-direction: column; align-items: center;'>"; // Admin panel container with border
        echo "<p style='font-weight: bold; text-align: center;'>COSN ADMIN PANEL</p>";
        echo "<div style='display: flex; justify-content: space-around; gap: 20px;'>"; // Horizontal button container
        echo "<a href='COSN_member_create.php'><button style='background-color: teal; color: black;'>Create COSN member</button></a>";
        echo "</div>";
        echo "</div>";
    }
    ?>
    <br><br>

    <table border="1">
        <tr>
            <th>Member ID</th>
            <th>Username</th>
            <th>Privilege level</th>
            <th>View profile</th>
            <!-- display the additional "Manage user" column if the user is an admin -->
            <?php if ($isAdmin){ echo "<th style='border: 2px solid teal;'>Member status</th>"; } ?>
            <?php if ($isAdmin){ echo "<th style='border: 2px solid teal;'>Manage member</th>"; } ?>
        </tr>
        
        <?php
        // Check if there are any results
        if ($row = $result) {
            // Output data of each row
            while($row = current($result)) {
                //start row
                echo "<tr>";

                //
                echo "<td>" . $row['member_id'] . "</td>";
                echo "<td>" . $row['username'] ."</td>";
                echo "<td>" . $row['privilege_level'] ."</td>";
                echo "<td><a href='homepage.php?member_id=" . $row['member_id'] . "'><button style='background-color: green; color: black;'>View member profile</button></a></td>";

                if($isAdmin){
                echo "<td style='border: 2px solid teal;'>". $row['status'] ."</td>";}
                if($isAdmin){
                echo "<td style='border: 2px solid teal;'><a href='COSN_member_manage.php?member_id=" . $row['member_id'] . "'><button style='background-color: teal; color: black;'>Manage member</button></a></td>";}
                //end row
                echo "</tr>";
                next($result);
                
            }
        } else {
            echo "<tr><td colspan='4'>No COSN members found</td></tr>";
        }

        ?>
    </table>
    <br>
        <br>
        <br>

    </div>
</body>
</html>