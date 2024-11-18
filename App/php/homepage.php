<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
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
        button {
            padding: 10px 20px;
            border: none;
            background-color: #666;
            color: #ffffff;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #555;
        }
    </style>
</head>

<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
</head>
<body>
<h1>Welcome to <?php echo $_SESSION['member_username']; ?> homepage!</h1>
    <p>You are now logged in.</p>

    <!-- Display this element only if the role is "admin" -->
<?php if ($_SESSION['privilege_level'] === 'administrator'): ?>
    <div style="border: 1px solid black; padding: 10px; margin: 10px;">
        <h2>Admin Panel</h2>
        <p>This section is only visible to admin users.</p>
        <ul>
            <li><a href="admin_manage_users.php">Manage COSN users</a></li>
            <li><a href="admin_manage_groups.php">Manage COSN groups</a></li>
            <li><a href="admin_post_public.php">Make a public post</a></li>
        </ul>

        

    </div>
<?php endif; ?>


    <a href="index.php">Logout</a>
</body>
</html>
