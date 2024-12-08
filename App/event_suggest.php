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
$group_event_id = $_GET['group_event_id'];

// Create a database connection
$conn = new mysqli('localhost', 'root', '', 'kpc353_2');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_group_event_id = $_POST['group_event_id'];
    $option_owner_member_id = $_SESSION['member_id'];
    $option_description = $_POST['option_description'];

    if ($option_owner_member_id > 0 && !empty($option_description)) {
        $query = $conn->prepare("INSERT INTO group_event_options (group_event_options_id, target_group_event_id, option_owner_member_id, option_description) VALUES (?, ?, ?, ?)");
        $query->bind_param("iis", $target_group_event_id, $option_owner_member_id, $option_description);

    if ($query->execute()) {
        echo "Your suggestion has been submitted successfully.";
        echo "<script>window.location.href = 'events.php';</script>"; 
    }
    else {
        echo "Failed to submit your suggestion.";
    }
    } else {
        echo "Invalid input.";
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suggest for Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #222;
            color: #fff;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #333;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        textarea {
            width: 92%;
            padding: 10px;
            border-radius: 5px;
            border: none;
            margin-bottom: 20px;
            background-color: #444;
            color: #fff;
        }

        button {
            padding: 10px 20px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1>Suggest for Event</h1>
    <form method="POST" action="event_suggest.php">
        <label for="suggestion">Your Suggestion (Date, Time, Location):</label>
        <textarea id="option_description" name="option_description" rows="3" placeholder="Suggest a date, time, and location for this event..." required></textarea>
        <button type="submit">Submit Suggestion</button>
    </form>
</body>
</html>

