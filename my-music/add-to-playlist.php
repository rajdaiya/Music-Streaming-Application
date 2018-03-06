<?php

session_start();

require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();

$trackId = $_POST['track-id-to-add'];
$selected_playlist_id = $_POST['usr-playlists'];
$logged_in_username = $_SESSION['username'];
$message = '';
if ($selected_playlist_id == -1) {
    $message = 'Please select a playlist';
    $_SESSION['add-to-playlist']['error'] = $message;
}
else {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $logged_in_username = $_SESSION['username'];

    $is_already_present = check_if_song_already_exist_in_playlist($conn, $trackId, $selected_playlist_id);
    if($is_already_present == TRUE) {
        $message = 'This track is already present in the selected playlist';
        $_SESSION['add-to-playlist']['info'] = $message;
    } else {
        insert_into_playtracks($conn, $selected_playlist_id, $trackId);
        $message = 'Track added to the selected playlist';
        $_SESSION['add-to-playlist']['success'] = $message;
    }   
}
header('Location: '.$_SERVER['HTTP_REFERER']);

function check_if_song_already_exist_in_playlist($conn, $trackId, $selected_playlist_id) {
    $sql = "SELECT TrackId from PlayTracks where PlaylistId = ? and TrackId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$selected_playlist_id, $trackId]);
    return $stmt->rowCount() == 1;
}

function insert_into_playtracks($conn, $selected_playlist_id, $trackId) {
    $sql = insert_into_playlist_tracks($selected_playlist_id, $trackId);
    $stmt = $conn->prepare($sql);
    $stmt->execute([$selected_playlist_id, $trackId]);
}