<?php
session_start();
include("db_config.php");

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Access denied - login first!');</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
$logged_in_member_id = $_SESSION['member_id'];


// Check if the target_member_id is set
if (!isset($_GET['target_member_id'])) {
    echo "<script>alert('No target_member_id is specified!');</script>";
    echo "<script>window.location.href = 'COSN_members.php';</script>";
    exit;
}
$target_member_id = $_GET['target_member_id'];

// Check if the rel_type is set
if (!isset($_GET['rel_type'])) {
    echo "<script>alert('No rel_type is specified!');</script>";
    echo "<script>window.location.href = 'COSN_members.php';</script>";
    exit;
}
$rel_type = $_GET['rel_type'];

//validate rel_type -- valid values:
// - friend
// - family
// - colleague
// - blocked
// - unblock
if($rel_type !== 'friend' && $rel_type !== 'family' && $rel_type !== 'colleague' && $rel_type !== 'blocked' && $rel_type !== 'unblock'){
    echo "<script>alert('Invalid rel_type is specified!');</script>";
    echo "<script>window.location.href = 'COSN_members.php';</script>";
    exit;
}

// Check if the rel_status is set
if (!isset($_GET['rel_status'])) {
    echo "<script>alert('No rel_status is specified!');</script>";
    echo "<script>window.location.href = 'COSN_members.php';</script>";
    exit;
}
$rel_status = $_GET['rel_status'];

//validate rel_status -- valid values:
// - requested: indicates that logged in user requested the relationship with the target user
// - remove: indicates that logged in user wants to remove the relationship with the target user
// - unblock: indicates that logged in user wants to unblock the target user
// - approved: indicates that the relationship between the two users is approved
if($rel_status !== 'requested' && $rel_status !== 'remove' && $rel_status !== 'approved' && $rel_status !== 'blocked' && $rel_status !== 'unblock' && $rel_status !== 'rejected'){
    echo "<script>alert('Invalid rel_status is specified!');</script>";
    echo "<script>window.location.href = 'COSN_members.php';</script>";
    exit;
}

//process 'requested' rel_status
if($rel_status === 'requested'){
    // check that the relationship request can be processed
    $sql = "SELECT 1 
            FROM 
                kpc353_2.member_relationships 
            WHERE 
                member_relationship_type = :rel_type
                AND target_member_id = :target_member_id
                AND origin_member_id = :logged_in_member_id
            UNION
            SELECT 1
            FROM 
                kpc353_2.member_relationships 
            WHERE 
                member_relationship_type = :rel_type
                AND target_member_id = :logged_in_member_id
                AND origin_member_id = :target_member_id
                AND member_relationship_status NOT IN ('requested')
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //if any type of relationship already exists between the two users (except of an existing request from the other member), the request cannot be processed
    if($result){
        echo "<script>alert('The relationship request cannot be processed!');</script>";
        echo "<script>window.location.href = 'COSN_members.php';</script>";
        exit;
    }

    // check that the relationship request can be processed
    $sql = "INSERT INTO kpc353_2.member_relationships
                (origin_member_id, target_member_id, member_relationship_type, member_relationship_status)
            VALUES 
                (:logged_in_member_id, :target_member_id, :rel_type, :rel_status)
            ";
     $stmt = $pdo->prepare($sql);
     $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type, ':rel_status' => $rel_status]);
    
     echo "<script>alert('The relationship request has been sent!');</script>";
     echo "<script>window.location.href = 'COSN_member_relationship_manage.php?target_member_id=" . $target_member_id . "';</script>";
}elseif($rel_status === 'blocked'){
    
    $pdo->beginTransaction();
    // Delete all relationships between the target and the logged in member
    $sql = "DELETE FROM kpc353_2.member_relationships
            WHERE 
                origin_member_id = :logged_in_member_id
                AND target_member_id = :target_member_id
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id]);
    
    // Delete all relationships between the logged in member (as target) and the target member (as origin)
    $sql = "DELETE FROM kpc353_2.member_relationships
            WHERE 
                origin_member_id = :target_member_id
                AND target_member_id = :logged_in_member_id
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id]);

    // Insert a new blocked relationship
    $sql = "INSERT INTO kpc353_2.member_relationships
                (origin_member_id, target_member_id, member_relationship_type, member_relationship_status)
            VALUES 
                (:logged_in_member_id, :target_member_id, :rel_type, :rel_status)
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type, ':rel_status' => $rel_status]);

    //commit the transaction
    $pdo->commit();
    
    echo "<script>alert('Member blocked succesfully!');</script>";
    echo "<script>window.location.href = 'COSN_member_relationship_manage.php?target_member_id=" . $target_member_id . "';</script>";
}elseif($rel_status === 'unblock'){
    
    //begin transaction
    $pdo->beginTransaction();
    //deleting all relationships between the target and the logged in member, unblocks the target member
    // Delete all relationships between the target and the logged in member
    $sql = "DELETE FROM kpc353_2.member_relationships
            WHERE 
                origin_member_id = :logged_in_member_id
                AND target_member_id = :target_member_id
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id]);
    
    // Delete all relationships between the logged in member (as target) and the target member (as origin)
    $sql = "DELETE FROM kpc353_2.member_relationships
            WHERE 
                origin_member_id = :target_member_id
                AND target_member_id = :logged_in_member_id
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id]);

    
    //commit the transaction
    $pdo->commit();
    
    echo "<script>alert('Member unblocked succesfully!');</script>";
    echo "<script>window.location.href = 'COSN_member_relationship_manage.php?target_member_id=" . $target_member_id . "';</script>";
}elseif($rel_status === 'approved'){
    //first, check if the relationship request exists between the two users
    $sql = "SELECT 1 
            FROM 
                kpc353_2.member_relationships 
            WHERE 
                member_relationship_type = :rel_type
                AND target_member_id = :target_member_id
                AND origin_member_id = :logged_in_member_id
                AND member_relationship_status = 'requested'
            UNION
            SELECT 1
            FROM 
                kpc353_2.member_relationships 
            WHERE 
                member_relationship_type = :rel_type
                AND target_member_id = :logged_in_member_id
                AND origin_member_id = :target_member_id
                AND member_relationship_status  = 'requested'
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //if no relationship request exists between the two users, the request cannot be processed
    if(!$result){
        echo "<script>alert('The relationship request cannot be processed!');</script>";
        echo "<script>window.location.href = 'COSN_members.php';</script>";
        exit;
    }

    //begin transaction
    $pdo->beginTransaction();

    // First, delete the existing relationship request from the logged in member to the target member
    $sql = "DELETE FROM kpc353_2.member_relationships
            WHERE 
                origin_member_id = :logged_in_member_id
                AND target_member_id = :target_member_id
                AND member_relationship_status = 'requested'
                AND member_relationship_type = :rel_type
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Second, delete the existing relationship request from the target member to the logged in member
    $sql = "DELETE FROM kpc353_2.member_relationships
            WHERE 
                origin_member_id = :target_member_id
                AND target_member_id = :logged_in_member_id
                AND member_relationship_status = 'requested'
                AND member_relationship_type = :rel_type
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Finally insert the approved relationship on both sides
    // First, insert the relationship from the logged in member to the target member
    $sql = "INSERT INTO kpc353_2.member_relationships
                (origin_member_id, target_member_id, member_relationship_type, member_relationship_status)
            VALUES 
                (:logged_in_member_id, :target_member_id, :rel_type, :rel_status)
            "; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type, ':rel_status' => $rel_status]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Second, insert the relationship from the target member, to the logged in member
    $sql = "INSERT INTO kpc353_2.member_relationships
                (origin_member_id, target_member_id, member_relationship_type, member_relationship_status)
            VALUES 
                ( :target_member_id, :logged_in_member_id, :rel_type, :rel_status)
            "; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type, ':rel_status' => $rel_status]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //commit the transaction
    $pdo->commit();

    echo "<script>alert('". $rel_type ." relationship created succesfully!');</script>";
    echo "<script>window.location.href = 'COSN_member_relationship_view.php';</script>";
}elseif($rel_status === 'remove'){
    //to remove the relationships, we  need to remove all the 'approved' relationships between the two users of the specified type
    //first, check if the relationship request exists between the two users
    $sql = "SELECT 1 
            FROM 
                kpc353_2.member_relationships 
            WHERE 
                member_relationship_type = :rel_type
                AND target_member_id = :target_member_id
                AND origin_member_id = :logged_in_member_id
                AND member_relationship_status = 'approved'
            UNION
            SELECT 1
            FROM 
                kpc353_2.member_relationships 
            WHERE 
                member_relationship_type = :rel_type
                AND target_member_id = :logged_in_member_id
                AND origin_member_id = :target_member_id
                AND member_relationship_status  = 'approved'
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //if no relationship exists between the two users, the request cannot be processed
    if(!$result){
        echo "<script>alert('The relationship request cannot be processed!');</script>";
        echo "<script>window.location.href = 'COSN_members.php';</script>";
        exit;
    }

    //begin transaction
    $pdo->beginTransaction();

    // First delete the existing relationship from the logged in member to the target member
    $sql = "DELETE FROM kpc353_2.member_relationships
            WHERE 
                origin_member_id = :logged_in_member_id
                AND target_member_id = :target_member_id
                AND member_relationship_status = 'approved'
                AND member_relationship_type = :rel_type
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Second delete the existing relationship from the target member to the logged in member
    $sql = "DELETE FROM kpc353_2.member_relationships
            WHERE 
                origin_member_id = :target_member_id
                AND target_member_id = :logged_in_member_id
                AND member_relationship_status = 'approved'
                AND member_relationship_type = :rel_type
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //commit the transaction
    $pdo->commit();

    echo "<script>alert('". $rel_type ." relationship removed succesfully!');</script>";
    echo "<script>window.location.href = 'COSN_member_relationship_manage.php?target_member_id=" . $target_member_id . "';</script>";
}elseif($rel_status === 'rejected'){
    //to reject the relationships, we  need to remove all the 'requested' relationships between the two users of the specified type
    //first, check if the relationship request exists between the two users
    $sql = "SELECT 1 
            FROM 
                kpc353_2.member_relationships 
            WHERE 
                member_relationship_type = :rel_type
                AND target_member_id = :target_member_id
                AND origin_member_id = :logged_in_member_id
                AND member_relationship_status = 'requested'
            UNION
            SELECT 1
            FROM 
                kpc353_2.member_relationships 
            WHERE 
                member_relationship_type = :rel_type
                AND target_member_id = :logged_in_member_id
                AND origin_member_id = :target_member_id
                AND member_relationship_status  = 'requested'
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //if no relationship exists between the two users, the request cannot be processed
    if(!$result){
        echo "<script>alert('The relationship request cannot be processed!');</script>";
        echo "<script>window.location.href = 'COSN_members.php';</script>";
        exit;
    }

    //begin transaction
    $pdo->beginTransaction();

    // First, delete the existing relationship request from the logged in member to the target member
    $sql = "DELETE FROM kpc353_2.member_relationships
            WHERE 
                origin_member_id = :logged_in_member_id
                AND target_member_id = :target_member_id
                AND member_relationship_status = 'requested'
                AND member_relationship_type = :rel_type
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Second, delete the existing relationship request from the target member to the logged in member
    $sql = "DELETE FROM kpc353_2.member_relationships
            WHERE 
                origin_member_id = :target_member_id
                AND target_member_id = :logged_in_member_id
                AND member_relationship_status = 'requested'
                AND member_relationship_type = :rel_type
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //Finally, insert the relationship 'rejected' status from the logged in member to the target member, to 
    $sql = "INSERT INTO kpc353_2.member_relationships
        (origin_member_id, target_member_id, member_relationship_type, member_relationship_status)
    VALUES 
        ( :logged_in_member_id, :target_member_id, :rel_type, :rel_status)
    "; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':logged_in_member_id' => $logged_in_member_id, ':target_member_id' => $target_member_id, ':rel_type' => $rel_type, ':rel_status' => $rel_status]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


    //commit the transaction
    $pdo->commit();

    echo "<script>alert('". $rel_type ." relationship rejected succesfully!');</script>";
    echo "<script>window.location.href = 'COSN_member_relationship_view.php';</script>";
}

?>