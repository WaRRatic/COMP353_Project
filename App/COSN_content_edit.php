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
//set the logged in member id who is accessing the homepage
$logged_in_member_id = $_SESSION['member_id'];

// Check if the Content_ID was passed in the URL
if (!isset($_GET['content_id'])) {
    echo "<script>alert('No content_ID is specified!');</script>";
    echo "<script>window.location.href = 'homepage.php';</script>"; 
    exit;
}
$content_id = $_GET['content_id'];

$isAdmin=false;
// Check if the user is a COSN admin
if ($_SESSION['privilege_level'] === 'administrator'){
    $isAdmin = true;
}
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
        m.member_id = :logged_in_member_id
        AND content_id = :content_id 
    ORDER BY content_creation_date desc
    ");

$sql->execute(['logged_in_member_id' => $logged_in_member_id, 'content_id'=>$content_id]);
$contentPermissions_user = $sql->fetchAll(PDO::FETCH_ASSOC);

$content_edit_permission = false;
while($row = current($contentPermissions_user)) {
    // Check for edit permission or allow it if the user is an admin
    if ((!$content_edit_permission && $row['content_permission_type'] === 'edit') || $isAdmin) {
        $content_edit_permission = true;
    }
    next($contentPermissions_user);
}
    
// Redirect the user if they don't have permission to edit the content or not an admin
if (!$content_edit_permission) {
    echo "<script>alert('You don't have permission to edit this content');</script>";
    echo "<script>window.location.href = 'homepage.php';</script>";
    exit;
}

//all content permissions
$sql = $pdo->prepare("
    SELECT
        content_id, cpp.content_public_permission_type AS content_permission_type, 'public' as content_feed_type, NULL as authorized_group_name, NULL as authorized_group_id, NULL as authorized_member_name, NULL as authorized_member_id
    FROM
        kpc353_2.content as cont
    INNER JOIN kpc353_2.content_public_permissions as cpp
        ON cont.content_id = cpp.target_content_id
    WHERE
        content_id = :content_id
    UNION
    SELECT
        content_id,cmp.content_permission_type AS content_permission_type, 'private' as content_feed_type, NULL as authorized_group_name,NULL as authorized_group_id, m.username as authorized_member_name, m.member_id as authorized_member_id
    FROM
        kpc353_2.content as cont
    INNER JOIN kpc353_2.content_member_permission as cmp
        ON cont.content_id = cmp.target_content_id
    INNER JOIN kpc353_2.members as m
        ON cmp.authorized_member_id = m.member_id
    WHERE
        content_id = :content_id 
    UNION
    SELECT
        content_id, cgp.content_group_permission_type AS content_permission_type, 'group' as content_feed_type, g.group_name as authorized_group_name, g.group_id as authorized_group_id, NULL as authorized_member_name, NULL as authorized_member_id
    FROM
        kpc353_2.content as cont
    INNER JOIN kpc353_2.content_group_permissions as cgp
        ON cont.content_id = cgp.target_content_id
    INNER JOIN kpc353_2.groups as g
        ON g.group_id = cgp.target_group_id
    WHERE 
        content_id = :content_id 
    ");

$sql->execute(['content_id'=>$content_id]);
$contentPermissions = $sql->fetchAll(PDO::FETCH_ASSOC);


//get COSN users to which a permission can apply
$sql = "SELECT 
            m.username, m.member_id as target_member_id
        FROM  kpc353_2.members as m";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$members_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

//get COSN users to which a permission can apply
$sql = "SELECT 
            g.group_name, g.group_id as target_group_id
        FROM  kpc353_2.groups as g";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$group_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the content details to display in the form and on the card
$sqlContent = $pdo->prepare('SELECT 
                            content_type, content_title, content_data, m.username, content_creation_date, moderation_status
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
$content_moderation = $contentDetails['moderation_status'];

// Form logic
// If the form is submitted, update the content in the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Delete content
    if (isset($_POST['delete_content'])) {

        $pdo->beginTransaction();

        $sql = 'DELETE FROM kpc353_2.content 
                WHERE content_id = :content_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['content_id' => $content_id]);

        $pdo->commit();

        echo "<script>alert('Content deleted successfully!');";
        echo "window.location.href = 'homepage.php';</script>";
        exit;
    }
    
    // Approve content
    elseif (isset($_POST['approve_content'])) {

        $pdo->beginTransaction();
        $sql = 'UPDATE kpc353_2.content 
                SET moderation_status = "approved" 
                WHERE content_id = :content_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['content_id' => $content_id]);

        $pdo->commit();

        echo "<script>
        alert('Content approved successfully!');
        window.location.href = 'COSN_content_edit.php?content_id=$content_id';
        </script>";
    }

    // Reject content
    elseif (isset($_POST['reject_content'])) {
        $pdo->beginTransaction();

        $sql = 'UPDATE kpc353_2.content 
                SET moderation_status = "rejected" 
                WHERE content_id = :content_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['content_id' => $content_id]);

        $pdo->commit();
        echo "<script>
        alert('Content rejected successfully!');
        window.location.href = 'COSN_content_edit.php?content_id=$content_id';
        </script>";

    }
    elseif(isset($_POST['update_content'])){    
        // update the variables from the form
        $content_type = $_POST['content_type'];
        $content_title = $_POST['content_title'];
        $content_data = $_POST['content_data'];

        // Begin transaction
        $pdo->beginTransaction();

        // Insert into content table
        $sql = 'UPDATE kpc353_2.content 
                SET content_type = :content_type, content_data = :content_data, content_title = :content_title 
                WHERE content_id = :content_id';
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'content_type'  => $content_type,
            'content_data'  => $content_data,
            'content_title' => $content_title,
            'content_id' => $content_id
        ]);

        //commit the transaction therefore saving the data
        $pdo->commit(); 
        echo "<script>alert('Content updated successfully!');</script>";
        echo "<script>window.location.href = 'COSN_content_edit.php?content_id=" . $content_id . "';</script>";
    }
    elseif(isset($_POST['add_public_permission'])){    
        // update the variables from the form
        $content_permission_type = $_POST['permission_type'];
        $permission_level = 'public';
        $permission_group_id = NULL;
        $permission_member_id = NULL;

        echo "<script>window.location.href = 'COSN_content_change_permission.php?content_id=" . $content_id . "&level=" . $permission_level . "&type=" . $content_permission_type . "&group_id=". $permission_group_id . "&member_id=" . $permission_member_id . "&action=add';</script>";
    }
    elseif(isset($_POST['add_private_permission'])){    
        // update the variables from the form
        $content_permission_type = $_POST['permission_type'];
        $permission_level = 'private';
        $permission_group_id = NULL;
        $permission_member_id = $_POST['target_member_id'];

        echo "<script>window.location.href = 'COSN_content_change_permission.php?content_id=" . $content_id . "&level=" . $permission_level . "&type=" . $content_permission_type . "&group_id=". $permission_group_id . "&member_id=" . $permission_member_id . "&action=add';</script>";
    }
    elseif(isset($_POST['add_group_permission'])){    
        // update the variables from the form
        $content_permission_type = $_POST['permission_type'];
        $permission_level = 'group';
        $permission_group_id = $_POST['target_group_id'];
        $permission_member_id = NULL;

        echo "<script>window.location.href = 'COSN_content_change_permission.php?content_id=" . $content_id . "&level=" . $permission_level . "&type=" . $content_permission_type . "&group_id=". $permission_group_id . "&member_id=" . $permission_member_id . "&action=add';</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="COSN_content_view.css" />
<head>
    <meta charset="UTF-8">
    <title>Manage & moderate specific content</title>
</head>

<body>
<div class="main-content">

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

        </div>
    </div>

    <h3>Manage & Moderate Content</h3>
    <form method="POST">
        <select name="content_type">
            <option value="text" <?php echo ($content_type === 'text') ? 'selected' : ''; ?>>Text</option>
            <option value="image" <?php echo ($content_type === 'image') ? 'selected' : ''; ?>>Image</option>
            <option value="video" <?php echo ($content_type === 'video') ? 'selected' : ''; ?>>Video</option>
        </select>
        
        <label for="content_title">Content Title:</label>
        <input type="text" id="content_title" name="content_title" value="<?php echo $content_title; ?>" ><br>
        
        <label for="content_data">Content Data:</label>
        <input type="text" id="content_data" name="content_data" value="<?php echo $content_data; ?>" ><br>
        
        <?php if ($isAdmin) :?>
            <label for="moderation_status">Moderation status:</label>
            <input type="text" id="moderation_status" name="moderation_status" value="<?php echo $content_moderation; ?>" required><br>
        <?php endif; ?>

    <button type="submit" name="update_content">Update Content</button>


    <button type="submit" name="delete_content" onclick="return confirm('Are you sure you want to delete this content? This action cannot be undone.');" style="background-color: #ff4444; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
    Delete Content
    </button>
    
    <?php if ($isAdmin) :?>
        <button type="submit" name="approve_content" onclick="return confirm('Are you sure you want to approve (moderate) this content? ');" style="background-color: #00ff00; color: black; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
        Approve Content
        </button>
    <?php endif; ?>
    
    <?php if ($isAdmin) :?>
    <button type="submit" name="reject_content" onclick="return confirm('Are you sure you want to reject (moderate) this content? ');" style="background-color: #FFFF00; color: black; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
    Reject Content
    </button>
    <?php endif; ?>
    

    </form>

    
    <br><br>
    <hr>
    <h3>Manage existing permissions</h3>
    
    <?php
        if(empty($contentPermissions)){
            echo "<h1>This content does not have any permissions set on it.</h1>";
        }
        else{
            echo "<table border='1'>";
            echo "<tr>";
            echo "<th>Permission Type</th>";
            echo "<th>Permission Level</th>";
            echo "<th>Authorized Group</th>";
            echo "<th>Authorized Member</th>";
            echo "<th>Remove permission?</th>";

            echo "</tr>";
            // Output data of each row
            while($row = current($contentPermissions)) {
                //start row
                echo "<tr>";
                echo "<td>" . $row['content_permission_type'] . "</td>";
                if($row['content_feed_type'] === 'private'){
                    echo "<td> member </td>";
                }
                else{echo "<td>" . $row['content_feed_type'] ."</td>";}
                if($row['content_feed_type'] === 'group'){
                    echo "<td>" . $row['authorized_group_name'] ."</td>";
                }else{
                    echo "<td> Not applicable </td>";
                }
                if($row['content_feed_type'] === 'private'){
                    echo "<td>" . $row['authorized_member_name'] ."</td>";
                }else{
                    echo "<td> Not applicable </td>";
                }
                echo "<td><a href='COSN_content_change_permission.php?content_id=" . $row['content_id'] . "&level=" . $row['content_feed_type'] . "&type=" . $row['content_permission_type'] . "&group_id=". $row['authorized_group_id'] . "&member_id=" . $row['authorized_member_id'] . "&action=remove'><button>Remove permission</button></a></td>";

                echo "</tr>";
                next($contentPermissions);
            }
            echo "</table>";
            echo "<br><br><hr>";
        }
    ?>

    <br><br>
    <hr>

    <h3>Add new public permission</h3>
    <form method="POST">
        <label for="permission_type">Permission Type:</label>
        <select id="permission_type" name="permission_type" required>
            <option value="read">Read</option>
            <option value="comment">Comment</option>
            <option value="share">Share</option>
            <option value="link">Link</option>
        </select>

        <br><br>
        <button type="submit" name="add_public_permission" style="background-color: green; color: black; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
        Add public permission
        </button>
    </form>

    <br><br>
    <hr>

    <h3>Add member permission</h3>
    <form method="POST">
        <label for="permission_type">Permission Type:</label>
        <select id="permission_type" name="permission_type" required>
            <option value="read">Read</option>
            <option value="edit">Edit</option>
            <option value="comment">Comment</option>
            <option value="share">Share</option>
            <option value="link">Link</option>
        </select>

        <label for="target_member_id">Member authorized:</label>
        <select id="target_member_id" name="target_member_id" required>
            <?php foreach($members_list as $member): ?>
                <option value= <?php echo $member['target_member_id']; ?> > <?php echo $member['username']; ?> </option>
            <?php endforeach; ?>
        </select>

        <br><br>
        <button type="submit" name="add_private_permission" style="background-color: green; color: black; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
        Add member permission
        </button>
    </form>
 
    <br><br>
    <hr>

    <h3>Add group permission:</h3>
    <form method="POST">
        <label for="permission_type">Permission Type:</label>
        <select id="permission_type" name="permission_type" required>
            <option value="read">Read</option>
            <option value="edit">Edit</option>
            <option value="comment">Comment</option>
            <option value="share">Share</option>
            <option value="link">Link</option>
        </select>
        <label for="target_group_id">Group authorized:</label>
        <select id="target_group_id" name="target_group_id" required>
            <?php foreach($group_list as $group): ?>
                <option value= <?php echo $group['target_group_id']; ?> > <?php echo $group['group_name']; ?> </option>
            <?php endforeach; ?>
        </select>

        <br><br>
        <button type="submit" name="add_group_permission" style="background-color: green; color: black; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
        Add group permission
        </button>
    </form>

</div>
</html>