<?php
include("db_config.php");
include("header.php");
include('sidebar.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/admin_manage_users.css" />
<head>
    <meta charset="UTF-8">
    <title>Vote on Events</title>
</head>
<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}


// Create a database connection
$conn = new mysqli($host, $user, $pass, $db);

$memberId = $_SESSION['member_id'];

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if ($event_id > 0) {
    // Fetch event details
    $event_query = $conn->prepare("SELECT event_name FROM group_event WHERE id = ?");
    $event_query->bind_param("i", $event_id);
    $event_query->execute();
    $event_result = $event_query->get_result();
    $event = $event_result->fetch_assoc();

    // Fetch event options
    $options_query = $conn->prepare("SELECT id, event_date, event_time, event_location, votes FROM event_options WHERE event_id = ?");
    $options_query->bind_param("i", $event_id);
    $options_query->execute();
    $options_result = $options_query->get_result();
} else {
    die("Invalid event ID.");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote on Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #222;
            color: #fff;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .options-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #333;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .option {
            margin-bottom: 15px;
            padding: 15px;
            background-color: #444;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .option button {
            padding: 10px 20px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }

        .option button:hover {
            background-color: #218838;
        }

        .option-info {
            flex-grow: 1;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <h1>Vote on Event: <?= htmlspecialchars($event['event_name']) ?></h1>
    <div class="options-container">
        <?php while ($option = $options_result->fetch_assoc()): ?>
            <div class="option">
                <div class="option-info">
                    <strong>Date:</strong> <?= htmlspecialchars($option['event_date']) ?><br>
                    <strong>Time:</strong> <?= htmlspecialchars($option['event_time']) ?><br>
                    <strong>Location:</strong> <?= htmlspecialchars($option['event_location']) ?><br>
                    <strong>Votes:</strong> <?= htmlspecialchars($option['votes']) ?>
                </div>
                <form method="post" action="vote.php">
                    <input type="hidden" name="option_id" value="<?= $option['id'] ?>">
                    <button type="submit">Vote</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>