<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        /* Night Mode Styles */
        body {
            background-color: #1a1a1a;
            color: #e0e0e0;
            font-family: Arial, sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        .container {
            max-width: 400px;
            margin: auto;
            padding: 50px;
            text-align:center;
            border: 1px solid #444;
            border-radius: 40px;
            background-color: #2a2a2a;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background-color: #333;
            color: #e0e0e0;
            border: 1px solid #555;
            border-radius: 5px;
        }
        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px; /* Spacing between buttons */
            margin-top: 10px;
        }
        button {
            padding: 10px 20px;
            border: none;
            background-color: #666;
            color: #ffffff;
            border-radius: 5px;
            cursor: pointer;
            flex: 1; /* Optional: makes both buttons the same width */
        }
        button:hover {
            background-color: #555;
        }
    </style>
</head>


<body>

<div class="container">
    <h2>Welcome to COSN!</h2>
    <h3>Please Login</h3>

    <?php
	session_start();
	$dbServername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbName = "cosn";
	
	
	
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Create a database connection
    $conn = new mysqli($dbServername, $dbUsername, $dbPassword, $dbName);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute a SQL query to fetch the user
    $stmt = $conn->prepare("SELECT member_id, password, privilege_level FROM members WHERE username = ?");
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Bind the result and fetch the user's ID and plain text password
        $stmt->bind_result($member_id, $storedPassword,$privilegeLevel);
        $stmt->fetch();

        // Directly compare the entered password with the stored password
        if ($inputPassword === $storedPassword) {
            $_SESSION['loggedin'] = true;
            $_SESSION['member_id'] = $member_id; // Store the user's ID in the session
            $_SESSION['member_username'] = $inputUsername; // Store the username in the session
            $_SESSION['privilege_level'] = $privilegeLevel; // Store the username in the session
            header("Location: homepage.php");
            exit;
        } else {
            // Javascript is generated on-the-fly by PHP, by using the echo(printing into HTMO document) the <script> HTML tag 
            // Javascript is used to be able to display the alert() popup, which is not possible to do in PHP -- yes, really
             echo "<script>alert('Invalid username or password!');</script>";
        }
    } else {
        echo "<script>alert('Invalid username or password!');</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<form method="POST" action="index.php">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

<div class="button-container">
<!-- Login Button -->
<button type="submit">Login</button>

<!-- Sign Up Button with JavaScript for inline functionality -->
<button type="button" onclick="window.location.href='cosn_sign_up.php'">Sign Up</button>

</div>

</body>
</html>
