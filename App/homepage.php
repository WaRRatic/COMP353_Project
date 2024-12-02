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



// Query to get public content and private content that the logged in user has access to
$sql = "
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
        moderation_status = 'approved'
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
        moderation_status = 'approved' AND
        cmp.authorized_member_id = :logged_in_member_id
    ORDER BY content_id, content_feed_type
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':logged_in_member_id' => $logged_in_member_id]);

// Fetch all rows as associative arrays
$public_content = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process each row to build the nested structure
foreach ($public_content as $row) {
    $content_id = $row['content_id'];
    
    // If the post doesn't exist in the $posts array, add it
    if (!isset($posts[$content_id])) {
        $posts[$content_id] = [
            'content_id' => $row['content_id'],
            'username' => $row['username'],
            'content_type' => $row['content_type'],
            'content_data' => $row['content_data'],
            'content_creation_date' => $row['content_creation_date'],
            'content_title' => $row['content_title'],
            'moderation_status' => $row['moderation_status'],
            'content_feed_type' => $row['content_feed_type'],
            'permissions' => [] // Initialize the permissions array
        ];
    }
    
    // Add the permission to the post's permissions array
    $posts[$content_id]['permissions'][] = [
        'content_permission_type' => $row['content_permission_type']
        // Add more permission-related fields here if necessary
    ];
}

// Optional: Reindex the $posts array to have sequential keys
$posts = array_values($posts);

// Function to extract YouTube video ID
function getYoutubeVideoId($url) {
    $video_id = '';
    // youtube.com/watch?v=VIDEO_ID format
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $matches)) {
        $video_id = $matches[1];
    }
    // youtu.be/VIDEO_ID format
    else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $matches)) {
        $video_id = $matches[1];
    }
    return $video_id;
}
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

<div id="main_feed">
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <?php
                // Extract permission types into an array
                $permission_types = array_column($post['permissions'], 'content_permission_type');
                // Create a space-separated string of permission types
                $permission_types_string = implode(' ', array_map('strtolower', $permission_types));
            ?>
            <div class="feed-item <?php echo htmlspecialchars($post['content_feed_type']); ?>" 
                 data-feed-type="<?php echo htmlspecialchars($post['content_feed_type']); ?>"
                 data-permission-type="<?php echo htmlspecialchars($permission_types_string); ?>">
                
                <h3><?php echo htmlspecialchars($post['content_title']); ?></h3>
                
                <?php
                switch($post['content_type']) {
                    case 'image':
                        echo '<img src="' . htmlspecialchars($post['content_data']) . '" alt="' . htmlspecialchars($post['content_title']) . '" class="content-image">';
                        break;
                    case 'video':
                        if (strpos($post['content_data'], 'youtube.com') !== false || strpos($post['content_data'], 'youtu.be') !== false) {
                            $video_id = getYoutubeVideoId($post['content_data']);
                            if ($video_id) {
                                echo '<iframe width="100%" height="315" 
                                        src="https://www.youtube.com/embed/' . htmlspecialchars($video_id) . '" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen></iframe>';
                            } else {
                                echo '<p>Invalid YouTube URL</p>';
                            }
                        } else {
                            echo '<video controls width="100%">
                                    <source src="' . htmlspecialchars($post['content_data']) . '" type="video/mp4">
                                    Your browser does not support the video tag.
                                  </video>';
                        }
                        break;
                    case 'text':
                        echo '<p>' . nl2br(htmlspecialchars($post['content_data'])) . '</p>';
                        break;
                    default:
                        echo '<p>Unsupported content type: ' . htmlspecialchars($post['content_type']) . '</p>';
                }
                ?>
                <br><hl>
                <small>
                    Posted on <?php echo htmlspecialchars($post['content_creation_date']); ?> 
                    by User <?php echo htmlspecialchars($post['username']); ?>
                    (Content is: <?php echo htmlspecialchars($post['content_feed_type']); ?>)
                </small>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="edit_content.php?content_id=<?php echo urlencode($post['content_id']); ?>" class="action-button edit-button">Edit</a>
                    <a href="Content_Interact.php?state=share&content_id=<?php echo urlencode($post['content_id']); ?>" class="action-button share-button">Share</a>
                    <a href="Content_Interact.php?state=comment&content_id=<?php echo urlencode($post['content_id']); ?>" class="action-button comment-button">Comment</a>
                    <a href="Content_Interact.php?state=link&content_id=<?php echo urlencode($post['content_id']); ?>" class="action-button link-button">Link</a>
                </div>
                <div class="view-post-button">
                    <a href="view_content.php?content_id=<?php echo urlencode($post['content_id']); ?>" class="view-post-button">View Post</a>
                </div>
            </div>
            <br><br> <!-- Added double line break for spacing -->
        <?php endforeach; ?>
    <?php else: ?>
        <p>No content available to display.</p>
    <?php endif; ?>
</div>


<footer>
    <a href="index.php" class="logout-button">Logout</a>
</footer>

</body>
</html>
