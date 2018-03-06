<?php
session_start();

require_once('DbConnection.php');
$db_conn = new DBConnection();
$con = $db_conn->getDBConnection();
echo "HERE";
$a=$_POST['playlistname'];
echo $a;
//$b=$_SESSION['username'];
$b='dj';
$c=$_POST['type'];
echo $c;
echo "HERE AGAIN";
$sql = "INSERT into Playlist (PlaylistName,UName,Is_Private) VALUES ('" . $a . "','" . $b . "',b'" . $c . "')";
$con->query($sql);
//header("location: mainhome.html"); 
exit();
echo "DONE";
mysql_close($con);
?>
