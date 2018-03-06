<?php
session_start();

require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$keyword1=$_SESSION['keyword1'];
$search_albums = get_search_albums($conn, $keyword1);

function get_search_albums($conn, $keyword1) {
    $sql = fetch_searchalbums1();
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
            
            
            <!-- Recent Albums-->
            <?php if($search_albums):?>
            <div id="recent-albums">
                <div id="recent-albums-headers">
                    <h3> Albums                       |<a href="searchbox3.php">View All</a></h3>
                    <ul class="row">

                        <?php foreach ($search_albums as $i => $arr): ?>
                             <?php $temp7= ucwords($arr['AlbumName']); ?>
                            <li class="col-md-5"><a href="artistbio.php?aname=<?php echo $temp7; ?>"><?php echo $arr['AlbumName']; ?></a></li>
                        <?php endforeach; ?>

                    </ul>
                </div>
            </div>
            <?php endif;?>
            
            
            
        </div>
  
        </div>

    </body>
</html>