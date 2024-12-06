<?php
include("db_config.php");
include("header.php");
include('sidebar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/edit_content.css" />
<head>
    <meta charset="UTF-8">
    <title>Admin manage users</title>
</head>
<?php
session_start();

// Check if the user is logged in via Sessuin
if (!isset($_SESSION['loggedin'])) {
    // Redirect to homepage if not authorized
    echo "<script>alert('Log in first!');</script>";
    header("Location: index.php"); 
    exit;
}

// Check if the member_ID was passed in the URL
if (!isset($_SESSION['member_id'])) {
    echo "<script>alert('No member_ID is specified!');</script>";
    header("Location: homepage.php"); 
    exit;
}

// Check if the Content_ID was passed in the URL
if (!isset($_GET['content_id'])) {
    echo "<script>alert('No content_ID is specified!');</script>";
    header("Location: homepage.php"); 
    exit;
}

$member_id = $_SESSION['member_id'];
$content_id = $_GET['content_id'];


// Set up DSN and options
$dsn = "mysql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// Create a PDO instance
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     exit('Database connection failed: ' . $e->getMessage());
}

// Query to get if the member has Edit permission in public or private table for the content
$sql = $pdo->prepare("
    SELECT
        content_id, m.username, content_type, content_data, content_creation_date, content_title, moderation_status, 
        cpp.content_public_permission_type AS content_permission_type, 'public' as content_feed_type
    FROM
        cosn.content as cont
    INNER JOIN cosn.content_public_permissions as cpp
        ON cont.content_id = cpp.target_content_id
    INNER JOIN cosn.members as m
        ON cont.creator_id = m.member_id
    WHERE 
        content_id = :content_id AND
        content_deleted_flag <> true AND
        cpp.content_public_permission_type = 'edit'
    UNION
    SELECT
        content_id, m.username, content_type, content_data, content_creation_date, content_title, moderation_status, 
        cmp.content_permission_type AS content_permission_type, 'private' as content_feed_type
    FROM
        cosn.content as cont
    INNER JOIN cosn.content_member_permission as cmp
        ON cont.content_id = cmp.target_content_id
    INNER JOIN cosn.members as m
        ON cont.creator_id = m.member_id
    WHERE
        content_id = :content_id AND
        cmp.authorized_member_id = :logged_in_member_id AND
        content_deleted_flag <> true AND
        cmp.content_permission_type = 'edit'
    ORDER BY content_id, content_feed_type
    ");

$sql->execute(['logged_in_member_id' => $member_id, 'content_id'=>$content_id]);
$editPermissionExists = $sql->fetch();

// Check if the member has Edit privilege on the content
if (!$editPermissionExists) {
    echo "<script>alert('You don't have permission to edit this content');</script>";
    header("Location: homepage.php"); 
    exit;
}

// If the form is submitted, update the content in the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // update the variables from the form, when the "Update Member" button is click and a POST request is sent
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

// Delete content
if (isset($_POST['delete_content'])) {
    try {
        $pdo2 = new PDO($dsn, $user, $pass, $options);
        $pdo2->beginTransaction();

        $deleteStmt = $pdo2->prepare('UPDATE content SET content_deleted_flag = true WHERE content_id = :content_id');
        $deleteStmt->execute(['content_id' => $content_id]);

        $pdo2->commit();
        echo "<script>alert('Content deleted successfully!');";
        echo "window.location.href = 'homepage.php';</script>";
        exit;
    } catch (Exception $e) {
        $pdo2->rollback();
        echo "<script>alert('Error deleting content: " . addslashes($e->getMessage()) . "');</script>";
    }
}


// Create a PDO instance
try {
    $pdo2 = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}

$sqlContent = $pdo2->prepare('SELECT content_type, content_title, content_data FROM content WHERE content_id = :content_id');
$sqlContent->execute(['content_id' => $content_id]);
$contentDetails = $sqlContent->fetch(PDO::FETCH_ASSOC);

$content_type = $contentDetails['content_type'];
$content_title = $contentDetails['content_title'];
$content_data = $contentDetails['content_data'];




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Content</title>
</head>
<body>
<div class="main-content">
    <h1>Edit Content</h1>
    <p>This section is only visible to users that have Edit permission to this content</p>
    <form method="POST">
        <label for="content_type">Content Type:</label>
        <input type="text" id="content_type" name="content_type" value="<?php echo $content_type; ?>" required><br>
        
        <label for="content_title">Content Title:</label>
        <input type="text" id="content_title" name="content_title" value="<?php echo $content_title; ?>" required><br>
        
        <label for="content_data">Content Data:</label>
        <input type="text" id="content_data" name="content_data" value="<?php echo $content_data; ?>" required><br>

        <button type="submit">Update Content</button>
        <button type="submit" name="delete_content" onclick="return confirm('Are you sure you want to delete this content? This action cannot be undone.');" style="background-color: #ff4444; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
    Delete Content
</button>
    </form>

    
    <br><br>
    <hr>
</div>
</html>