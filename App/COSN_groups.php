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

// Prepare the SQL statement
$sql = "
    SELECT 
    group_id, group_name, owner_id, description, creation_date,
    COALESCE((CASE WHEN (1=:logged_in_member_id) || (gm.group_member_status = 'owner') THEN 'admin' ELSE gm.group_member_status END),'outsider') AS group_role
    FROM kpc353_2.groups as g
        LEFT JOIN 
            kpc353_2.group_members as gm 
            ON g.group_id = gm.joined_group_id
                AND gm.participant_member_id = :logged_in_member_id
        LEFT JOIN 
            kpc353_2.members as m 
                ON m.member_id = :logged_in_member_id
    ";

$stmt = $pdo->prepare($sql);
$stmt->execute([':logged_in_member_id' => $logged_in_member_id]);

// Fetch all rows
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

//check if the Admin column is needed to print in the table, based on the user's role in the groups 
$result_col_needed_check = $result;
$adminColumn_needed = false;
while($row = current($result_col_needed_check)) {
    // Check for 'admin' role
    if (!$adminColumn_needed && $row['group_role'] === 'admin') {
        $adminColumn_needed = true;
    }

    next($result_col_needed_check);
}
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="COSN_groups.css" />
<head>
    <meta charset="UTF-8">
    <title>COSN groups</title>
</head>

<body>
  <div class="main-content">
    <h1>COSN groups</h1>
    <br>
    <a href="COSN_create_group.php"><button style='background-color: yellow; color: black;'>Create a new group</button></a>
    <br><br>
    <table border="1">
        <tr>
            <th>Group Name</th>
            <th>Description</th>
            <th>Your status</th>
            <th>Access group</th>

            <!-- only show the admin column, if the user has Admin privelege to at least one group -->
            <?php if ($adminColumn_needed) { ?>
                <th>Admin page</th>
            <?php } ?>


        </tr>
        
        <?php
        // Check if there are any results
        if ($row = $result) {
            // Output data of each row
            while($row = current($result)) {
                //start row
                echo "<tr>";
                
                //start columns
                echo "<td>" . $row['group_name'] . "</td>";
                echo "<td>" . $row['description'] ."</td>";
                echo "<td>" . $row['group_role'] . "</td>";

                //Access group column
                if($row['group_role'] === 'outsider'){
                    echo "<td><a href='COSN_group_request_access.php?group_id=" . $row['group_id'] . "'><button style='background-color: gray; color: white;'> Request Access</button></a></td>";
                } elseif($row['group_role'] === 'requested'){
                    echo "<td> waiting for access </td>";
                } elseif($row['group_role'] === 'member' || $row['group_role'] === 'admin'){
                    echo "<td><a href='homepage.php?group_id=" . $row['group_id'] . "'><button style='background-color: green; color: black;'>Access group</button></a></td>";
                } elseif($row['group_role'] === 'ousted'){
                    echo "<td style='background-color: red; color: black;'>You've been banned!</td>";
                }

                //Admin page column
                if($row['group_role'] === 'admin'){
                    echo "<td><a href='COSN_group_admin.php?group_id=" . $row['group_id'] . "'><button> Admin page</button></a></td>";
                } else {
                    echo "<td style='background-color: black; color: yellow;'>Not an admin</td>";
                }

                //end row
                echo "</tr>";
                //iterate to next result in the $result array
                next($result);
                
            }
        } else {
            echo "<tr><td colspan='4'>No groups found</td></tr>";
        }

        ?>
    </table>
    <br>
        <br>
        <br>

    </div>
</body>
</html>