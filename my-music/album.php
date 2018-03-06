<?php
session_start();

require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();
$logged_in_username = $_SESSION['username'];

if (isset($_GET['id']))
    $album_id = htmlspecialchars($_GET['id']);


if (isset($_POST['user_play_track']) && isset($_POST['artist-title'])) {
    $track_id = htmlspecialchars($_POST['user_play_track']);
    $artist_title = $_POST['artist-title'];
    insert_into_playhistory($conn, $track_id, $artist_title, $logged_in_username);
}

if (isset($_POST['track-id-rating']) && isset($_POST['rating-value'])) {
    $rating_given = $_POST['rating-value'];
    $track_rated = $_POST['track-id-rating'];
    insert_into_ratings($conn, $logged_in_username, $rating_given, $track_rated);
}

$my_playlists = get_my_playlists($conn, $logged_in_username);
$album_info = fetch_album_details($conn, $album_id, $logged_in_username, $my_playlists);

function insert_into_ratings($conn, $username, $rating_given, $track_rated) {
    $sql = insert_or_update_into_ratings_sql();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$track_rated, $username, $rating_given, $rating_given]);
}

function fetch_album_details($conn, $album_id, $username, $my_playlists) {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($my_playlists) {
        $sql = album_info();
        $stmt = $conn->prepare($sql);
        $stmt->execute([$album_id, $username]);
    } else {
        $sql = album_info_1();
        $stmt = $conn->prepare($sql);
        $stmt->execute([$album_id]);
    }
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $album_info['all_songs'] = $rows;

    $sql = album_name();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$album_id]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $album_info['AlbumName'] = $rows['AlbumName'];

    return $album_info;
}

function insert_into_playhistory($conn, $track_id, $artist_title, $username) {

    $sql = insert_into_play_history();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $track_id, $artist_title]);
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
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    </head>
    <body><?php require_once 'header.php'; ?>

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

            <div id="album-summary">
                <h1><?php echo ucwords($album_info['AlbumName']); ?> </h1>
            </div>

            <!-- Displaying album songs -->
            <?php if ($album_info['all_songs']): ?>
                <div id = "album-songs">
                    <?php
                    $song_type_to_fetch = $album_info['all_songs'];
                    $div_appender = 'all-songs';
                    require 'render-songs.php';
                    ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="iframe-container">
            <div style="overflow: hidden;"></div>

            <iframe src='https://open.spotify.com/embed/track/<?php echo $_POST['user_play_track']; ?>' width='100%' height='100' frameborder='0' allowtransparency='true'></iframe>
        </div>
    </body>
</html>
