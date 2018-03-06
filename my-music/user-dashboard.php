<?php

session_start();

require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$logged_in_username = $_SESSION['username'];

if (isset($_POST['track-id-rating']) && isset($_POST['rating-value'])) {
    $rating_given = $_POST['rating-value'];
    $track_rated = $_POST['track-id-rating'];
    insert_into_ratings($conn, $logged_in_username, $rating_given, $track_rated);
}
$my_playlists = get_my_playlists($conn, $logged_in_username);

$best_songs = get_best_songs($conn, $logged_in_username, $my_playlists);

$songs_by_artists_you_like = get_songs_by_artists_you_like($conn, $logged_in_username, $my_playlists);

$recent_albums = get_recent_albums($conn);

$playlists_of_users_you_follow = get_playlists_of_users_you_follow($conn, $logged_in_username);

function get_best_songs($conn, $username, $my_playlists) {
    if ($my_playlists) {
        $sql = fetch_best_songs();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':uname', strval($username), PDO::PARAM_STR);
        $stmt->bindValue(':offset', intval(0), PDO::PARAM_INT);
        $stmt->bindValue(':max_limit', intval(10), PDO::PARAM_INT);
    }
    else {
        $sql = fetch_best_songs_1();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':offset', intval(0), PDO::PARAM_INT);
        $stmt->bindValue(':max_limit', intval(10), PDO::PARAM_INT);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

function get_user_play_history($conn, $username, $my_playlists) {
    if ($my_playlists) {
        $sql = fetch_user_play_history();
    }
    else {
        $sql = fetch_user_play_history_1();
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':uname', $username, PDO::PARAM_STR);
    $stmt->bindValue(':offset', intval(0), PDO::PARAM_INT);
    $stmt->bindValue(':max_limit', intval(10), PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

function get_songs_by_artists_you_like($conn, $username, $my_playlists) {
    if ($my_playlists) {
        $sql = fetch_songs_by_artist_you_like();
    }
    else {
        $sql = fetch_songs_by_artist_you_like_1();
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':uname', $username, PDO::PARAM_STR);
    $stmt->bindValue(':offset', intval(0), PDO::PARAM_INT);
    $stmt->bindValue(':max_limit', intval(10), PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

function get_recent_albums($conn) {
    $sql = fetch_recent_albums();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':offset', intval(0), PDO::PARAM_INT);
    $stmt->bindValue(':max_limit', intval(10), PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

function get_playlists_of_users_you_follow($conn, $username) {
    $sql = fetch_playlists_of_users_you_follow();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':uname', $username, PDO::PARAM_STR);
    $stmt->bindValue(':offset', intval(0), PDO::PARAM_INT);
    $stmt->bindValue(':max_limit', intval(10), PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

function insert_into_ratings($conn, $username, $rating_given, $track_rated) {
    $sql = insert_or_update_into_ratings_sql();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$track_rated, $username, $rating_given, $rating_given]);
}

if (isset($_POST['user_play_track']) && isset($_POST['artist-title'])) {
    $track_id = htmlspecialchars($_POST['user_play_track']);
    $artist_title = $_POST['artist-title'];
    insert_into_playhistory($conn, $track_id, $artist_title, $logged_in_username);
}

function insert_into_playhistory($conn, $track_id, $artist_title, $username) {

    $sql = insert_into_play_history();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $track_id, $artist_title]);
}

$user_play_history = get_user_play_history($conn, $logged_in_username, $my_playlists);

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
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php require 'header.php'; ?>
        <div id="page-container">

            <?php if (isset($_SESSION['add-to-playlist']['error'])): ?><div><p class="alert alert-danger row"><?php
                echo $_SESSION['add-to-playlist']['error'];
                unset($_SESSION['add-to-playlist']['error']);
                ?></p></div><?php endif; ?>
            <?php if (isset($_SESSION['add-to-playlist']['success'])): ?><div><p class="alert alert-success"><?php
                echo $_SESSION['add-to-playlist']['success'];
                unset($_SESSION['add-to-playlist']['success']);
                ?></p></div><?php endif; ?>
            <?php if (isset($_SESSION['add-to-playlist']['info'])): ?><div><p class="alert alert-info"><?php
                echo $_SESSION['add-to-playlist']['info'];
                unset($_SESSION['add-to-playlist']['info']);
                ?></p></div><?php endif; ?>


            <!-- Top Songs-->
            <?php if ($best_songs): ?>
                <h3>Top Songs</h3>
                <?php
                $song_type_to_fetch = $best_songs;
                $div_appender = "top-songs";
                require 'render-songs.php';
                ?>
            <?php endif; ?>

            <!-- Fav artist songs-->
            <?php if ($songs_by_artists_you_like): ?>
                <h3>Songs by your Fav Artists</h3>
                <?php
                $song_type_to_fetch = $songs_by_artists_you_like;
                $div_appender = "fav-artist";
                require 'render-songs.php';
                ?>
            <?php endif; ?>

            <!-- Recent Albums-->
            <?php if ($recent_albums): ?>
                <div id="recent-albums">
                    <div id="recent-albums-headers">
                        <h3>Recent Albums</h3>
                        <ul class="row">

                            <?php foreach ($recent_albums as $i => $arr): ?>
                                <li class="col-md-1"><a href="./album.php?id=<?php echo $arr['AlbumId']; ?>"><?php echo $arr['AlbumName']; ?></a></li>
                            <?php endforeach; ?>

                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Follower's playlist-->
            <?php if ($playlists_of_users_you_follow): ?>
                <div id="follower-playlist">
                    <div id="playlist-headers">
                        <h3>Playlists by People You Follow</h3>
                        <ul class="row">
                            <table class="table-bordered table-hover">
                                <?php foreach ($playlists_of_users_you_follow as $i => $arr): ?>
                                    <tr><li class="col-md-1"><a href="./playlist.php?id=<?php echo $arr['PlaylistId']; ?>"><?php echo $arr['PlaylistName']; ?></a></li></tr>
                                <?php endforeach; ?>
                            </table>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Play history-->
            <?php if ($user_play_history): ?>
                <h3>Last Played</h3>
                <?php
                $song_type_to_fetch = $user_play_history;
                $div_appender = "play-history";
                require 'render-songs.php';
                ?>
            <?php endif; ?>

            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
        </div>
        <div class="iframe-container">
            <div style="overflow: hidden;"></div>
            <iframe src='https://open.spotify.com/embed/track/<?php echo $_POST['user_play_track']; ?>' width='100%' height='100' frameborder='0' allowtransparency='true'></iframe>
        </div>

    </body>
</html>