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
            padding: 20px;
            text-align: center;
            border: 1px solid #444;
            border-radius: 5px;
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
        /* Styling omitted for brevity */
        .hidden {
            display: none;
        }
    </style>
</head>



<body>

<div class="container">

<!-- First part of the form to verify existing member -->
<form id="verificationForm" method="POST">
    <h2>Do you know a COSN member?</h2>
    <label for="username">Existing COSN Member Name:</label>
    <input type="text" id="username" name="username" required>
    
    <label for="member_id">Existing COSN Member ID:</label>
    <input type="text" id="member_id" name="member_id" required>

    <button type="submit">Verify Member</button>
</form>

<!-- Hidden part of the form for sign-up details -->
<form id="signupForm" class="hidden"  method="POST">
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

<?php
    //catch PHP errors
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

	session_start();
	$dbServername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbName = "cosn";
	
	
	
// Check if the form is submitted
//this is done by catching the "POST" request method and checking that 'username' and 'member_id' variables were POSTed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['member_id'])) {
    $username = $_POST['username'];
    $member_id = $_POST['member_id'];

     
    $pdo = new PDO("mysql:host=$dbServername;dbname=$dbName", $dbUsername, $dbPassword);
    //configure the error handling mode for the PDO (PHP Data Objects) database connection.
    //throw an exception when a database error occurs.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to check if member exists
    $stmt = $pdo->prepare('SELECT member_id, username FROM members WHERE username = :username AND member_id = :member_id');
    $stmt->execute(['username' => $username, 'member_id' => $member_id]);
    $result = $stmt->fetch();
    try{
        if ($result) {
            // Use JavaScript to reveal the signup form if verification is successful
            echo "<script>
                    document.getElementById('signupForm').classList.remove('hidden');
                    document.getElementById('verificationForm').classList.add('hidden');
                </script>";
        } else {
            //if the member_id and username entered do not match what can be find in the database, then throw a warning and ask to try again
            echo "<script>alert('Invalid member name or ID. Please try again.');</script>";
        }
    } 
    catch (PDOException $e) {
        echo "<script>alert('Database error: {$e->getMessage()}');</script>";
    }
}

 // Sign-up form logic to check for existing email
//this is done by catching the "POST" request method and checking that the 'submit_signup' variable was POSTed
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_signup'])) {
    $new_username = $_POST['new_username'];
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $password = $_POST['password'];

    try {
        $pdo = new PDO("mysql:host=$dbServername;dbname=$dbName", $dbUsername, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if email already exists
        $stmt = $pdo->prepare('SELECT * FROM members WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $emailExists = $stmt->fetch();

        // Check if username already exists
        $stmt = $pdo->prepare('SELECT * FROM members WHERE username = :new_username');
        $stmt->execute(['new_username' => $new_username]);
        $usernameExists = $stmt->fetch();

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
        }
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}

?>


</body>
</html>
