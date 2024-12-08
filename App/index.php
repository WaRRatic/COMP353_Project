<?php
session_start();
include("header.php");
include("db_config.php");


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];
    
    $sql = $pdo->prepare('SELECT member_id, password, privilege_level FROM kpc353_2.members WHERE username = :username');
    $sql->execute(['username' => $inputUsername]);
    $sql_result = $sql->fetch(PDO::FETCH_ASSOC);

    if ($sql_result) {
        // Bind the result and fetch the user's ID and plain text password
        $member_id = $sql_result['member_id'];
        $storedPassword = $sql_result['password'];
        $privilegeLevel = $sql_result['privilege_level'];
        
        // Directly compare the entered password with the stored password
        if ($inputPassword === $storedPassword) {
            $_SESSION['loggedin'] = true;
            $_SESSION['member_id'] = $member_id; // Store the user's ID in the session
            $_SESSION['member_username'] = $inputUsername; // Store the username in the session
            $_SESSION['privilege_level'] = $privilegeLevel; // Store the username in the session
            
            echo "<script>alert('Sign-in successful!');</script>";
            echo "<script>window.location.href = 'homepage.php';</script>";
            exit;
        } else {
            // Javascript is generated on-the-fly by PHP, by using the echo(printing into HTMO document) the <script> HTML tag 
            // Javascript is used to be able to display the alert() popup, which is not possible to do in PHP -- yes, really
             echo "<script>alert('Invalid username or password!');</script>";
             echo "<script>window.location.href = 'homepage.php';</script>";
             exit;
        }
    } else {
        echo "<script>alert('Invalid username or password!');</script>";
        echo "<script>window.location.href = 'homepage.php';</script>";
        exit;
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="index.css" />
<body>

<div class="container">
    <h2>Welcome to COSN!</h2>
    <h3>Please Login</h3>


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
