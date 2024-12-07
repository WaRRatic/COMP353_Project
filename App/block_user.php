<?php
session_start();
include 'db_config.php'; //include database connection

if (isset($_POST['blocked_id'])) 
{
    $blocker_id = $_SESSION['user_id'];
    $blocked_id = $_POST['blocked_id'];

    $stmt = $conn->prepare("SELECT * 
                            FROM cosn.member_realtionships
                            WHERE (origin_member_id = ? AND target_member_id = ?
                            OR origin_member_id = ? AND target_member_id = ?)
                            AND member_relationshp_type = 'blocked'");

    $stmt->bind_param("iiii", $blocker_id, $blocked_id, $blocked_id, $blocker_id);
    $stmt->execute();
    
    $result = $stmt->get_result();

    //check if the user already blocked

    if ($result->num_rows > 0) 
    {
        echo "User already blocked";
    }
    else 
    {
        $stmt = $conn->prepare("INSERT INTO consn.member_relationships (origin_member_id, target_memeber_id, member_relationship_type, member_relationship_status)
                                VALUES (?,?,'blocked','approved')");

        $stmt-> bind_param("ii", $blocker_id, $blocked_id);
        $stmt-> execute();

        echo "User blocked";
    }
}
?>
