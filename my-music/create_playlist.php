<?php
session_start();

require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();

$logged_in_username = $_SESSION['username'];

$my_playlists = get_my_playlists($conn, $logged_in_username);

if (isset($_POST['pl-name']) && isset($_POST['pop'])) {
    $pl_name = $_POST['pl-name'];
    
    $pop = $_POST['pop'];
   
     
        $sql = "INSERT INTO Playlist (`PlaylistName`, `UName`, `Is_Private`) values('$pl_name', '$logged_in_username', b'$pop')";
       
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        echo "Playlist successfully created!";
        
}

//header("Location: ".$_SERVER['HTTP_REFERER']);

function user_already_has_same_named_pl($my_pl, $name) {
    foreach ($my_pl as $i => $pl) {
        if ($pl['PlaylistName'] == $name) {
            return TRUE;
        }
    }
    return FALSE;
}

function get_my_playlists($conn, $username) {
    $sql = fetch_my_playlists();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>        

    </head>
    <body>
        <?php require_once 'header.php'; ?>
        <div id="page-container">
            <center>
                <P>Create a new Playlist</P>
                <form method="POST" action="" name="create-playlist" id="create-playlist" >
                    <p>Enter Playlist Name</p>
                    <input type="text" name="pl-name"/>
                    <p>Private or Public?</p>
                    <input type="radio" value="1" name="pop" checked/>Public
                    <input type="radio" value="0" name="pop" />Private
                    <input type="submit" value="Create Playlist"/>
                </form></center>
        </div>
    </body>
</html>


