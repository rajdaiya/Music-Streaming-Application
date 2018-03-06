<?php
session_start();
require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$keyword1=$_SESSION['keyword1'];

$search_tracks = get_search_tracks($conn, $keyword1);

function get_search_tracks($conn, $keyword1) {
    $sql = fetch_searchtracks1();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$keyword1]);
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
    <body><?php require_once 'header.php'; ?>

        <div id="page-container">
            
            <!-- Search Tracks-->
            <?php if($search_tracks):?>
            <div id="top-songs">
                <div id="top-songs-headers">
                    <h3>Songs        |<a href="searchbox1.php">View All</a></h3>
                    <ul id="top-songs-headers" class="row">
                      <li class="song-header-title col-sm-1"></li>
                        <li class="song-header-title col-sm-4">TRACK NAME</li>
                        <li class="song-header-rating col-sm-1">AVG. RATINGS</li>
                        <li class="song-header-duration col-sm-1">DURATION</li>
                        <li class="song-header-rate">RATE</li>
                    </ul>
                    <?php foreach ($search_tracks as $i => $arr): ?>
                        <ul id ="nav-top-<?php echo $i; ?>" class="row pay-load">
                            <li class="song-header-cnt col-sm-1"><?php echo $i + 1; ?></li>
                            <form id="nav-top-<?php echo $arr['TrackId']; ?>" method="POST" action="#nav-top-<?php echo $i; ?>">
                                <input type="hidden" name="user_play_track" id="user_play_track" value="<?php echo $arr['TrackId']; ?>"/>
                                <li class="song-header-title col-sm-4">
                                    <a onclick="document.getElementById('nav-top-<?php echo $arr['TrackId']; ?>').submit();">
                                        <?php echo ucwords($arr['TrackName']); ?>
                                    </a>
                                </li>
                            </form>
                            <li class="song-header-rating col-sm-1"><?php echo number_format($arr['avg_rating'], 2, '.', ''); ?></li>
                            <li class="song-header-duration col-sm-1"><?php echo number_format(($arr['TrackDuration'] / 60000), 2, ':', ''); ?></li>
                            <li>
                                <fieldset class="rating">
                                    <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="Rocks!">5 stars</label>
                                    <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Pretty good">4 stars</label>
                                    <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Meh">3 stars</label>
                                    <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Kinda bad">2 stars</label>
                                    <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Sucks big time">1 star</label>
                                </fieldset>
                            </li>
                        </ul>

                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif;?>
            
            
            
            
        </div>
        <div class="iframe-container">
            <div style="overflow: hidden;"></div>
            <iframe src='https://open.spotify.com/embed/track/<?php echo $_POST['user_play_track']; ?>' width='100%' height='100' frameborder='0' allowtransparency='true'></iframe>
        </div>

    </body>
</html>