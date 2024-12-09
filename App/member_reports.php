<?php
    session_start();
    include("db.php");
    include("header.php");
    include('sidebar.php');

    if (!isset($_SESSION['loggedin'])) {
        header("Location: index.php");
        exit;
    }

    $selected_type = isset($_GET['type']) ? $_GET['type'] : 'interest';
    $member_id = $_SESSION['member_id'];

    // Get categories for selected type
    $sql = "SELECT 
                mc.category_id,
                mc.category_name,
                COUNT(mca.member_id) as member_count
            FROM member_categories mc
            LEFT JOIN member_category_assignments mca ON mc.category_id = mca.category_id
            WHERE mc.category_type = :type
            GROUP BY mc.category_id, mc.category_name
            ORDER BY mc.category_name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['type' => $selected_type]);
    $categories = $stmt->fetchAll();

    // If category is selected, get members in that category
    $selected_category = isset($_GET['category']) ? $_GET['category'] : null;
    $members = [];
    
    if ($selected_category) {
        $sql = "SELECT 
                    m.member_id,
                    m.username,
                    m.first_name,
                    m.last_name,
                    m.email
                FROM members m
                JOIN member_category_assignments mca ON m.member_id = mca.member_id
                WHERE mca.category_id = :category_id
                ORDER BY m.username";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['category_id' => $selected_category]);
        $members = $stmt->fetchAll();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Member Reports</title>
        <link rel="stylesheet" type="text/css" href="member_reports.css">
    </head>
    <body>
    <div class="container">
    <h1>Member Reports</h1>
    
    <div class="type-selector">
        <a href="?type=interest" class="type-button <?= $selected_type == 'interest' ? 'active' : '' ?>">Interests</a>
        <a href="?type=age_group" class="type-button <?= $selected_type == 'age_group' ? 'active' : '' ?>">Age Groups</a>
        <a href="?type=profession" class="type-button <?= $selected_type == 'profession' ? 'active' : '' ?>">Professions</a>
        <a href="?type=region" class="type-button <?= $selected_type == 'region' ? 'active' : '' ?>">Regions</a>
    </div>

    <div class="report-container">
        <div class="categories-list">
            <h2><?= ucfirst(str_replace('_', ' ', $selected_type)) ?></h2>
            <?php foreach ($categories as $category): ?>
                <a href="?type=<?= $selected_type ?>&category=<?= $category['category_id'] ?>" 
                   class="category-item <?= $selected_category == $category['category_id'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($category['category_name']) ?>
                    <span class="count">(<?= $category['member_count'] ?>)</span>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($selected_category && $members): ?>
            <div class="members-list">
                <h2>Members</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td><?= htmlspecialchars($member['username']) ?></td>
                                <td><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></td>
                                <td><?= htmlspecialchars($member['email']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

    </body>
</html>