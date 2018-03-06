<?php
session_start();

require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();


$dest_user = htmlspecialchars($_GET['uname']);
$logged_in_username = $_SESSION['username'];

$user_info = fetch_user_profile_details($conn, $dest_user);

function fetch_user_profile_details($conn, $username) {
    $user_info['username'] = $username;
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = fetch_user_bio_details();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_info['UName'] = $rows['UName'];
    $user_info['Email'] = $rows['Email'];

    $sql = fetch_user_followers();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_info['followers'] = $rows;

    $sql = fetch_user_followers_count();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_info['followers_count'] = $rows['followers_count'];



    $sql = fetch_user_following_count();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_info['following_count'] = $rows['following_count'];

    return $user_info;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
    </head>
    <body><?php require_once 'header.php'; ?>

        <div id="page-container">



            <!-- Displaying Artist Info -->
            <div id="artist-bio" class="row">
                <div id="artist-image" class="col-sm-5">
                    <img title="<?php echo ucwords($user_info['username']); ?> image" alt="<?php ucwords($user_info['username']) ?>" src="artist-images/download.png">
                </div>

                <div id="summary-and-bio" class="col-sm-7">

                    <div id="artist-summary">
                        <h1><?php echo ucwords($user_info['username']); ?> </h1>
                        <?php if ($logged_in_username == $dest_user): ?>
                            <p> <?php echo $user_info['Email'] ?> </p>
                        <?php endif; ?>
                        <p> <a href="following.php?uname=<?php echo $dest_user; ?>"><?php echo $user_info['following_count'] ?> Following</a> |
                            <a href="followers.php?uname=<?php echo $dest_user; ?>"><?php echo $user_info['followers_count'] ?> Followers</a></p>
                        <?php if ($logged_in_username == $dest_user): ?>
                            <p> <a href="./create_playlist.php"> Create Playlist </a> </p>
                        <?php endif; ?>
                    </div>



                </div>
            </div>  



            <?php if ($user_info['followers']): ?>
                <div id = "top-songs">
                    <h3>Your Followers:</h3>
                    <ul id="top-songs-headers" class="row payload">
                        <li class="song-header-cnt col-sm-2">#</li>
                        <li class="song-header-title col-sm-10">FOLLOWER NAME</li>


                    </ul>
                    <ul id ="pay-load" class="row pay-load">
                        <?php foreach ($user_info['followers'] as $i => $arr): ?>

                            <li class="song-header-cnt col-sm-2"><?php echo $i + 1; ?></li>
                            <?php $temp3 = $arr['followers']; ?>
                            <li class="song-header-title col-sm-10"><a href="userprofile.php?uname=<?php echo $temp3; ?>"><?php echo ucwords($arr['followers']); ?></a></li>

                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <h3>You Followers:</h3>
                <p><i>No one is following you yet!</i></p>
            <?php endif; ?>
            </br></br></br>

        </div>
    </body>
</html>
