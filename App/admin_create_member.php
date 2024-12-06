<?php
include("db_config.php");
include("header.php");
include('sidebar.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type = "text/css" href="./css/admin_create_member.css"/>
<head>
    <meta charset="UTF-8">
    <title>Admin Create users</title>
</head>
<?php
session_start();

// Check if the user is logged in and has the "admin" role
if (!isset($_SESSION['loggedin']) || $_SESSION['privilege_level'] !== 'administrator') {
    // Redirect to an error page or homepage if not authorized
    header("Location: homepage.php"); 
    exit;
}


// Create a database connection
$conn = new mysqli($host, $user, $pass, $db);

// If the form is submitted, update the member's data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_signup'])) {
    $new_username = $_POST['new_username'];
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $password = $_POST['password'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if email already exists
        $stmt = $pdo->prepare('SELECT * FROM members WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $emailExists = $stmt->fetch();

        // Check if username already exists
        $stmt = $pdo->prepare('SELECT * FROM members WHERE username = :new_username');
        $stmt->execute(['new_username' => $new_username]);
        $usernameExists = $stmt->fetch();

        //JavaScript <script> tag
        if ($emailExists) {
            echo "<script>alert('Email is already in use. Please use a different email address.');</script>";
        } elseif($usernameExists) {
            echo "<script>alert('Username is already in use. Please use a different username.');</script>";
        } else {
            // Proceed with inserting the new member if email is unique
            $stmt = $pdo->prepare('INSERT INTO members (username,first_name, last_name, email, password,privilege_level,status) VALUES (:username,:firstName, :lastName, :email, :password,"junior","active")');
            $stmt->execute([
                'username' => $new_username,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'password' => $password // store the password in plaintext, like a retard
            ]);

            echo "<script>alert('Sign-up successful!');</script>";
            header("Location: admin_manage_users.php");
        }
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>Create Member</title>
</head>
<body>
<div class="main-content">
    <h1>Create Member</h1>
    <p>This section is only visible to admin users.</p>
<!-- Hidden part of the form for sign-up details -->
<form id="signupForm"  method="POST">
    <h2>Sign-Up Details</h2>
    <label for="new_username">Username:</label>
    <input type="text" id="new_username" name="new_username" required>
    
    <label for="firstName">First Name:</label>
    <input type="text" id="firstName" name="firstName" required>

    <label for="lastName">Last Name:</label>
    <input type="text" id="lastName" name="lastName" required>

    <label for="email">Email:</label>
    <input type="text" id="email" name="email" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <button type="submit" name="submit_signup">Sign Up</button>
</form>

</div>
</html>