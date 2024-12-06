<?php
session_start();
include("db.php");


$member_id = $_SESSION['member_id'];

// Query to fetch groups the user belongs to
$sql = "
    SELECT g.group_id, g.group_name, g.description, g.creation_date, g.cathegory, 
           gm.role_of_member AS role
    FROM cosn.groups g
    INNER JOIN cosn.group_members gm ON g.group_id = gm.joined_group_id
    WHERE gm.participant_member_id = ? 
    ORDER BY g.creation_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the results
$groups = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $groups[] = $row;
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
    <title>Your Groups</title>
    <link rel="stylesheet" type="text/css" href="./css/admin_edit_groups.css">
</head>
<body>
    <h1>Your Groups</h1>
    <div>
        <?php if (count($groups) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Group Name</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Created On</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
    <?php foreach ($groups as $group): ?>
        <tr>
            <td><?php echo htmlspecialchars($group['group_name']); ?></td>
            <td><?php echo htmlspecialchars($group['description']); ?></td>
            <td><?php echo htmlspecialchars($group['cathegory']); ?></td>
            <td><?php echo htmlspecialchars($group['creation_date']); ?></td>
            <td><?php echo htmlspecialchars($group['role']); ?></td>
            <td>
                <form action="withdraw_group.php" method="POST" style="display:inline;">
                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                    <button type="submit" name="withdraw" class="btn-withdraw">Withdraw</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
            </table>
        <?php else: ?>
            <p>You are not a member of any groups yet.</p>
        <?php endif; ?>
    </div>


     <!-- Display Groups the user is not a part of with "Join" button -->
     <div>
        <h2>Available Groups to Join</h2>
        <?php if (count($not_joined_groups) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Group Name</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Created On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($not_joined_groups as $group): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($group['group_name']); ?></td>
                            <td><?php echo htmlspecialchars($group['description']); ?></td>
                            <td><?php echo htmlspecialchars($group['cathegory']); ?></td>
                            <td><?php echo htmlspecialchars($group['creation_date']); ?></td>
                            <td>
                                <form action="join_group.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                    <button type="submit" name="join" class="btn-join">Join</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>There are no other groups to join at the moment.</p>
        <?php endif; ?>
    </div>

</body>
</html>
