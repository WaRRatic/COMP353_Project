<?php
session_start();
include("db_config.php");
include("header.php");
include('sidebar.php');
include('getYoutubeVideoId_function.php');

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}


// Check if the Content_ID was passed in the URL
if (!isset($_GET['content_id'])) {
    echo "<script>alert('No content_id specified!');</script>";
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
$content_id = $_GET['content_id'];

// Query to get if the member has Read permission in public or private table for the content
//It is assumed that the member has Read permission, if he has any kind of permission like (comment, share, link, etc)
//The comment needs to have passed moderation in order to be viewed (this way the member can't View unmoderated content, even if they know the Content_ID, are logged in and have permission to view it)
$sql = $pdo->prepare("
    SELECT
        content_id, m.username, content_type, content_data, content_creation_date, content_title, moderation_status, 
        cpp.content_public_permission_type AS content_permission_type, 'public' as content_feed_type, NULL as post_group_name
    FROM
        kpc353_2.content as cont
    INNER JOIN kpc353_2.content_public_permissions as cpp
        ON cont.content_id = cpp.target_content_id
    INNER JOIN kpc353_2.members as m
        ON cont.creator_id = m.member_id
        AND content_id = :content_id 
    WHERE 
        moderation_status = 'approved'
    UNION
    SELECT
        content_id, m.username, content_type, content_data, content_creation_date, content_title, moderation_status, 
        cmp.content_permission_type AS content_permission_type, 'private' as content_feed_type, NULL as post_group_name
    FROM
        kpc353_2.content as cont
    INNER JOIN kpc353_2.content_member_permission as cmp
        ON cont.content_id = cmp.target_content_id
    INNER JOIN kpc353_2.members as m
        ON cont.creator_id = m.member_id
    WHERE
        moderation_status = 'approved' AND
        cmp.authorized_member_id = :logged_in_member_id
        AND content_id = :content_id 
    UNION
    SELECT
        content_id, m.username, content_type, content_data, content_creation_date, content_title, moderation_status, 
        cgp.content_group_permission_type AS content_permission_type, 'group' as content_feed_type, g.group_name as post_group_name
    FROM
        kpc353_2.content as cont
    INNER JOIN kpc353_2.content_group_permissions as cgp
        ON cont.content_id = cgp.target_content_id
    INNER JOIN kpc353_2.groups as g
        ON g.group_id = cgp.target_group_id
    INNER JOIN kpc353_2.group_members as gm
        on gm.joined_group_id = g.group_id
    INNER JOIN kpc353_2.members as m
        ON m.member_id = gm.participant_member_id
    WHERE 
        moderation_status = 'approved'
        AND m.member_id = :logged_in_member_id
        AND content_id = :content_id 
    ORDER BY content_creation_date desc
    ");

$sql->execute(['logged_in_member_id' => $logged_in_member_id, 'content_id'=>$content_id]);
$contentPermissions = $sql->fetchAll(PDO::FETCH_ASSOC);

// Check if the member has any kind of permission on the content or if the content has been moderated or if the content exists
if (!$contentPermissions) {
    echo "<script>alert('You don't have permission to view this content or it was not moderated yet or it doesn't exist');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}


$content_comment_permission = false;
$content_edit_permission = false;
$content_share_permission = false;
$content_link_permission = false;
while($row = current($contentPermissions)) {
    // Check for comment permission or allow it if the user is an admin
    if ((!$content_comment_permission && $row['content_permission_type'] === 'comment') || $isAdmin) {
        $content_comment_permission = true;
    }
    // Check for edit permission or allow it if the user is an admin
    if ((!$content_edit_permission && $row['content_permission_type'] === 'edit') || $isAdmin) {
        $content_edit_permission = true;
    }
    // Check for share permission or allow it if the user is an admin
    if ((!$content_share_permission && $row['content_permission_type'] === 'share') || $isAdmin) {
        $content_share_permission = true;
    }
    // Check for link permission or allow it if the user is an admin
    if ((!$content_link_permission && $row['content_permission_type'] === 'link') || $isAdmin) {
        $content_link_permission = true;
    }

    next($contentPermissions);
}



// Process each row to build the nested structure
foreach ($contentPermissions as $row) {
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
            'post_group_name' => $row['post_group_name'],
            'permissions' => [] // Initialize the permissions array
        ];
    }
    
    // Add the permission to the post's permissions array
    $posts[$content_id]['permissions'][] = [
        'content_permission_type' => $row['content_permission_type']
        // Add more permission-related fields here if necessary
    ];
}

// Extract permission types into an array
$permission_types = array_column($posts[$content_id]['permissions'], 'content_permission_type');
// Create a space-separated string of permission types
$permission_types_string = implode(' ', array_map('strtolower', $permission_types));

// Check if comment privilege are set for the post, str_contains() outputs a boolean True if the string contains the word 'comment'
$hasCommentPrivilege = str_contains($permission_types_string, 'comment');

$sqlContent = $pdo->prepare('SELECT 
                            content_type, content_title, content_data, m.username, content_creation_date
                            FROM 
                                kpc353_2.content as c
                                LEFT JOIN kpc353_2.members as m
                                    on c.creator_id = m.member_id
                            WHERE 
                                content_id = :content_id');
$sqlContent->execute(['content_id' => $content_id]);
$contentDetails = $sqlContent->fetch(PDO::FETCH_ASSOC);

$content_type = $contentDetails['content_type'];
$content_title = $contentDetails['content_title'];
$content_data = $contentDetails['content_data'];
$content_creator = $contentDetails['username'];
$content_creation_datetime = $contentDetails['content_creation_date'];



$commentContent = $pdo->prepare('
SELECT content_comment_id, commenter_member_id, comment_text, target_content_id, datetime_comment, m.username
FROM content_comment as cc
    INNER JOIN members as m
        ON cc.commenter_member_id = m.member_id
WHERE target_content_id = :content_id
ORDER BY datetime_comment DESC'
);
$commentContent->execute(['content_id' => $content_id]);
$commentDetails = $commentContent->fetchAll(PDO::FETCH_ASSOC);
 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment']) && $hasCommentPrivilege) {
    //Sanitize the user input to prevent cross-site scripting (XSS) attacks
    $comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');

    try {
        $stmt_comment = $pdo->prepare('
            INSERT INTO content_comment
                (commenter_member_id, comment_text, target_content_id) 
            VALUES 
                (:member_id, :comment, :content_id)
        ');
        $stmt_comment->execute([
            'member_id' => $logged_in_member_id, 
            'comment' => $comment, 
            'content_id' => $content_id
        ]);

        echo "<script>alert('Comment created!');</script>";
        echo "<script>window.location.href = 'COSN_content_view.php?content_id=" . $content_id . "';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Database error: {$e->getMessage()}');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="COSN_content_view.css" />
<head>
    <meta charset="UTF-8">
    <title>View content</title>
</head>

<body>
<div class="main-content">
    <h1>View Content</h1>
    <small>This content has passed moderation</small>
    
    <div class="view-content-container">   
        <div class="feed-item" data-permission-type="<?php echo htmlspecialchars($permission_types_string); ?>">
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
            <hr>
            <small>
                <br>Creator: <?php echo $content_creator; ?>
                <br>Creation Date: <?php echo $content_creation_datetime; ?>
                <br>
                Content Type: <?php echo htmlspecialchars($content_type); ?>
            </small>

            <!-- Action Buttons -->
            <div class="action-buttons">

                <?php if ($content_edit_permission): ?>
                    <a href="COSN_content_edit.php?content_id=<?php echo urlencode($content_id); ?>" class="action-button edit-button">EDIT</a>
                <?php else: ?>
                    <p>You don't have permission to EDIT this content</p>
                <?php endif; ?>

                <?php if ($content_share_permission): ?>
                    <a href="edit_content.php?content_id=<?php echo urlencode($content_id); ?>" class="action-button share-button">SHARE</a>
                <?php else: ?>
                    <p>You don't have permission to SHARE this content</p>
                <?php endif; ?>

                <?php if ($content_link_permission): ?>
                    <a href="edit_content.php?content_id=<?php echo urlencode($content_id); ?>" class="action-button link-button">LINK</a>
                <?php else: ?>
                    <p>You don't have permission to LINK this content</p>
                <?php endif; ?>


            </div>
        </div>
    </div>

    <div class="comments-container">
        <h2>Comments on this post</h2>
        <?php foreach ($commentDetails as $comment): ?>
            <div class="comment">
                <p><strong><?php echo htmlspecialchars($comment['username']); ?></strong> said:</p>
                <p><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                <p class="comment-date"><?php echo $comment['datetime_comment']; ?></p>
            </div>
        <?php endforeach; ?>
        <?php if (empty($commentDetails)): ?>
            <p>No comments yet, be the first one to comment!</p>
        <?php endif; ?>
    </div>
    
    <br><br>
    <hr>
    <?php if ($hasCommentPrivilege): ?>
        <form class="comment-form" data-permission-type="<?php echo htmlspecialchars($permission_types_string); ?>" method="POST">
            <h2>Add comment on this content</h2>
                <label for="comment">Comment:</label>
                <input type="text" id="comment" name="comment" required>
            <button type="submit" name="add_comment">Add comment</button>
        </form>
    <?php else: ?>
        <p>You don't have permission to comment on this content</p>
    <?php endif; ?>

</html>