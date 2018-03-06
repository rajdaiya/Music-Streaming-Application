<?php
session_start();

require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$logged_in_username = $_SESSION['username'];
$dest_user= htmlspecialchars($_POST['dest-username']);


$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = does_user_follow_user1();
$stmt = $conn->prepare($sql);
$stmt->execute([$logged_in_username, $dest_user]);
$rows = $stmt->fetch(PDO::FETCH_ASSOC);
$does_user_follow = $rows['rec_count'] > 0 ? 1 : 0;

$current_follow_value = isset($_POST['follow-check']) ? 1 : 0;

if ($does_user_follow == 0 && $current_follow_value == 1) {
    try {
        $sql = insert_into_followers();
        $stmt = $conn->prepare($sql);
        $stmt->execute([$logged_in_username, $dest_user]);
        $success = 1;
    }
    catch (PDOException $E) {
        
    }
}
elseif ($does_user_follow == 1 && $current_follow_value == 0) {
    $sql = delete_from_followers();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$logged_in_username, $dest_user]);
    $success = 1;
}

if (isset($_REQUEST["destination"])) {
    if($success == 1 && !isset($_GET['success'])) {
        header("Location: {$_REQUEST["destination"]}&success=$success");
    }
    else {
        header("Location: {$_REQUEST["destination"]}");
    }
}
else if (isset($_SERVER["HTTP_REFERER"])) {
    header("Location: {$_SERVER["HTTP_REFERER"]}");
}