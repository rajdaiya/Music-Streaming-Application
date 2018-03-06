<?php
session_start();

require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();


if (isset($_GET['uname'])) {
    $dest_user = htmlspecialchars($_GET['uname']);
}
$logged_in_username = $_SESSION['username'];

$user_info = fetch_user_profile_details($conn, $logged_in_username, $dest_user);

function fetch_user_profile_details($conn, $username, $username1) {
    //$user_info = array();
    $user_info['username'] = $username1;
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = fetch_user_bio_details();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username1]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_info['UName'] = $rows['UName'];
    $user_info['Email'] = $rows['Email'];


    $sql = fetch_user_followers_count();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username1]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_info['followers_count'] = $rows['followers_count'];


    $sql = fetch_user_followers();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username1]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_info['followers'] = $rows;


    $sql = fetch_user_following_count();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username1]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_info['following_count'] = $rows['following_count'];


    $sql = fetch_user_following();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username1]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_info['following'] = $rows;

    $sql = fetch_fav_artists();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username1]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_info['fav_artists'] = $rows;

    if ($username == $username1) {
        $sql = fetch_self_playlists();
    }
    else {
        $sql = fetch_public_playlists();
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username1]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_info['self_playlist'] = $rows;

    $sql = fetch_other_users_playlists();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username1]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_info['users_playlist'] = $rows;

    $sql = does_user_follow_user1();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $username1]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_info['does_follow'] = $rows['rec_count'] > 0 ? 1 : 0;


    $sql = fetch_user_followers_count();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username1]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_info['follower_count'] = $rows['follower_count'];

    return $user_info;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php require_once 'header.php'; ?>
        <div id="page-container">

            <!-- Displaying Artist Info -->
            <div id="user-bio" class="row">
                <div id="user-image" class="col-sm-5">
                    <img title="<?php echo ucwords($user_info['username']); ?> image" alt="<?php ucwords($user_info['username']) ?>" src="artist-images/download.png">
                </div>

                <div id="summary-and-bio" class="col-sm-7">

                    <div id="user-summary">
                        <h1><?php echo ucwords($user_info['username']); ?> </h1>
                        <?php if ($logged_in_username == $dest_user): ?>
                            <p> <?php echo $user_info['Email'] ?> </p>
                        <?php endif; ?>
                        <p> <a href="following.php?uname=<?php echo $dest_user; ?>"><?php echo $user_info['following_count'] ?> Following</a> | 
                            <a href="followers.php?uname=<?php echo $dest_user; ?>"><?php echo $user_info['followers_count'] ?> Followers</a></p>
                        <?php if ($logged_in_username == $dest_user): ?>
                        <p> <a href="./create_playlist.php"> Create Playlist </a> </p>
<!--                            <div data-toggle="modal" data-target="#create-playlist-modal"><span>Create Playlist</span></div>
                            <div class="modal fade" id="create-playlist-modal>" role="dialog">
                                <div class="modal-dialog">
                                     Modal content
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Create new Playlist</h4>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" action="create-playlist.php" name="create-playlist" id="create-playlist">
                                                <p>Enter Playlist Name</p>
                                                <input type="text"/>
                                                <p>Private or Public?</p>
                                                <input type="radio" value="1" name="pop" checked/>Public
                                                <input type="radio" value="0" name="pop" checked/>Private
                                                <input type="submit" value="Create Playlist"/>
                                            </form>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>   
                                    </div>
                                </div>
                            </div>-->
                        <?php endif; ?>
                    </div>

                    <?php if ($logged_in_username != $dest_user): ?>
                        <div id="" class="row">
                            <div class="col-sm-3">
                                <form action="FollowUnfollow.php" method="post" class="artist-like-form">
                                    <input type="hidden" value="<?php echo $dest_user ?>" id="dest-username" name="dest-username"/>
                                    <?php if ($user_info['does_follow'] == 1): ?>
                                        <input type="checkbox" class="" id="follow-check" name="follow-check" checked> Follow
                                    <?php else: ?>
                                        <input type="checkbox" class="" id="follow-check" name="follow-check"> Follow
                                    <?php endif; ?>
                                    <input type="hidden" name="destination" value="<?php echo $_SERVER["REQUEST_URI"]; ?>"/>
                                    <button type="submit"  class="form-sbmt-btn btn btn-default">Confirm</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['success'])): ?>
                        <div class="col-sm-5" id="success-msg">
                            <?php if ($user_info['does_follow'] == 1): ?>
                                <p class="alert alert-success">You have Followed <?php echo $dest_user; ?></p>
                            <?php else: ?>
                                <p class="alert alert-info">You have Unfollowed <?php echo $dest_user; ?></p>
                            <?php endif; ?>     
                        </div>
                    <?php endif; ?> 
                </div>
            </div>   



            <?php if ($user_info['fav_artists']): ?>
                <div id = "fav-artists">
                    <?php if ($logged_in_username == $dest_user): ?>
                        <h3>Your Favorite Artists:</h3>
                    <?php else: ?>
                        <h3><?php echo $dest_user . "'s" ?> Favorite Artists:</h3>
                    <?php endif; ?>
                    <ul id="fav-artist-songs-headers" class="row playload">
                        <li class="song-header-cnt col-sm-2">#</li>
                        <li class="song-header-title col-sm-10">ARTIST NAME</li>


                    </ul>
                    <ul id="fav-artist-songs-headers" class="row pay-load">
                        <?php foreach ($user_info['fav_artists'] as $i => $arr): ?>

                            <li class="song-header-cnt col-sm-2"><?php echo $i + 1; ?></li>
                            <?php $temp1 = $arr['fav_artists']; ?>
                            <li class="song-header-title col-sm-10"><a href="artistbio.php?aname=<?php echo $temp1; ?>"> <?php echo ucwords($arr['fav_artists']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <?php if ($logged_in_username == $dest_user): ?>
                    <h3>Your Favorite Artists:</h3>
                    <p><i>You don't like any Artist yet!</i></p>
                <?php else: ?>
                    <p><i><?php echo $dest_user ?> doesn't like any Artist yet</i></p>
                <?php endif; ?>
            <?php endif; ?>
            </br></br></br>

            <?php if ($user_info['self_playlist']): ?>
                <div id = "top-songs">
                    <?php if ($logged_in_username == $dest_user): ?>
                        <h3>Your Playlists:</h3>
                    <?php else: ?>
                        <h3><?php echo $dest_user . "'s" ?> Playlists:</h3>
                    <?php endif; ?>
                    <ul id="top-songs-headers" class="row pay-load">
                        <li class="song-header-cnt col-sm-2">#</li>
                        <li class="song-header-title col-sm-10">PLAYLIST NAME</li>


                    </ul>
                    <ul id ="playlist-headers" class="row pay-load">
                        <?php foreach ($user_info['self_playlist'] as $i => $arr): ?>
                            <li class="song-header-cnt col-sm-2"><?php echo $i + 1; ?></li>
                            <li class="song-header-title col-sm-10"><a href="playlist.php?id=<?php echo $arr['PlaylistId']?>"> <?php echo ucwords($arr['self_playlist']); ?></a></li>

                        <?php endforeach; ?>
                    </ul>
                        <br/>
                        <br/>
                </div>
            <?php else: ?>
                <h3>Your Playlists:</h3>
                <?php if ($logged_in_username == $dest_user): ?>
                    <p><i>You don't have any Playlists yet!</i></p>
                <?php else: ?>
                    <p><i><?php echo $dest_user ?> doesn't have any Public Playlists to show yet!</i></p>
                <?php endif; ?>
            <?php endif; ?>
            <!-- Displaying Top songs -->
        </div>
    </body>
</html>
