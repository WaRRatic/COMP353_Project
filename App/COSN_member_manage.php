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
if (!isset($_GET['member_id'])) {
    echo "<script>alert('No Member_ID is specified!');</script>";
    echo "<script>window.location.href = 'COSN_members.php';</script>";
    exit;
}

$isAdmin = false;
// Check if the user is an admin
if ($_SESSION['privilege_level'] === 'administrator'){
    $isAdmin = true;
}

// Set member_id variable for both fetching and updating
$member_id = $_GET['member_id'];

// Check if the user is the account owner
$isAccountOwner = ($member_id == $_SESSION['member_id']) ? true : false;
// if the user is not an admin and is not the account owner, then notify them that can only manage their own account and redirect to their own modify account page
if(!$isAdmin){
    if(!$isAccountOwner){
        echo "<script>alert('You can only manage your own account!');</script>";
        echo "<script>window.location.href = 'COSN_member_manage.php?member_id=" . $_SESSION['member_id'] . "';</script>";
        exit;
    }
}



// Get the member public permissions from the database
$sql = "SELECT 
            personal_info_type
        FROM 
            kpc353_2.personal_info_public_permissions
        WHERE 
            owner_member_id = :member_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':member_id' => $member_id]);
$public_permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the member private permissions from the database
$sql = "SELECT 
            personal_info_type, authorized_member_id, m.username
        FROM 
            kpc353_2.personal_info_permissions as pim
                LEFT JOIN kpc353_2.members as m
                    ON pim.authorized_member_id = m.member_id
        WHERE 
            owner_member_id = :member_id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':member_id' => $member_id]);
$private_permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

//get COSN users to which a permission can apply
$sql = "SELECT 
            m.username, m.member_id as target_member_id
        FROM  kpc353_2.members as m";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$members_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the member personal data from the database
$sql = "
    SELECT 
        member_id,username,password,email,first_name,last_name,address,date_of_birth,privilege_level,status 
    FROM 
        kpc353_2.members
    WHERE 
        member_id = :member_id
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':member_id' => $member_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

//load the variables which will be used to populate the form
$member_id = $result[0]['member_id'];
$username = $result[0]['username'];
$password = $result[0]['password'];
$email = $result[0]['email'];
$first_name = $result[0]['first_name'];
$last_name = $result[0]['last_name'];
$address = $result[0]['address'];
$dob = $result[0]['date_of_birth'];
$privilege_level = $result[0]['privilege_level'];
$cosn_status = $result[0]['status'];

// If the form is submitted, update the group's data
if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    
    //check if the delete button is clicked and the user is an admin
    if (isset($_POST['delete_member']) && $isAdmin === true) {
        try {
            $sql_delete = '
            DELETE FROM
                kpc353_2.members 
            WHERE
                member_id = :member_id';

            $deleteStmt = $pdo->prepare($sql_delete);
            $deleteStmt->execute(['member_id' => $member_id]);

            echo "<script>alert('Member deleted successfully!');";
            echo "window.location.href = 'COSN_members.php';</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Error deleting member: " . addslashes($e->getMessage()) . "');</script>";
            exit;
        }
    // update the variables from the form, when the "Update member" button is click and a POST request is sent
    }elseif(isset($_POST['update_member'])){
        $original_email = $email;
        $original_username = $username;
        
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $address = $_POST['address'];
        $dob = $_POST['dob'];
        //if the user is an admin, then the privilege level and status can be changed, otherwise it will remain the same
        $privilege_level = ($isAdmin) ? $privilege_level : $_POST['privilege_level'];
        $cosn_status = ($isAdmin) ? $cosn_status : $_POST['status'];


        if ($email !== $original_email) {
            // Check if email already exists
            $stmt = $pdo->prepare('SELECT * FROM members WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $emailExists = $stmt->fetch();
            if ($emailExists) {
                echo "<script>alert('Email is already in use by another COSN member. Please use a different email address.');</script>";
                echo "<script>window.location.href = 'COSN_member_manage.php?member_id=" . $member_id . "';</script>";
                exit;
            } 
        }

        if ($username !== $original_username) {
            // Check if username already exists
            $stmt = $pdo->prepare('SELECT * FROM members WHERE username = :new_username');
            $stmt->execute(['new_username' => $original_username]);
            $usernameExists = $stmt->fetch();
            if ($usernameExists) {
                echo "<script>alert('Username is already in use by another COSN member. Please use a different username.');</script>";
                echo "<script>window.location.href = 'COSN_member_manage.php?member_id=" . $member_id . "';</script>";
                exit;
            } 
        }

        try{
            // Fetch the selected group data from the database
            $sql = "
            UPDATE 
                kpc353_2.members
            SET
                username = :username,
                password = :password,
                email = :email,
                first_name = :first_name,
                last_name = :last_name,
                address = :address,
                date_of_birth = :dob,
                privilege_level = :privilege_level,
                status = :status
            WHERE 
                member_id = :member_id
            ";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([':member_id' => $member_id, ':username' => $username, ':password' => $password, ':email' => $email, ':first_name' => $first_name, ':last_name' => $last_name, ':address' => $address, ':dob' => $dob, ':privilege_level' => $privilege_level, ':status' => $cosn_status]);

            echo "<script>alert('Member updated successfully!');</script>";
            "<script>window.location.href = 'edit_COSN_group.php?member_id=" . $member_id . "';</script>";
        } catch (Exception $e) {
            // Output an alert and use JavaScript for redirection
            echo "<script>alert('Error updating the group! Check your datatypes and try again: " . addslashes($e->getMessage()) . "');</script>";
            echo "<script>window.location.href = 'edit_COSN_group.php?member_id=" . $member_id . "&error=" . urlencode($e->getMessage()) . "';</script>";
            exit;
        }
    }elseif(isset($_POST['add_public_permission'])){
        // update the variables from the form
        $content_permission_type = $_POST['permission_type'];

        echo "<script>window.location.href = 'COSN_member_personal_info_permission_change.php?member_id=" . $member_id . "&level=public&type=" . $content_permission_type ."&action=add';</script>";
    }elseif(isset($_POST['add_private_permission'])){
        // update the variables from the form
        $content_permission_type = $_POST['permission_type'];
        $target_member_id = $_POST['target_member_id'];

        echo "<script>window.location.href = 'COSN_member_personal_info_permission_change.php?member_id=" . $member_id . "&level=private&type=" . $content_permission_type ."&target_member_id=" . $target_member_id . "&action=add';</script>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="COSN_member_manage.css" />
<head>
    <meta charset="UTF-8">
    <title>Manage COSN member</title>
</head>
<body>
<div class="main-content">
    <h1>Manage COSN member</h1>

    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo $username; ?>" required><br>
        
        <label for="password">Password:</label>
        <input type="text" id="password" name="password" value="<?php echo $password; ?>" required><br>

        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="<?php echo $email; ?>" required><br>

        <label for="first_name">First name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" ><br>

        <label for="last_name">Last name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" ><br>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo $address; ?>" ><br>

        <label for="dob">Date of birth:</label>
        <input type="text" id="dob" name="dob" value="<?php echo $dob; ?>" ><br>

        <label for="privilege_level" <?php echo ($isAdmin) ? : 'class="hidden"' ?>>Privilege level:</label>
        <select id="privilege_level" name="privilege_level" <?php echo ($isAdmin) ? : 'class="hidden"' ?> required>
            <option value="administrator" <?php echo $privilege_level === 'administrator' ? 'selected' : ''; ?>>COSN Administrator</option>
            <option value="senior" <?php echo $privilege_level === 'senior' ? 'selected' : ''; ?>>Senior</option>
            <option value="junior" <?php echo $privilege_level === 'junior' ? 'selected' : ''; ?>>Junior</option>
        </select><br>

        <label for="status" <?php echo ($isAdmin) ? : 'class="hidden"' ?> >COSN status:</label>
        <select id="status" name="status" <?php echo ($isAdmin) ? : 'class="hidden"' ?> required>
            <option value="Active" <?php echo $cosn_status === 'active' ? 'selected' : ''; ?>>Active</option>
            <option value="Inactive" <?php echo $cosn_status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            <option value="Suspended" <?php echo $cosn_status === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
        </select><br>


        <br>
        <br>
        <br>

        <button type="submit" name="update_member" style="background-color: green; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Update member</button>
        <button type="submit" name="delete_member" <?php echo ($isAdmin) ? : 'class="hidden"' ?> onclick="return confirm('Are you sure you want to delete this COSN member? ');" style="background-color: #ff4444; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">    Delete COSN Member
        </button>
    </form>

    <br><br>
    <hr>
    <h2>Manage permissions on member's personal information</h3>
    <?php
        if(empty($public_permissions)&& empty($private_permissions)){
            echo "<h1>There are no permissions set -- all the personal information is private (except for the admin who can see everything) !</h1>";
        }
        elseif(!empty($public_permissions)){
            echo "<br><br>";
            echo "<h2>Public permissions</h2>";
            echo "<table border='1'>";
            echo "<tr>";
            echo "<th>Personal information visible</th>";
            echo "<th>Permission explanation</th>";
            echo "</tr>";
            // Output data of each row
            while($row = current($public_permissions)) {
                //start row
                echo "<tr>";
                echo "<td>" . $row['personal_info_type'] . "</td>";
                echo "<td> public can see \"". $row['personal_info_type'] . "\" of " .$username . " </td>";
                
                echo "<td><a href='COSN_member_personal_info_permission_change.php?member_id=" . $member_id . "&level=public&type=" . $row['personal_info_type'] ."&action=remove'><button>Remove permission</button></a></td>";

                echo "</tr>";
                next($public_permissions);
            }
            echo "</table>";
            echo "<br><br><hr>";
        }
        ?>
        <h3>Add new public permission</h3>
        <form method="POST">
            <label for="permission_type">Personal info permission:</label>
            <select id="permission_type" name="permission_type" required>
                <option value="first_name">First Name</option>    
                <option value="last_name">Last Name</option>
                <option value="dob">Date of Birth</option>
                <option value="email">Email</option>
                <option value="address">Address</option>
            </select>

            <br><br>
            <button type="submit" name="add_public_permission" style="background-color: green; color: black; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            Add public permission
            </button>
        </form>
        
        <?php
        if(!empty($private_permissions)){
            echo "<br><br>";
            echo "<h3>Private permissions</h3>";
            echo "<table border='1'>";
            echo "<tr>";
            echo "<th>Personal information visible</th>";
            echo "<th>Authorized member</th>";
            echo "<th>Permission explanation</th>";
            echo "</tr>";
            // Output data of each row
            while($row = current($private_permissions)) {
                //start row
                echo "<tr>";
                echo "<td>" . $row['personal_info_type'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td> ".$row['username'] ." is authorized to see \"". $row['personal_info_type'] . "\" of " . $username . " </td>";
                echo "<td><a href='COSN_member_personal_info_permission_change.php?member_id=" . $member_id . "&level=private&type=" . $row['personal_info_type'] ."&target_member_id=" . $row['authorized_member_id'] . "&action=remove'><button>Remove permission</button></a></td>";
                echo "</tr>";
                next($private_permissions);
            }
            echo "</table>";
            echo "<br><br><hr>";
        }
    ?>
        <h3>Add new authorized member permission</h3>
        <form method="POST">
            <label for="permission_type">Personal info permission:</label>
            <select id="permission_type" name="permission_type" required>
                <option value="first_name">First Name</option>    
                <option value="last_name">Last Name</option>
                <option value="date_of_birth">Date of Birth</option>
                <option value="email">Email</option>
                <option value="address">Address</option>
            </select>

            <label for="target_member_id">Member authorized:</label>
            <select id="target_member_id" name="target_member_id" required>
                <?php foreach($members_list as $member): ?>
                    <option value= <?php echo $member['target_member_id']; ?> > <?php echo $member['username']; ?> </option>
                <?php endforeach; ?>
            </select>

            <br><br>
            <button type="submit" name="add_private_permission" style="background-color: green; color: black; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            Add authorized member permission
            </button>
        </form>


</div>
</html>