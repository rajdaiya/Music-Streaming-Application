<?php
session_start();
require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['track-id-rating']) && isset($_POST['rating-value'])) {
    $rating_given = $_POST['rating-value'];
    $track_rated = $_POST['track-id-rating'];
    $logged_in_username = $_SESSION['username'];
    insert_into_ratings($conn, $logged_in_username, $rating_given, $track_rated);
}

$keyword = $_POST['keyword'];
$keyword1 = '%' . $keyword . '%';
$_SESSION['keyword1'] = $keyword1;
$username = $_SESSION['username'];

$my_playlists = get_my_playlists($conn, $username);
$search_tracks = get_search_tracks($conn, $keyword1, $username, $my_playlists);
$search_artists = get_search_artists($conn, $keyword1);
$search_albums = get_search_albums($conn, $keyword1);
$search_users = get_search_users($conn, $keyword1);



function get_my_playlists($conn, $username) {
    $sql = fetch_my_playlists();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

function get_search_tracks($conn, $keyword1, $username, $my_playlists) {
    if($my_playlists) {
        $sql = fetch_searchtracks();
        $stmt = $conn->prepare($sql);
        $stmt->execute([$keyword1, $username]);
    }
    else {
        $sql = fetch_searchtracks_1();
        $stmt = $conn->prepare($sql);
        $stmt->execute([$keyword1]);
    }
    
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

function get_search_artists($conn, $keyword1) {
    $sql = fetch_searchartists();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$keyword1]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

function get_search_albums($conn, $keyword1) {
    $sql = fetch_searchalbums();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$keyword1]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

function get_search_users($conn, $keyword1) {
    $sql = fetch_search_users();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$keyword1, $keyword1]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

function insert_into_ratings($conn, $username, $rating_given, $track_rated) {
    $sql = insert_or_update_into_ratings_sql();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$track_rated, $username, $rating_given, $rating_given]);
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

            <!-- Search Tracks-->
            <?php if ($search_tracks): ?>
                <div id="top-songs">
                    <div id="top-songs-headers">
                        <h3>Songs        | <a href="searchbox1.php">View All</a></h3>
                        <?php 
                        $div_appender = "all-songs";
                        $song_type_to_fetch = $search_tracks;
                        require 'render-songs.php';
                        ?>
                        
                    </div>
                </div>
            <?php endif; ?>
            <!-- Search artists-->
            <?php if ($search_artists): ?>
                <div id="fav-artist-songs">
                    <div id="fav-artist-songs-headers">
                        <h3>Artists                 | <a href="searchbox2.php">View All</a></h3>
                        <ul id="fav-artist-songs-headers" class="row pay-load">
                        <?php foreach ($search_artists as $i => $arr): ?>
                            <?php $temp1 = $arr['ArtistTitle']; ?>
                            <li class="col-md-6"><a href="artistbio.php?aname=<?php echo $temp1; ?>"> <?php echo ucwords($arr['ArtistTitle']); ?></a></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Recent Albums-->
            <?php if ($search_albums): ?>
                <div id="recent-albums">
                    <div id="recent-albums-headers">
                        <h3> Albums                       | <a href="searchbox3.php">View All</a></h3>
                        <ul class="row pay-load">

                            <?php foreach ($search_albums as $i => $arr): ?>
                                <?php $temp7 = $arr['AlbumName']; ?>
                                <li class="col-md-6"><a href="artistbio.php?aname=<?php echo $temp7; ?>"><?php echo $arr['AlbumName']; ?></a></li>
                            <?php endforeach; ?>

                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($search_users): ?>
                <div id="search-users">
                    <h3>People</h3>
                    <ul class="row pay-load">
                        <?php foreach ($search_users as $i => $arr): ?>
                            <?php $temp7 = $arr['UName']; ?>
                            <li class="col-md-5"><a href="./userprofile.php?uname=<?php echo $temp7; ?>"><?php echo $arr['UName']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>


        </div>
        <?php if (isset($_POST['user_play_track'])): ?>
            <div class="iframe-container">
                <div style="overflow: hidden;"></div>
                <iframe src='https://open.spotify.com/embed/track/<?php echo $_POST['user_play_track']; ?>' width='100%' height='100' frameborder='0' allowtransparency='true'></iframe>
            </div>
        <?php endif; ?>

    </body>
</html>