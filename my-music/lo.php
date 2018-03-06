<?php

require_once('DbConnection.php');
$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();

$logged_in_username = $_POST['uname'];
$password = $_POST['pass'];

$sql7 = "SELECT * FROM login_info WHERE UName='$logged_in_username' AND Pass=password('$password')";
$stmt = $conn->prepare($sql7);
$stmt->execute();
$rowCount = $stmt->rowCount();
//$con->query($sql7);
//$n=$sql7->rowCount();
//echo $n;

if ($rowCount == 0) {
    header("Location: login.html");
} else {
    header("Location: user-dashboard.php");
    session_start();
    $_SESSION['username'] = $logged_in_username;
}
?>
