<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/homepage.css" />
<link rel="stylesheet" type = "text/css" href="./css/content_feed.css" />
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
</head>



<?php
session_start();

//set the logged in member id
$logged_in_member_id = $_SESSION['member_id'];


// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit;
}


// Database connection parameters
$host = 'localhost';
$db   = 'cosn';
$user = 'root';
$pass = '';


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



// Query to get public content
$sql = "
SELECT
    content_id, m.username, content_type, content_data, content_creation_date, content_title, moderation_status, cpp.content_public_permission_type, 'public' as content_feed_type
FROM
    cosn.content as cont
INNER JOIN cosn.content_public_permissions as cpp
    ON cont.content_id = cpp.target_content_id
INNER JOIN cosn.members as m
    ON cont.creator_id = m.member_id
WHERE 
moderation_status = 'approved'
UNION
SELECT
    content_id, m.username, content_type, content_data, content_creation_date, content_title, moderation_status, cmp.content_permission_type, 'private' as content_feed_type
FROM
    cosn.content as cont
INNER JOIN cosn.content_member_permission as cmp
    ON cont.content_id = cmp.target_content_id
INNER JOIN cosn.members as m
    ON cont.creator_id = m.member_id
WHERE
moderation_status = 'approved' AND
cmp.authorized_member_id = :logged_in_member_id;
";


$stmt = $pdo->prepare($sql);
$stmt->execute([':logged_in_member_id' => $logged_in_member_id]);

$public_content = $stmt->fetchAll();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
</head>
<body>
<h1>Welcome to <?php echo $_SESSION['member_username']; ?> homepage!</h1>
    <small>You are now logged in.</small>
    <h2>Activities</h2>
        <ul>
            <li><a href="create_content_and_set_permissions.php">Post content to COSN</a></li>
        </ul>


        
<!-- Display this element only if the role is "admin" -->
<?php if ($_SESSION['privilege_level'] === 'administrator'): ?>
    <div style="border: 1px solid black; padding: 10px; margin: 10px;">
        <h2>Admin Panel</h2>
        <p>This section is only visible to admin users.</p>
        <ul>
            <li><a href="admin_manage_users.php">Manage COSN users</a></li>
            <li><a href="admin_manage_groups.php">Manage COSN groups</a></li>
            <li><a href="admin_post_public.php">Make a public post</a></li>
        </ul>
    </div>
<?php endif; ?>
    
<!-- Display public feed -->
<h2><?php echo $_SESSION['member_username']; ?>'s Content Feed</h2>
<?php foreach ($public_content as $content): ?>
    <div class="<?php echo $content['content_feed_type'] === 'private' ? 'private-feed-item' : 'public-feed-item'; ?>" 
         data-permission-type="<?php echo htmlspecialchars($content['content_public_permission_type']); ?>"
         data-feed-type="<?php echo htmlspecialchars($content['content_feed_type']); ?>">
        <h3><?php echo ($content['content_title']); ?></h3>
        <p><?php echo nl2br(($content['content_data'])); ?></p>
        <small>Posted on <?php echo ($content['content_creation_date']); ?> by User <?php echo ($content['username']);?> (content is: <?php echo ($content['content_feed_type']);?> )</small>
    </div>
    <br><br> <!-- Added double line break for spacing -->
<?php endforeach; ?>

    <a href="index.php">Logout</a>
</body>
</html>
