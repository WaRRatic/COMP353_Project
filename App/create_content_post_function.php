<?php
function createContentPost_function($member_id, $content_type, $content_title, $content_data) {
    try {
        $dbServername = "localhost";
        $dbUsername = "root";
        $dbPassword = "";
        $dbName = "cosn";
        
        $pdo = new PDO("mysql:host=$dbServername;dbname=$dbName", $dbUsername, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare('INSERT INTO content (creator_id, content_type, content_data, content_creation_date, content_title, moderation_status) VALUES (:member_id, :content_type, :content_data, NOW(), :content_title, "pending")');

        $stmt->execute([
            'member_id' => $member_id,
            'content_type' => $content_type,
            'content_data' => $content_data,
            'content_title' => $content_title
        ]);

        echo "<script>alert('Post created successfully! Post will be visible after passing moderation.');</script>";
    } catch(PDOException $e) {
        echo "<script>
            alert('A database error occurred: ' + " . json_encode($e->getMessage()) . ");
            console.error('Database Error: ' + " . json_encode($e->getMessage()) . ");
        </script>";
    }
}