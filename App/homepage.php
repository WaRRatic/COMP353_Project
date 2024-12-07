<?php
session_start();
include("db_config.php");
include("header.php");
include('sidebar.php'); 

//set the logged in member id
$logged_in_member_id = $_SESSION['member_id'];

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
// Query to get the member's status
$sql_member_status = "
    SELECT 
        privilege_level 
    FROM 
        kpc353_2.members 
    WHERE 
        member_id = :logged_in_member_id";

$stmt_member_status = $pdo->prepare($sql_member_status);
$stmt_member_status->execute([':logged_in_member_id' => $logged_in_member_id]);
$member_status = $stmt_member_status->fetchColumn();


// check if the Homepage is accessed in the context of a group or member
// If the group_id is set, the user is accessing the homepage in the context of a group
// If the group_id is not set (false), the user is accessing the homepage in the context of a member
$group_id = null;
if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];

    //check if the user is a member of the group
    $sql_check_group_membership = 
    "SELECT 
        participant_member_id
    FROM 
        kpc353_2.group_members 
    WHERE 
        joined_group_id = :group_id 
        AND participant_member_id = :logged_in_member_id
        AND GROUP_MEMBER_STATUS NOT IN ('requested','ban')";

    $stmt_group_membership = $pdo->prepare($sql_check_group_membership);
    $stmt_group_membership->execute([':group_id' => $group_id, ':logged_in_member_id' => $logged_in_member_id]);
    $isGroupMember = $stmt_group_membership->fetchColumn();
    
    //check if the member is a member of the group to view the group's homepage
    if(!$isGroupMember){
        echo "<script>alert('You don't have permission to view this group's homepage!');</script>";
        echo "<script>window.location.href = 'homepage.php';</script>";
        exit;
    }

    // Get the group name
    $sql_group_name = "
        SELECT 
            group_name 
        FROM 
            kpc353_2.groups 
        WHERE 
            group_id = :group_id";

    $stmt_group_name = $pdo->prepare($sql_group_name);
    $stmt_group_name->execute([':group_id' => $group_id]);
    $group_name = $stmt_group_name->fetchColumn();

    // Check if the logged-in member is the group admin/owner
    $sql = "
    SELECT 
        group_member_status
    FROM kpc353_2.group_members
    WHERE 
        joined_group_id = :requested_group_id
        AND participant_member_id = :logged_in_member_id
        AND group_member_status = 'admin'
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':requested_group_id' => $group_id]);
    $isGroupAdmin = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
}

//fetch the content feed based on MEMBER context of the homepage
if(!$group_id){
    // Content feed SQL QUERY explanation:
    // General conditions: (1) Get content the logged-in user has permissions on, (2) content that has passed moderation, (3) content that is not deleted, (4) exclude comments
    // Query to get Public content
    // UNION Query to get private content that the user has permission to view
    // UNION Query to get content that the group the user is in has permission to view
    $sql = "
        SELECT
            content_id, m.username, content_type, content_data, content_creation_date, content_title, moderation_status, 
            cpp.content_public_permission_type AS content_permission_type, 'public' as content_feed_type, NULL as post_group_name
        FROM
            kpc353_2.content as cont
        INNER JOIN kpc353_2.content_public_permissions as cpp
            ON cont.content_id = cpp.target_content_id
        INNER JOIN kpc353_2.members as m
            ON cont.creator_id = m.member_id
        WHERE 
            moderation_status = 'approved'
            and content_deleted_flag <> true
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
            and content_deleted_flag <> true
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
            and m.member_id = :logged_in_member_id
            and content_deleted_flag <> true
        ORDER BY content_creation_date desc
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id]);
}else{
    // Content feed SQL QUERY explanation:
    // General conditions: (1) Get content the logged-in user has permissions on, (2) content that has passed moderation, (3) content that is not deleted, (4) exclude comments
    // Get only the content for which this Group has permission 
    $sql = "
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
            and m.member_id = :logged_in_member_id
            and content_deleted_flag <> true
        ORDER BY content_creation_date desc
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id]);

}



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
<link rel="stylesheet" type = "text/css" href="homepage.css" />
<link rel="stylesheet" type = "text/css" href="content_feed.css" />
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
</head>
<body>
<div class="main-content">
    <?php
    // Display the admin panel if the user is an administrator
    if ($_SESSION['privilege_level'] === 'administrator') {
        echo "<div style='border: 2px solid teal; padding: 10px; margin-bottom: 20px; display: flex; flex-direction: column; align-items: center;'>"; // Admin panel container with border
        echo "<p style='font-weight: bold; text-align: center;'>ADMIN PANEL</p>";
        echo "<div style='display: flex; justify-content: space-around; gap: 20px;'>"; // Horizontal button container
        echo "<a href='admin_manage_users.php'><button style='background-color: teal; color: black;'>Manage COSN users</button></a>";
        echo "<a href='admin_manage_groups.php'><button style='background-color: teal; color: black;'>Manage COSN groups</button></a>";
        echo "<a href='admin_manage_content.php'><button style='background-color: teal; color: black;'>Manage & moderate COSN content</button></a>";
        echo "</div>";
        echo "</div>";
    }
   
    if ($group_id) {
        // Display the group admin panel if the user is the admin of the group
        if($isGroupAdmin){
            echo "<div style='border: 2px solid orange; padding: 10px; margin-bottom: 20px; display: flex; flex-direction: column; align-items: center;'>"; // Admin panel container with border
            echo "<p style='font-weight: bold; text-align: center;'>GROUP ADMIN PANEL</p>";
            echo "<a href='COSN_group_admin.php?group_id=". $group_id ."'><button style='background-color: orange; color: black;'>Manage group</button></a>";
            echo "</div>";
        }
        // Display the group name if the user is accessing the homepage in the context of a group
        echo "<h1>Welcome to the ". $group_name . " COSN Group homepage!</h1>";

    //display normal Member homepage greeting, if the user is accessing the homepage in the context of a member
    } else {
        // Display the member's username if the user is accessing the homepage in the context of a member
        echo "<h1>Welcome to ". $_SESSION['member_username'] ." homepage!</h1>";
        echo "<small>Your status in COSN is: ".$member_status." </small>";
    }
    ?>

    <br>
    <hr>


    <h2>Content feed:</h2>

<!-- Display public feed -->
<!-- <h2><?php echo $_SESSION['member_username']; ?>'s Content Feed (most recent posts first)</h2> -->

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
                <hr>
                    Posted on <?php echo htmlspecialchars($post['content_creation_date']); ?> 
                    by User <?php echo htmlspecialchars($post['username']); ?>

                    (Content permission: <?php echo htmlspecialchars($post['content_feed_type']); ?>)
                    <?php if (isset($post['post_group_name']) && !empty($post['post_group_name'])): ?>
                       <hr><br> Content permission via group: <?php echo htmlspecialchars($post['post_group_name']); ?>
                    <?php endif; ?>
                </small>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="edit_content.php?content_id=<?php echo urlencode($post['content_id']); ?>" class="action-button edit-button">Edit</a>
                    <a href="Content_Interact.php?state=share&content_id=<?php echo urlencode($post['content_id']); ?>" class="action-button share-button">Share</a>
                    <a href="comment_on_content.php?content_id=<?php echo urlencode($post['content_id']); ?>" class="action-button comment-button">Comment</a>
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


    </div>
</body>
</html>
