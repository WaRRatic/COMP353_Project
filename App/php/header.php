<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" type = "text/css" href="../css/main.css">

    <title><?= $title ?></title>
</head>

<body>
<div class="topnav">
    <a href="index.php"><img src="../logos/images.png" alt="Logo" style="height: 80px; width: auto;"></a>

    <div class="search-container">
        <form action="index.php">
            <input type="text" placeholder="Search" name="SEARCH">
            <button type = "submit">Search</button>
        </form>
    </div>

    <div class="auth-buttons">
        <a href="index.php" class="btn signup-btn">Logout</a>
    </div>
</div>


</body>

</html>
