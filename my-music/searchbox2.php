<?php
session_start();

require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$keyword1=$_SESSION['keyword1'];

$search_artists = get_search_artists($conn, $keyword1);
function get_search_artists($conn, $keyword1) {
    $sql = fetch_searchartists1();
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

            <!-- Search artists-->
            <?php if($search_artists):?>
            <div id="fav-artist-songs">
                <div id="fav-artist-songs-headers">
                    <h3>Artists                 |<a href="searchbox2.php">View All</a></h3>
                    <ul id="fav-artist-songs-headers" class="row">
                
                    </ul>
                    <?php foreach ($search_artists as $i => $arr): ?>
                        <ul id ="nav-fas-<?php echo $i; ?>" class="row pay-load">
                                                        <?php $temp1= ucwords($arr['ArtistTitle']); ?>
                            <li class="song-header-title col-sm-10"><a href="artistbio.php?aname=<?php echo $temp1; ?>"> <?php echo ucwords($arr['ArtistTitle']); ?></a></li>
                        </ul>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif;?>

        </div>
    

    </body>
</html>