<?php
include("db_config.php");
include("header.php");
include('sidebar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/admin_moderate_specific_content.css" />
<head>
    <meta charset="UTF-8">
    <title>Manage & moderate specific content</title>
</head>
<?php
session_start();

// Check if the user is logged in and has the "admin" role
if (!isset($_SESSION['loggedin']) || $_SESSION['privilege_level'] !== 'administrator') {
    // Redirect to an error page or homepage if not authorized
    header("Location: homepage.php"); 
    exit;
}

// Check if the Content_ID was passed in the URL
if (!isset($_GET['content_id'])) {
    echo "<script>alert('No content_ID is specified!');</script>";
    header("Location: homepage.php"); 
    exit;
}
$content_id = $_GET['content_id'];


// Set up DSN and options
$dsn = "mysql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];


// If the form is submitted, update the content in the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Delete content
    if (isset($_POST['delete_content'])) {
        try {
            $pdo2 = new PDO($dsn, $user, $pass, $options);
            $pdo2->beginTransaction();

            $deleteStmt = $pdo2->prepare('UPDATE content SET content_deleted_flag = true WHERE content_id = :content_id');
            $deleteStmt->execute(['content_id' => $content_id]);

            $pdo2->commit();
            echo "<script>alert('Content deleted successfully!');";
            echo "window.location.href = 'admin_manage_content.php';</script>";
            exit;
        } catch (Exception $e) {
            $pdo2->rollback();
            echo "<script>alert('Error deleting content: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
    
    // Approve content
    elseif (isset($_POST['approve_content'])) {
        try {
            $pdo2 = new PDO($dsn, $user, $pass, $options);
            $pdo2->beginTransaction();

            $deleteStmt = $pdo2->prepare('UPDATE content SET moderation_status = "approved" WHERE content_id = :content_id');
            $deleteStmt->execute(['content_id' => $content_id]);

            $pdo2->commit();
            echo "<script>alert('Content approved successfully!');";
            echo "window.location.href = 'admin_manage_content.php';</script>";
            exit;
        } catch (Exception $e) {
            $pdo2->rollback();
            echo "<script>alert('Error approving content: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
    // Reject content
    elseif (isset($_POST['reject_content'])) {
        try {
            $pdo2 = new PDO($dsn, $user, $pass, $options);
            $pdo2->beginTransaction();

            $deleteStmt = $pdo2->prepare('UPDATE content SET moderation_status = "rejected" WHERE content_id = :content_id');
            $deleteStmt->execute(['content_id' => $content_id]);

            $pdo2->commit();
            echo "<script>alert('Content rejected successfully!');";
            echo "window.location.href = 'admin_manage_content.php';</script>";
            exit;
        } catch (Exception $e) {
            $pdo2->rollback();
            echo "<script>alert('Error rejected content: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
    else{    // update the variables from the form, when the "Update Member" button is click and a POST request is sent
        $content_type = $_POST['content_type'];
        $content_title = $_POST['content_title'];
        $content_data = $_POST['content_data'];

        // Create a PDO instance
        try {
            $pdo2 = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            exit('Database connection failed: ' . $e->getMessage());
        }

        // Begin transaction
        $pdo2->beginTransaction();

        // Insert into content table
        $stmt = $pdo2->prepare('
            UPDATE content SET content_type = :content_type, content_data = :content_data, content_title = :content_title WHERE content_id = :content_id
        ');

        try{
            $stmt->execute([
                'content_type'  => $content_type,
                'content_data'  => $content_data,
                'content_title' => $content_title,
                'content_id' => $content_id
            ]);

            //if the update goes through without error, commit the transaction therefore saving the data
            $pdo2->commit(); 
            echo "<script>alert('Content updated successfully!');</script>";
        
        } catch (Exception $e) {
            // Rollback in case of error
            $pdo2->rollback();

            // Output an alert and use JavaScript for redirection
            echo "<script>alert('Error updating the member! Check your datatypes and try again: " . addslashes($e->getMessage()) . "');</script>";
            echo "<script>window.location.href = 'edit_content.php?content_id=" . $content_id . "&error=" . urlencode($e->getMessage()) . "';</script>";

            exit;
            }
    }
}





// Create a PDO instance
try {
    $pdo2 = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}

$sqlContent = $pdo2->prepare('SELECT content_type, content_title, content_data,moderation_status FROM content WHERE content_id = :content_id');
$sqlContent->execute(['content_id' => $content_id]);
$contentDetails = $sqlContent->fetch(PDO::FETCH_ASSOC);

$content_type = $contentDetails['content_type'];
$content_title = $contentDetails['content_title'];
$content_data = $contentDetails['content_data'];
$content_moderation = $contentDetails['moderation_status'];




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage & Moderate Content</title>
</head>
<body>
<div class="main-content">
    <h1>Manage & Moderate Content</h1>
    <form method="POST">
        <label for="content_type">Content Type:</label>
        <input type="text" id="content_type" name="content_type" value="<?php echo $content_type; ?>" required><br>
        
        <label for="content_title">Content Title:</label>
        <input type="text" id="content_title" name="content_title" value="<?php echo $content_title; ?>" ><br>
        
        <label for="content_data">Content Data:</label>
        <input type="text" id="content_data" name="content_data" value="<?php echo $content_data; ?>" ><br>

        <label for="moderation_status">Moderation status:</label>
        <input type="text" id="moderation_status" name="moderation_status" value="<?php echo $content_moderation; ?>" required><br>

        <button type="submit">Update Content</button>
        <button type="submit" name="delete_content" onclick="return confirm('Are you sure you want to delete this content? This action cannot be undone.');" style="background-color: #ff4444; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
    Delete Content
</button>

    <button type="submit" name="approve_content" onclick="return confirm('Are you sure you want to approve (moderate) this content? ');" style="background-color: #00ff00; color: black; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
    Approve Content
    </button>
    <button type="submit" name="reject_content" onclick="return confirm('Are you sure you want to reject (moderate) this content? ');" style="background-color: #FFFF00; color: black; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
    Reject Content
    </button>
    </form>

    
    <br><br>
    <hr>
</div>
</html>