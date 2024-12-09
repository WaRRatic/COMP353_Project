<?php
    session_start();
    include("db_config.php");
    include("header.php");
    include('sidebar.php');

    if (!isset($_SESSION['loggedin']) || $_SESSION['privilege_level'] !== 'administrator') {
        header("Location: index.php");
        exit;
    }

    $member_id = $_SESSION['member_id'];
    $selected_type = isset($_GET['type']) ? $_GET['type'] : 'interest';

    // Handle category creation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
        $sql = "INSERT INTO member_categories (category_type, category_name) 
                VALUES (:type, :name)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'type' => $selected_type,
            'name' => $_POST['category_name']
        ]);
    }

    // Handle category deletion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
        $sql = "DELETE FROM member_category_assignments WHERE category_id = :category_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['category_id' => $_POST['category_id']]);

        $sql = "DELETE FROM member_categories WHERE category_id = :category_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['category_id' => $_POST['category_id']]);
    }

    // Get all categories of selected type
    $sql = "SELECT 
                mc.category_id,
                mc.category_name,
                COUNT(mca.member_id) as member_count
            FROM kpc353_2.member_categories mc
            LEFT JOIN kpc353_2.member_category_assignments mca ON mc.category_id = mca.category_id
            WHERE mc.category_type = :type
            GROUP BY mc.category_id, mc.category_name
            ORDER BY mc.category_name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['type' => $selected_type]);
    $categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Manage Categories</title>
        <link rel="stylesheet" type="text/css" href="member_reports.css">
    </head>
    <body>
        <div class="container">
            <h1>Manage Member Categories</h1>
            
            <div class="type-selector">
                <a href="?type=interest" class="type-button <?= $selected_type == 'interest' ? 'active' : '' ?>">Interests</a>
                <a href="?type=age_group" class="type-button <?= $selected_type == 'age_group' ? 'active' : '' ?>">Age Groups</a>
                <a href="?type=profession" class="type-button <?= $selected_type == 'profession' ? 'active' : '' ?>">Professions</a>
                <a href="?type=region" class="type-button <?= $selected_type == 'region' ? 'active' : '' ?>">Regions</a>
            </div>

            <div class="category-management">
                <h2>Add New <?= ucfirst(str_replace('_', ' ', $selected_type)) ?></h2>
                <form method="POST" class="add-category-form">
                    <input type="text" name="category_name" placeholder="Category Name" required>
                    <button type="submit" name="add_category">Add Category</button>
                </form>

                <h2>Existing Categories</h2>
                <div class="categories-list">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-item">
                            <span class="category-name"><?= htmlspecialchars($category['category_name']) ?></span>
                            <span class="member-count">(<?= $category['member_count'] ?> members)</span>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>">
                                <button type="submit" name="delete_category" class="delete-button" 
                                        onclick="return confirm('Are you sure you want to delete this category?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </body>
</html>