<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="../css/index.css" />
<head>
    <meta charset="UTF-8">
    <title>Organize Event</title>
    
</head> 

<?php
session_start();

include('sidebar.php');
include('header.php');

//set the logged in member id
$logged_in_member_id = $_SESSION['member_id'];


// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit;
}


// Database connection parameters
$host = 'localhost';
$db   = 'cosn';
$user = 'root';
$pass = '';


// Set up DSN and options
$dsn = "mysql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// Create a PDO instance
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     exit('Database connection failed: ' . $e->getMessage());
}

//Fetch events for groups that the user is part of
$sql_events = "SELECT e.event_id, e.title, e.description, e.status, e.created_at, g.group_name
               FROM events e
               JOIN groups g ON e.group_id = g.group_id
               WHERE g.group_id IN
              (SELECT group_id FROM group_members WHERE member_id = :user_id)";
$stmt_events = $pdo->prepare($sql_events);
$stmt_events->execute(['user_id' => $logged_in_member_id]);
$events = $stmt_events->fetchAll();

//Code for user to vote
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote_option_id'])) 
{
    $vote_option_id = $_POST['vote_option_id'];
    $voter_id = $_SESSION['member_id'];

    //Verify that the user has not voted yet
    $sql_check_vote = "SELECT *
                    FROM event_votes
                    WHERE voter_id = :voter_id AND option_id = :option_id";
    $stmt_check_vote = $pdo->prepare($sql_check_vote);
    $stmt_check_vote->execute(['voter_id' => $voter_id, 'option_id' => $vote_option_id]);
    if ($stmt_check_vote->rowCount() > 0) 
    {
        echo "You have already voted for this option.";
    } 
    else 
    {
        // Record the vote
        $sql_vote = "INSERT INTO event_votes (option_id, voter_id) VALUES (:option_id, :voter_id)";
        $stmt_vote = $pdo->prepare($sql_vote);
        $stmt_vote->execute(['option_id' => $vote_option_id, 'voter_id' => $voter_id]);
        echo "Vote recorded!";
    }
}

//Code to get event details
    $event_id = $_GET['event_id']; 

    $sql_event = "SELECT * 
                  FROM events
                  Where event_id = :event_id";
    $stmt_event = $pdo->prepare($sql_event);
    $stmt_event->execute(['event_id' => $event_id]);
    $event = $stmt_event->fetch();

    if (!$event) {
        die("Event not found.");
    }
// Fetch the proposed location, date, time of the event, and group name
$sql_details = "SELECT 
                eventOption.option_id,
                eventOption.time,
                eventOption.date,
                eventOption.location,
                m.username AS suggested_by,
                COUNT(ev.vote_id) AS vote_count,
                g.group_name
                FROM event_details eventOption
                LEFT JOIN event_votes ev ON eventOption.option_id = ev.option_id
                INNER JOIN events e ON eventOption.event_id = e.event_id
                INNER JOIN groups g ON e.group_id = g.group_id
                INNER JOIN members m ON eventOption.suggested_by = m.member_id
                WHERE eventOption.event_id = :event_id
                GROUP BY eventOption.option_id, g.group_name, m.username, eventOption.time, eventOption.date, eventOption.location";
$stmt_details = $pdo->prepare($sql_details);
$stmt_details->execute(['event_id' => $event_id]);
$details = $stmt_details->fetchAll();


//User suggests alternate details for event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['suggestion'])) 
{
    $date = $_POST['date'];
    $time = $_POST['time'];
    $place = $_POST['place'];
    $suggested_by = $_SESSION['member_id'];

    $sql_suggestion = "INSERT 
                       INTO event_options (event_id, date, time, place, suggested_by) 
                       VALUES (:event_id, :date, :time, :place, :suggested_by)";
    $stmt_suggestion = $pdo->prepare($sql_suggestion);
    $stmt_suggestion->execute([
        'event_id' => $event_id,
        'date' => $date,
        'time' => $time,
        'place' => $place,
        'suggested_by' => $suggested_by
    ]);
    echo "Suggestion added!";
}


//Code to create a new event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event'])) 
{
    $group_id = $_POST['group_id']; // ID of the group for which the event is being created
    $creator_id = $_SESSION['member_id']; // Logged-in user's ID
    $title = $_POST['title'];             //Title of the vent
    $description = $_POST['description']; //Description of the event

    // Verify that the user belongs to the selected group
    $sql_verify_group = "SELECT 1 FROM group_members WHERE group_id = :group_id AND member_id = :user_id";
    $stmt_verify_group = $pdo->prepare($sql_verify_group);
    $stmt_verify_group->execute(['group_id' => $group_id, 'user_id' => $creator_id]);

    if ($stmt_verify_group->rowCount() === 0) 
    {
        echo "Invalid group selection.";
        exit;
    }    

    // Insert the event into the events table
    $sql_create_event = "
        INSERT INTO events (group_id, creator_id, title, description)
        VALUES (:group_id, :creator_id, :title, :description)";
    $stmt_create_event = $pdo->prepare($sql_create_event);
    $stmt_create_event->execute([

        'group_id' => $group_id,
        'creator_id' => $creator_id,
        'title' => $title,
        'description' => $description
    ]);

    //After creating the event, we update the current events so that it also shows up
    $sql_events_updated = "SELECT * FROM events WHERE group_id = :group_id";
    $stmt_events_updated = $pdo->prepare($sql_events_updated);
    $stmt_events_updated->execute(['group_id' => $group_id]);
    $events = $stmt_events_updated->fetchAll();

    echo "Event created successfully!";
}

// Code to finalize voting (if creator)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalize_event']) && isset($event)) 
{
    // Check if the logged-in user is the creator of the event
    if ($event['creator_id'] == $_SESSION['member_id']) 
    {
        // Update the event status to 'finalized'
        $sql_update_status = "UPDATE events SET status = 'finalized' WHERE event_id = :event_id";
        $stmt_update_status = $pdo->prepare($sql_update_status);
        $stmt_update_status->execute(['event_id' => $event_id]);
        echo "Voting has been closed. Event status is now finalized.";
    }
}

?>


<!-- Front end for event voting and event creating -->
<body>

<h2>Events:</h2>
<?php if (!empty($events)): ?>
    <?php foreach ($events as $current_event): ?>
        <h3><?php echo htmlspecialchars($current_event['title']); ?></h3>
        <p><?php echo htmlspecialchars($current_event['description']); ?></p>
        <p><strong>Group:</strong> <?php echo htmlspecialchars($current_event['group_name']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($current_event['status']); ?></p> 
    <?php endforeach; ?>
<?php else: ?>
    <p>No events created yet for any group.</p>
<?php endif; ?>

<?php if (isset($event)): ?>
    <?php if ($event['creator_id'] == $_SESSION['member_id'] && $event['status'] !== 'finalized'): ?>
        <form method="post">
            <button type="submit" name="finalize_event">Finalize event</button>
        </form>
    <?php endif; ?>
<?php endif; ?>


<h1>Event: <?php echo htmlspecialchars($event['title']); ?></h1>
<p>Description: <?php echo htmlspecialchars($event['description']); ?></p>

    <?php if (isset($details) && !empty($details)): ?>
    <h2>Vote for an Event:</h2>
    <form method="post">
        <?php foreach ($details as $option): ?>
            <label>
                <input type="radio" name="vote_option_id" value="<?php echo $option['option_id']; ?>">
                Group: <?php echo htmlspecialchars($option['group_name']); ?>,
                Date: <?php echo htmlspecialchars($option['date']); ?>,
                Time: <?php echo htmlspecialchars($option['time']); ?>,
                Place: <?php echo htmlspecialchars($option['place']); ?>
                (Suggested by <?php echo htmlspecialchars($option['suggested_by']); ?>, Votes: <?php echo $option['vote_count']; ?>)
            </label><br>
        <?php endforeach; ?>
        <button type="submit">Vote</button>
    </form>
<?php else: ?>   
    <p>No options available for voting.</p>
<?php endif; ?>

<?php if (isset($event)): ?>
    <h2>Suggest an Alternate Option:</h2>
    <form method="post">
        <label>Date: <input type="date" name="date" required placeholder="yyyy-mm-dd"></label><br>
        <label>Time: <input type="time" name="time" required placeholder="00:00"></label><br>
        <label>Place: <input type="text" name="place" required placeholder="Enter Location"></label><br>
        <button type="submit" name="suggestion">Suggest</button>
    </form>
<?php else: ?>
    <p>No event selected. Please select an event to suggest alternate options.</p>
<?php endif; ?> 


<h1>Create a New Event</h1>
<form method="post">
    <label for="group_id">Group:</label>
    <select name="group_id" id="group_id" required>
        <?php
        // Fetch and display groups the user belongs to
        $user_id = $_SESSION['member_id'];
        $sql_groups = "SELECT group_id, group_name FROM groups WHERE group_id IN 
                      (SELECT group_id FROM group_members WHERE member_id = :user_id)";
        $stmt_groups = $pdo->prepare($sql_groups);
        $stmt_groups->execute(['user_id' => $user_id]);
        $groups = $stmt_groups->fetchAll();
        foreach ($groups as $group) {
            echo "<option value='{$group['group_id']}'>{$group['group_name']}</option>";
        }
        ?>
    </select><br>

    <label for="title">Event Title:</label>
    <input type="text" name="title" id="title" required><br>

    <label for="description">Description:</label>
    <textarea name="description" id="description" required></textarea><br>

    <button type="submit" name="create_event">Create Event</button>
</form>

</body>



</html>