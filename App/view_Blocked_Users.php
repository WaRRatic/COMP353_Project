<?php
session_start();
include("db.php");


$member_id = $_SESSION['member_id'];

// Query to fetch blocked users
$sql = "
    SELECT m.user_id, m.username, m.first_name, m.last_name
    FROM cosn.member_realtionships mr
    INNER JOIN cosn.members m ON m.user_id = mr.target_member_id
    WHERE mr.origin_member_id = ? AND mr.member_relationship_type = 'blocked' 
    AND mr.member_relationship_status = 'approved'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $logged_in_user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the blocked users
$blocked_users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $blocked_users[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blocked Users</title>
    <link rel="stylesheet" type="text/css" href="./css/admin_edit_groups.css">
</head>
<body>
    <h1>Blocked Users</h1>

    <div>
        <?php if (count($blocked_users) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blocked_users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                            <td>
                                <form action="unblock_user.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="blocked_id" value="<?php echo $user['user_id']; ?>">
                                    <button type="submit" name="unblock" class="btn-unblock">Unblock</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not blocked any users.</p>
        <?php endif; ?>
    </div>
</body>
</html>
