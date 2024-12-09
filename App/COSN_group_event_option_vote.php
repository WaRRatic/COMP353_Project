<?php
session_start();
include("db_config.php");

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
$logged_in_member_id = $_SESSION['member_id'];

// Check if the user is logged in
if (!isset($_GET['group_event_option_id'])) {
    echo "<script>alert('no group_event_option_id provided !');</script>";
    echo "<script>window.location.href = 'homepage.php';</script>";
    exit;
}
$group_event_option_id = $_GET['group_event_option_id'];

// Check if the user is logged in
if (!isset($_GET['group_event_id'])) {
    echo "<script>alert('no group_event_id provided !');</script>";
    echo "<script>window.location.href = 'homepage.php';</script>";
    exit;
}
$group_event_id = $_GET['group_event_id'];


//delete first votes by this member for this option, to avoid cheating
$sql = "
DELETE FROM kpc353_2.group_event_option_vote
WHERE option_voter_member_id = :member_id 
    AND target_group_event_option_id = :group_event_option_id 
";

$Stmt = $pdo->prepare($sql);
$Stmt->execute(['member_id' => $logged_in_member_id, 'group_event_option_id' => $group_event_option_id]);

//Insert the vote
$sql = "
INSERT INTO kpc353_2.group_event_option_vote
(option_voter_member_id, target_group_event_option_id, option_voting_decision)
VALUES (:member_id, :group_event_option_id, 1)
";

$Stmt = $pdo->prepare($sql);
$Stmt->execute(['member_id' => $logged_in_member_id, 'group_event_option_id' => $group_event_option_id]);

echo "<script>alert('You voted on this option! Only one vote allowed per member, don't try to cheat');</script>";
echo "<script>window.location.href = 'event_vote.php?group_event_id=". $group_event_id ."';</script>";
?>