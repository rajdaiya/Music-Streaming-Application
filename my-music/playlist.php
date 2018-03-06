<?php
session_start();

require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();
//$artist_title='Maroon';
//$artist_title = htmlspecialchars($_GET['aname']);
$logged_in_username = $_SESSION['username'];
//$username = htmlspecialchars($_GET['uname']);
//$username = 'dj';
$PlaylistId = $_GET['id'];
$my_playlists = get_my_playlists($conn, $logged_in_username);

$playlist_info = fetch_playlist_details($conn, $PlaylistId);

function fetch_playlist_details($conn, $PlaylistId) {
    $playlist_info = array();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = fetch_playlistname();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$PlaylistId]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $playlist_info['PlaylistName'] = $rows['PlaylistName'];

    $sql = fetch_playlisttracks();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$PlaylistId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $playlist_info['playlist_tracks'] = $rows;
    return $playlist_info;
}

if (isset($_POST['user_play_track']) && isset($_POST['user_play_artist'])) {
    $track_id = htmlspecialchars($_POST['user_play_track']);
    $artist_title = htmlspecialchars($_POST['user_play_artist']);
    insert_into_playhistory($conn, $track_id, $artist_title, $logged_in_username);
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

            <!-- Displaying Artist Info -->
            <div id="artist-bio" class="row">
                <div id="summary-and-bio" class="col-sm-7">

                    <div id="artist-summary">
                        <h1>Playlist: <?php echo ucwords($playlist_info['PlaylistName']); ?> </h1>


                    </div>


                    <div id="" class="row">
                        <div class="col-sm-3">
                        </div>
                    </div>
                </div>
            </div>   



            <?php if ($playlist_info['playlist_tracks']): ?>
                <div id = "top-songs">
                    <h3>Songs:</h3>
                    <?php
                    $song_type_to_fetch = $playlist_info['playlist_tracks'];
                    $div_appender = 'pl-songs';
                    require 'render-songs.php';
                    ?>

                </div>
            <?php endif; ?>

            <!-- Displaying Top songs -->
        </div>

        <div class="iframe-container">
            <div style="overflow: hidden;"></div>

            <iframe src='https://open.spotify.com/embed/track/<?php echo $_POST['user_play_track']; ?>' width='100%' height='100' frameborder='0' allowtransparency='true'></iframe>
        </div>
    </body>
</html>

