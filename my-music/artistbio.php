<?php
session_start();

require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();

if (isset($_GET['aname']))
    $artist_title = htmlspecialchars($_GET['aname']);
$logged_in_username = $_SESSION['username'];

if (isset($_POST['track-id-rating']) && isset($_POST['rating-value'])) {
    $rating_given = $_POST['rating-value'];
    $track_rated = $_POST['track-id-rating'];
    echo $rating_given;
    echo $track_rated;
    insert_into_ratings($conn, $logged_in_username, $rating_given, $track_rated);
}


if (isset($_POST['user_play_track'])) {
    $track_id = htmlspecialchars($_POST['user_play_track']);
    insert_into_playhistory($conn, $track_id, $artist_title, $logged_in_username);
}

$my_playlists = get_my_playlists($conn, $logged_in_username);
$artist_info = fetch_artist_details($conn, $artist_title, $logged_in_username, $my_playlists);

function fetch_artist_details($conn, $artist_title, $username, $my_playlists) {
    $artist_info['artist_title'] = $artist_title;
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = check_if_artist_exists();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$artist_title]);
    $rowCount = $stmt->rowCount();

    if ($rowCount > 0) {
        $sql = fetch_artist_bio_details();
        $stmt = $conn->prepare($sql);
        $stmt->execute([$artist_title]);
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);
        $artist_info['artist_desc'] = $rows['artist_desc'];
        $artist_info['track_count'] = $rows['track_count'];

        if ($my_playlists) {
            $sql = fetch_top_songs_by_artist();
            $stmt = $conn->prepare($sql);
            $stmt->execute([$artist_title, $username]);
        }
        else {
            $sql = fetch_top_songs_by_artist_1();
            $stmt = $conn->prepare($sql);
            $stmt->execute([$artist_title]);
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() > 0) {
            $artist_info['top_songs'] = $rows;
        }

        $sql = fetch_all_tracks_of_artist();
        $stmt = $conn->prepare($sql);
        $stmt->execute([$artist_title]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artist_info['all_songs'] = $rows;

        if ($username) {
            $sql = does_user_like_artist();
            $stmt = $conn->prepare($sql);
            $stmt->execute([$artist_title, $username]);
            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
            $artist_info['does_like'] = $rows['rec_count'] > 0 ? TRUE : FALSE;
        }

        $sql = fetch_artist_likes_count();
        $stmt = $conn->prepare($sql);
        $stmt->execute([$artist_title]);
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);
        $artist_info['like_count'] = $rows['like_count'];
    }
    else {
        $artist_info['error']['message'] = "No such Artist found!";
    }
    return $artist_info;
}

function get_my_playlists($conn, $username) {
    $sql = fetch_my_playlists();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

function insert_into_playhistory($conn, $track_id, $artist_title, $username) {

    $sql = insert_into_play_history();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $track_id, $artist_title]);
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
    <body>
        <?php require 'header.php'; ?>
        <div id="page-container">
            <?php if (isset($artist_info['error'])): ?>
                <div class="alert alert-danger alert-dismissable">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $artist_info['error']['message']; ?>
                </div>
            <?php endif; ?>

            <?php if (!isset($artist_info['error'])): ?>

                <!-- Displaying Artist Info -->
                <div id="artist-bio" class="row">
                    <div id="artist-image" class="col-sm-5">
                        <img class="w3-card" title="<?php echo ucwords($artist_info['artist_title']); ?> image" alt="<?php ucwords($artist_info['artist_title']) ?>" src="artist-images/download.png">
                    </div>

                    <div id="summary-and-bio" class="col-sm-7">
                        <!-- Displaying Artist Summary -->
                        <div id="artist-summary">
                            <h1><?php echo ucwords($artist_info['artist_title']); ?> Songs</h1>
                            <a href=""><p><?php echo $artist_info['track_count']; ?> Tracks</a> | <?php echo $artist_info['like_count'] ?> Likes</p>
                        </div>
                        <!-- Displaying Artist Summary -->

                        <?php if ($artist_info['artist_desc']): ?>
                            <div id="artist-desc">
                                <h2>Bio</h2>
                                <p id = "artist-desc-txt"><?php echo ucwords($artist_info['artist_desc']); ?></p>
                            </div>
                        <?php endif; ?>

                        <div id="" class="row">
                            <div class="col-sm-3">
                                <form action="likeUnlikeArtistAction.php" method="post" class="artist-like-form">
                                    <input type="hidden" value="<?php echo $artist_title ?>" id="artist_name" name="artist_title"/>
                                    <?php if ($artist_info['does_like'] == 1): ?>
                                        <input type="checkbox" class="" id="like-check" name="like-check" checked> Like
                                    <?php else: ?>
                                        <input type="checkbox" class="" id="like-check" name="like-check"> Like
                                    <?php endif; ?>
                                    <input type="hidden" name="destination" value="<?php echo $_SERVER["REQUEST_URI"]; ?>"/>
                                    <button type="submit"  class="form-sbmt-btn btn btn-default">Submit</button>
                                </form>
                            </div>
                        </div>
                        <?php if (isset($_GET['success'])): ?>
                            <div class="col-sm-4" id="success-msg">
                                <?php if ($artist_info['does_like'] == 1): ?>
                                    <p class="alert alert-success">You have Liked <?php echo $artist_title; ?></p>
                                <?php else: ?>
                                    <p class="alert alert-info">You have unliked <?php echo $artist_title; ?></p>
                                <?php endif; ?>     
                            </div>
                        <?php endif; ?> 
                    </div>
                </div>    
                <!-- Displaying Artist Info -->
                <br>
                <!-- Displaying Top songs -->
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Top Songs</a>
                            </h3>
                        </div>
                        <div id="collapse1" class="panel-collapse collapse in">
                            <?php if ($artist_info['top_songs']): ?>
                                <?php
                                $div_appender = "top-songs";
                                $song_type_to_fetch = $artist_info['top_songs'];
                                require 'render-songs.php';
                                ?>
                            <?php else: ?>
                                <p>No Songs Rated yet!</p>
                            <?php endif; ?>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">All Songs</a>
                                </h3>
                                <div id="collapse2" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <?php
                                        if ($artist_info['all_songs']) {
                                            $div_appender = "all-songs";
                                            $song_type_to_fetch = $artist_info['all_songs'];
                                            require 'render-songs.php';
                                        }
                                        ?>
                                    <?php else: ?>
                                        <p>No Songs by <?php echo $artist_info['artist_title'] ?> yet!</p>    
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="iframe-container">
            <div style="overflow: hidden;"></div>
            <iframe src='https://open.spotify.com/embed/track/<?php echo $_POST['user_play_track']; ?>' width='100%' height='100' frameborder='0' allowtransparency='true'></iframe>
        </div>
    </body>
</html>