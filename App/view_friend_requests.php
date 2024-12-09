<?php
session_start();
include 'db.php'; // Include database connection


$user_id = $_SESSION['user_id'];

// Fetch friend requests sent to the logged-in user (receiver)
$stmt = $conn->prepare("SELECT mr.relationship_id, mm1.member_id AS sender_id, mm1.username AS sender_name
                        FROM kpc353_2.member_relationships mr
                        JOIN kpc353_2.members mm1 ON mm1.member_id = mr.origin_member_id
                        WHERE mr.target_member_id = ? AND mr.member_relationship_type = 'friend' AND mr.member_relationship_status = 'requested'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Display the requests
if ($result->num_rows > 0) {
    echo "<h3>Friend Requests</h3>";
    echo "<table>";
    echo "<tr><th>Sender</th><th>Action</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['sender_name']) . "</td>";
        echo "<td>
                <form action='respond_friend_request.php' method='POST' style='display:inline;'>
                    <input type='hidden' name='request_id' value='" . $row['relationship_id'] . "'>
                    <button type='submit' name='status' value='approved' class='btn-accept'>Accept</button>
                    <button type='submit' name='status' value='rejected' class='btn-reject'>Reject</button>
                </form>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No pending friend requests!";
}

$conn->close();
?>
