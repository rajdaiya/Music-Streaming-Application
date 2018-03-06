<?php
session_start();

require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$logged_in_username = $_SESSION['username'];
$artist_title = $_POST['artist_title'];

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = does_user_like_artist();
$stmt = $conn->prepare($sql);
$stmt->execute([$artist_title, $logged_in_username]);
$rows = $stmt->fetch(PDO::FETCH_ASSOC);
$does_user_like = $rows['rec_count'] > 0 ? 1 : 0;

$current_like_value = isset($_POST['like-check']) ? 1 : 0;

if ($does_user_like == 0 && $current_like_value == 1) {
    try {
        $sql = insert_into_likes();
        $stmt = $conn->prepare($sql);
        $stmt->execute([$artist_title, $logged_in_username]);
        $success = 1;
    }
    catch (PDOException $E) {
        echo $E->getMessage();
    }
}
elseif ($does_user_like == 1 && $current_like_value == 0) {
    $sql = delete_from_likes();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$artist_title, $logged_in_username]);
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