<?php
session_start();
include("db_config.php");
include("header.php");
include('sidebar.php');
include('getYoutubeVideoId_function.php');

//set the logged in member id who is accessing the homepage
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

// Set the Homepage context, which can be 'owner', 'group' or 'member'
// 'group' - the user is accessing the homepage of a group
// 'member' - the user is accessing the homepage of another member
// 'onwer' - the user is accessing their own homepage
$homepage_context = null;
$homepage_context_group_id = null;
$homepage_context_member_id = null;

// If the group_id is set, the user is accessing the homepage in the context of a group
if (isset($_GET['group_id'])) {
    
    $homepage_context = 'group';
    $homepage_context_group_id = $_GET['group_id'];

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
    $stmt_group_membership->execute([':group_id' => $homepage_context_group_id, ':logged_in_member_id' => $logged_in_member_id]);
    $isGroupMember = $stmt_group_membership->fetchColumn();
    
    //check if the member is a member of the group to view the group's homepage
    if(!$isGroupMember){
        echo "<script>alert('You don't have permission to view this group's homepage!');</script>";
        echo "<script>window.location.href = 'homepage.php';</script>";
        exit;}

    // Check if the logged-in member is the group admin/owner
    $sql = "
    SELECT 
        group_member_status
    FROM 
        kpc353_2.group_members
    WHERE 
        joined_group_id = :requested_group_id
        AND participant_member_id = :logged_in_member_id
        AND group_member_status = 'admin'
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':requested_group_id' => $homepage_context_group_id]);
    $isGroupAdmin = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //get group's name, description, category and creation date
    $sql = "
    SELECT 
        group_name, description, category, creation_date
    FROM 
        kpc353_2.groups
    WHERE 
        group_id = :requested_group_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':requested_group_id' => $homepage_context_group_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    //set the group name, description, category and creation date
    $homepage_context_group_name = $result[0]['group_name'];
    $homepage_context_group_description = $result[0]['description'];
    $homepage_context_group_category = $result[0]['category'];
    $homepage_context_group_creation_date = $result[0]['creation_date'];

    }
    // If the member_id is set, the user is accessing the homepage in the context of a member
    elseif(isset($_GET['member_id'])){

        $homepage_context = 'member';
        $homepage_context_member_id = $_GET['member_id'];
        
        //check if the logged-in member has been blocked by the member whose homepage is being accessed
        $sql_check_blocked_status = 
        "SELECT 
            relationship_id
        FROM 
            kpc353_2.member_relationships 
        WHERE 
            origin_member_id = :member_id 
            AND target_member_id = :logged_in_member_id
            AND member_relationship_type IN ('blocked')";

        $stmt_blocked_check = $pdo->prepare($sql_check_blocked_status);
        $stmt_blocked_check->execute([':member_id' => $homepage_context_member_id, ':logged_in_member_id' => $logged_in_member_id]);
        $isRelationshipBlocked = $stmt_blocked_check->fetchColumn();

        //check if the logged-in member has blocked the member whose homepage is being accessed
        if($isRelationshipBlocked){
            echo "<script>alert('This member has blocked you!');</script>";
            echo "<script>window.location.href = 'homepage.php';</script>";
            exit;}
    
        
        //check if the logged-in member has a relationship with the member whose homepage is being accessed (friends, family, co-worker)
        $sql_check_relationship_status = 
        "SELECT 
            member_relationship_type
        FROM 
            kpc353_2.member_relationships 
        WHERE 
            origin_member_id = :member_id 
            AND target_member_id = :logged_in_member_id
            AND member_relationship_type IN ('accepted')";

        $stmt_relationship_status = $pdo->prepare($sql_check_relationship_status);
        $stmt_relationship_status->execute([':member_id' => $homepage_context_member_id, ':logged_in_member_id' => $logged_in_member_id]);
        $memberRelationshipStatus = $stmt_relationship_status->fetchColumn();
        

        // Get the member personal info on which permissions will be applied later
        $sql = "
        SELECT 
            username,email,first_name,last_name,address,date_of_birth,privilege_level
        FROM 
            kpc353_2.members
        WHERE 
            member_id = :member_id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':member_id' => $homepage_context_member_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //load the variables which will be used to populate the form
        $homepage_context_member_username = $result[0]['username'];
        $homepage_context_member_email = $result[0]['email'];
        $homepage_context_member_first_name = $result[0]['first_name'];
        $homepage_context_member_last_name = $result[0]['last_name'];
        $homepage_context_member_address = $result[0]['address'];
        $homepage_context_member_dob = $result[0]['date_of_birth'];
        $homepage_context_member_privilege_level = $result[0]['privilege_level'];
        
        //Get the permissions of the logged-in member on the personal information of the member whose homepage is being accessed
        //Get the pemission on EMAIL
        $sql = "
        SELECT 
            personal_info_permission_id
        FROM 
            kpc353_2.personal_info_permissions
        WHERE
            personal_info_type = 'email'
            AND authorized_member_id = :logged_in_member_id
        UNION
        SELECT
            personal_info_public_permission_id
        FROM 
            kpc353_2.personal_info_public_permissions
        WHERE
            personal_info_type = 'email'
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':logged_in_member_id' => $logged_in_member_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $display_email_permission = true;
        } else {
            $display_email_permission = false;
        }
        
        //Get the pemission on FIRST NAME
        $sql = "
        SELECT 
            personal_info_permission_id
        FROM 
            kpc353_2.personal_info_permissions
        WHERE
            personal_info_type = 'first_name'
            AND authorized_member_id = :logged_in_member_id
        UNION
        SELECT
            personal_info_public_permission_id
        FROM 
            kpc353_2.personal_info_public_permissions
        WHERE
            personal_info_type = 'first_name'
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':logged_in_member_id' => $logged_in_member_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $display_first_name_permission = true;
        } else {
            $display_first_name_permission = false;
        }

        //Get the pemission on LAST NAME
        $sql = "
        SELECT 
            personal_info_permission_id
        FROM 
            kpc353_2.personal_info_permissions
        WHERE
            personal_info_type = 'last_name'
            AND authorized_member_id = :logged_in_member_id
        UNION
        SELECT
            personal_info_public_permission_id
        FROM 
            kpc353_2.personal_info_public_permissions
        WHERE
            personal_info_type = 'last_name'
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':logged_in_member_id' => $logged_in_member_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $display_last_name_permission = true;
        } else {
            $display_last_name_permission = false;
        }

        //Get the pemission on ADDRESS
        $sql = "
        SELECT 
            personal_info_permission_id
        FROM 
            kpc353_2.personal_info_permissions
        WHERE
            personal_info_type = 'address'
            AND authorized_member_id = :logged_in_member_id
        UNION
        SELECT
            personal_info_public_permission_id
        FROM 
            kpc353_2.personal_info_public_permissions
        WHERE
            personal_info_type = 'address'
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':logged_in_member_id' => $logged_in_member_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $display_address_permission = true;
        } else {
            $display_address_permission = false;
        }

        //Get the pemission on DATE OF BIRTH
        $sql = "
        SELECT 
            personal_info_permission_id
        FROM 
            kpc353_2.personal_info_permissions
        WHERE
            personal_info_type = 'date_of_birth'
            AND authorized_member_id = :logged_in_member_id
        UNION
        SELECT
            personal_info_public_permission_id
        FROM 
            kpc353_2.personal_info_public_permissions
        WHERE
            personal_info_type = 'date_of_birth'
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':logged_in_member_id' => $logged_in_member_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $display_date_of_birth_permission = true;
        } else {
            $display_date_of_birth_permission = false;
        }

//if the context is not Group or Member, then the user is accessing their own homepage
    }else{
        $homepage_context = 'owner';
        // Get the member personal info on which permissions will be applied later
        $sql = "
        SELECT 
            username,email,first_name,last_name,address,date_of_birth,privilege_level
        FROM 
            kpc353_2.members
        WHERE 
            member_id = :member_id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':member_id' => $logged_in_member_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //load the variables which will be used to populate the form
        $homepage_context_owner_username = $result[0]['username'];
        $homepage_context_owner_email = $result[0]['email'];
        $homepage_context_owner_first_name = $result[0]['first_name'];
        $homepage_context_owner_last_name = $result[0]['last_name'];
        $homepage_context_owner_address = $result[0]['address'];
        $homepage_context_owner_dob = $result[0]['date_of_birth'];
        $homepage_context_owner_privilege_level = $result[0]['privilege_level'];

    }





// check if the Homepage is accessed in the context of a member
// If the group_id is set, the user is accessing the homepage in the context of a group
// If the group_id is not set (false), the user is accessing the homepage in the context of a member


//fetch the content feed based on MEMBER context of the homepage
if($homepage_context === 'owner'){
    // Content feed SQL QUERY explanation:
    // General conditions: (1) Get content the logged-in user has permissions on (2) content that has passed moderation
    
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
        ORDER BY content_creation_date desc
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id]);
}elseif($homepage_context === 'group'){
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
            AND m.member_id = :logged_in_member_id
        ORDER BY content_creation_date desc
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id]);

}elseif($homepage_context === 'member'){
    // Content feed SQL QUERY explanation:
    // General conditions: (1) Get content the logged-in user has permissions on, (2) content that has passed moderation, (3) content that is not deleted, (4) exclude comments
    // Get only the content for which this Member has permission 
    $sql = "
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
            moderation_status = 'approved'
            AND cmp.authorized_member_id = :logged_in_member_id
            AND cont.creator_id = :homepage_context_member_id
        ORDER BY content_creation_date desc
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':homepage_context_member_id' => $homepage_context_member_id, ':logged_in_member_id' => $logged_in_member_id]);
}



// Fetch the content feed
$content_feed = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process each row to build the nested structure
foreach ($content_feed as $row) {
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

// // Optional: Reindex the $posts array to have sequential keys
// if($posts){
//     $posts = array_values($posts);
// }
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
        echo "<p style='font-weight: bold; text-align: center;'>COSN Admin panel</p>";
        echo "<div style='display: flex; justify-content: space-around; gap: 20px;'>"; // Horizontal button container
        echo "<a href='COSN_members.php'><button style='background-color: teal; color: black;'>Manage COSN users</button></a>";
        echo "<a href='COSN_groups.php'><button style='background-color: teal; color: black;'>Manage COSN groups</button></a>";
        echo "<a href='admin_manage_content.php'><button style='background-color: teal; color: black;'>Manage & moderate COSN content</button></a>";
        echo "</div>";
        echo "</div>";
    }
    
    if ($homepage_context === 'group') {
        // Display the group admin panel if the user is the admin of the group
        if($isGroupAdmin){
            echo "<div style='border: 2px solid orange; padding: 10px; margin-bottom: 20px; display: flex; flex-direction: column; align-items: center;'>"; // Admin panel container with border
            echo "<p style='font-weight: bold; text-align: center;'>GROUP ADMIN PANEL</p>";
            echo "<a href='COSN_group_admin.php?group_id=". $homepage_context_group_id ."'><button style='background-color: orange; color: black;'>Manage group</button></a>";
            echo "</div>";
        }
        // Display the group name if the user is accessing the homepage in the context of a group
        echo "<h1>You are viewing ". $homepage_context_group_name . " COSN Group homepage!</h1>";
        echo "<hr>";
        echo "<h3>COSN Group Information:</h3>";
       
        echo"<table border='1'>";
        
        echo "<tr>";
        echo "<td>Group name: </td>";
        echo "<td>". $homepage_context_group_name ."</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td> Group description: </td>";
        echo "<td>". $homepage_context_group_description ."</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td> Group category: </td>";
        echo "<td>". $homepage_context_group_category ."</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td> Group creation date: </td>";
        echo "<td>". $homepage_context_group_creation_date ."</td>";
        echo "</tr>";

        echo"</table>";

    //display normal Member homepage greeting, if the user is accessing the homepage in the context of a member
    } elseif($homepage_context === 'owner'){
        // Display the member's username if the user is accessing the homepage in the context of a member
        echo "<h1>Welcome to your homepage!</h1>";

        echo"<table border='1'>";
        
        echo "<tr>";
        echo "<td>Your username: </td>";
        echo "<td>". $_SESSION['member_username'] ."</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Your COSN privilege level: </td>";
        echo "<td>". $homepage_context_owner_privilege_level ."</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td> Your First Name: </td>";
        echo "<td>". (($homepage_context_owner_first_name) ? $homepage_context_owner_first_name : "you haven't set your First Name yet") ."</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td> Your Last Name: </td>";
        echo "<td>". (($homepage_context_owner_last_name) ? $homepage_context_owner_last_name : "you haven't set your Last Name yet") ."</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td> Your Email: </td>";
        echo "<td>". (($homepage_context_owner_email) ? $homepage_context_owner_email : "you haven't set your Email yet") ."</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td> Your Date of Birth: </td>";
        echo "<td>". (($homepage_context_owner_dob) ? $homepage_context_owner_dob : "you haven't set your Date of Birth yet") ."</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td> Your Address: </td>";
        echo "<td>". (($homepage_context_owner_address) ? $homepage_context_owner_address : "you haven't set your Address yet") ."</td>";
        echo "</tr>";

        echo"</table>";
        echo "<a href='COSN_member_manage.php?member_id=". $logged_in_member_id ."'><button style='background-color: green; color: white;'>Manage my COSN profile</button></a>";


    }elseif($homepage_context === 'member'){
        // Display the member's username if the user is accessing the homepage in the context of a member
        echo "<h1>You are viewing ". $homepage_context_member_username ."'s COSN profile</h1>";
        if($memberRelationshipStatus){
            echo "<bold style='background-color: green; color: black;'>Your relationship with this member is: ".$memberRelationshipStatus." !</bold>";
        }
        echo "<hr>";
        echo "<h3>COSN Member Information:</h3>";
       
        //Username is always visible as is public information
        //new row
        echo"<table border='1'>";
        echo "<tr>";
        //columns of that row
        echo "<td>Username: </td>";
        echo "<td>". $homepage_context_member_username ."</td>";
        echo "</tr>";

        echo "<tr>";
        //columns of that row
        echo "<td>COSN privilege level: </td>";
        echo "<td>". $homepage_context_member_privilege_level ."</td>";
        echo "</tr>";

        if($display_email_permission){
            echo "<tr>";
            echo "<td> Email: </td>";
            echo "<td>". $homepage_context_member_email ."</td>";
            echo "</tr>";
        }
        
        if($display_first_name_permission){
            echo "<tr>";
            echo "<td> First Name: </td>";
            echo "<td>". $homepage_context_member_first_name ."</td>";
            echo "</tr>";}
        
        if($display_last_name_permission){
            echo "<tr>";
            echo "<td> Last Name: </td>";
            echo "<td>". $homepage_context_member_last_name ."</td>";
            echo "</tr>";}
        
        if($display_date_of_birth_permission){
            echo "<tr>";
            echo "<td> Date of birth: </td>";
            echo "<td>". $homepage_context_member_dob ."</td>";
            echo "</tr>";}
        
        if($display_address_permission){
            echo "<tr>";
            echo "<td> Address: </td>";
            echo "<td>". $homepage_context_member_address ."</td>";
            echo "</tr>";}
        
        echo"</table>";
    }
    ?>

<br>
<hr>
<h2>Content feed:</h2>
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
                <br><br>
                <div class="view-post-button">
                    <a href="COSN_content_view.php?content_id=<?php echo urlencode($post['content_id']); ?>" class="view-post-button">View Post</a>
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
