<?php
require_once('DbConnection.php');
$db_conn = new DBConnection();
$con = $db_conn->getDBConnection();

$a=$_POST['uname'];
$b=$_POST['Name'];
$c=$_POST['Email'];
$d=$_POST['City'];
$e=$_POST['pass'];

try {
	$sql = "INSERT into user (UName,Name,Email,City) VALUES ('" . $a . "','" . $b . "','" . $c . "','" . $d . "')";
	$sql1= "INSERT into login_info (UName,Pass) VALUES('" . $a . "',password('" . $e . "'))";
	$con->query($sql);
	$con->query($sql1);	
	header('Location: user-dashboard.php');
        session_start();
        $_SESSION['username']=$a;
} catch (Exception $ex) {
        header('Location: error.php');
}

?>
