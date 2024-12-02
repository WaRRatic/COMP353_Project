<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/view_content.css" />
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

// Query to get if the member has Read permission in public or private table for the content
//It is assumed that the member has Read permission, if he has any kind of permission like (comment, share, link, etc)
//The comment needs to have passed moderation in order to be viewed (this way the member can't View unmoderated content, even if they know the Content_ID, are logged in and have permission to view it)
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
        cpp.content_public_permission_type in ('read','comment','share','link') AND
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
        content_id = :content_id AND
        cmp.authorized_member_id = :logged_in_member_id AND
        cmp.content_permission_type in ('read','edit','comment','share','modify-permission','moderate','link') AND
        moderation_status = 'approved'
    ORDER BY content_id, content_feed_type
    ");

$sql->execute(['logged_in_member_id' => $member_id, 'content_id'=>$content_id]);
$readPermissionExists = $sql->fetch();

// Check if the member has Edit privilege on the content
if (!$readPermissionExists) {
    echo "<script>alert('You don't have permission to view this content or it was not moderated yet');</script>";
    header("Location: homepage.php"); 
    exit;
}

// Create a PDO instance to display
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
    <title>View Content</title>
</head>
<body>
    <h1>View Content</h1>
    <p>This section is only visible to users that have appropriate permission to this content</p>
    <small>This content has passed moderation</small>
    
    <div class="view-content-container">
        
        <div class="feed-item">
            <h3><?php echo htmlspecialchars($content_title); ?></h3>
            
            <?php
            switch($content_type) {
                case 'image':
                    echo '<img src="' . htmlspecialchars($content_data) . '" alt="' . htmlspecialchars($content_title) . '" class="content-image">';
                    break;
                    
                    case 'video':
                        if (strpos($content_data, 'youtube.com') !== false || strpos($content_data, 'youtu.be') !== false) {
                            $video_id = getYoutubeVideoId($content_data);
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
                                    <source src="' . htmlspecialchars($content_data) . '" type="video/mp4">
                                    Your browser does not support the video tag.
                                  </video>';
                        }
                    break;
                    
                case 'text':
                    echo '<p>' . nl2br(htmlspecialchars($content_data)) . '</p>';
                    break;
                    
                default:
                    echo '<p>Unsupported content type: ' . htmlspecialchars($content_type) . '</p>';
            }
            ?>
            
            <small>
                Content Type: <?php echo htmlspecialchars($content_type); ?>
            </small>
        </div>

    </div>
    
    <br><br>
    <hr>
<a href="Homepage.php">Back to Homepage</a>

</html>