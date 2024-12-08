<?php
session_start();
include 'db_config.php';


$member_id = $_SESSION['member_id'];

// Query to fetch friends
$sql_friends = "SELECT m.user_id, m.username, m.first_name, m.last_name
    FROM kpc353_2.member_relationships mr
    INNER JOIN kpc353_2.members m 
    ON (m.user_id = mr.target_member_id AND mr.origin_member_id = ?)
    OR (m.user_id = mr.origin_member_id AND mr.target_member_id = ?)
    WHERE mr.member_relationship_type = 'friend' 
    AND mr.member_relationship_status = 'approved'";

$stmt_friends = $conn->prepare($sql_friends);
$stmt_friends->bind_param("ii", $member_id, $member_id);
$stmt_friends->execute();
$result_friends = $stmt_friends->get_result();
$friends = $result_friends->fetch_all(MYSQLI_ASSOC);
$stmt_friends->close();


// Query to fetch potential friends
$sql_potential_friends = "SELECT DISTINCT m.user_id, m.username, m.first_name, m.last_name
        FROM kpc353_2.members m
        INNER JOIN kpc353_2.group_members gm ON m.user_id = gm.participant_member_id
        INNER JOIN kpc353_2.group_members gm2 ON gm.joined_group_id = gm2.joined_group_id
        WHERE gm2.participant_member_id = ? 
        AND m.user_id != ?
        AND m.user_id NOT IN (
        SELECT mr.target_member_id FROM kpc353_2.member_relationships mr
        WHERE mr.origin_member_id = ? 
        AND (mr.member_relationship_type = 'friend' OR mr.member_relationship_status = 'requested')
        UNION
        SELECT mr.origin_member_id FROM kpc353_2.member_relationships mr
        WHERE mr.target_member_id = ?
        AND (mr.member_relationship_type = 'friend' OR mr.member_relationship_status = 'requested')
    )
    LIMIT 20";

$stmt_potential_friends = $conn->prepare($sql_potential_friends);
$stmt_potential_friends->bind_param("iiii", $member_id, $member_id, $member_id, $member_id);
$stmt_potential_friends->execute();
$result_potential_friends = $stmt_potential_friends->get_result();
$potential_friends = $result_potential_friends->fetch_all(MYSQLI_ASSOC);
$stmt_potential_friends->close();


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends</title>
    <link rel="stylesheet" type="text/css" href="./css/admin_edit_groups.css">
</head>
<body>
    <h1>Your Friends</h1>

    <div>
        <?php if (count($friends) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($friends as $friend): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($friend['username']); ?></td>
                            <td><?php echo htmlspecialchars($friend['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($friend['last_name']); ?></td>
                            <td>
                                <form action="unfriend_user.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="target_id" value="<?php echo $friend['user_id']; ?>">
                                    <button type="submit" name="unfriend" class="btn-unfriend">Unfriend</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You currently have no friends added.</p>
        <?php endif; ?>
    </div>

        
    <h1>Potential Friends</h1>

<!-- Display Potential Friends -->
<div>
    <?php if (count($potential_friends) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($potential_friends as $potential_friend): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($potential_friend['username']); ?></td>
                        <td><?php echo htmlspecialchars($potential_friend['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($potential_friend['last_name']); ?></td>
                        <td>
                            <form action="send_friend_request.php" method="POST" style="display:inline;">
                                <input type="hidden" name="receiver_id" value="<?php echo $potential_friend['user_id']; ?>">
                                <button type="submit" name="send_request" class="btn-send-request">Send Friend Request</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No potential friends found.</p>
    <?php endif; ?>
</div>


</body>
</html>
