<?php

function fetch_artist_bio_details() {
    $sql = "SELECT ArtistDescription as artist_desc, count(TrackId) as track_count from artists natural join tracks where ArtistTitle = ? LIMIT 1";
    return $sql;
}

//function fetch_top_songs_by_artist() {
//    $sql = "SELECT t.TrackId, t.TrackName, t.ArtistTitle, t.TrackDuration, avg(r.Rating) as avg_rating, pt.PlaylistId "
//        . "from tracks t "
//        . "natural join Rating r "
//        . "left outer join PlayTracks pt on pt.TrackId = t.TrackId "
//        . "cross join Playlist pl "
//        . "where t.ArtistTitle = ? and pl.UName = ? "
//        . "group by t.TrackId "
//        . "order by avg_rating "
//        . "desc limit 25";
//    return $sql;
//}

function fetch_top_songs_by_artist() {
    $sql = "SELECT t.TrackId, t.TrackName, t.ArtistTitle, t.TrackDuration, avg(r.Rating) as avg_rating, p.PlaylistId "
        . "from tracks t "
        . "natural join Rating r "
        . "left outer join PlayTracks pt on pt.TrackId = t.TrackId "
        . "left outer join (select PlaylistId from Playlist where UName = ?) p on p.PlaylistId = pt.PlaylistId "
        . "where t.ArtistTitle = ? "
        . "group by t.TrackId "
        . "order by avg_rating "
        . "desc limit 25";
    return $sql;
}

function fetch_top_songs_by_artist_1() {
    $sql = "SELECT t.TrackId, t.TrackName, t.ArtistTitle, t.TrackDuration, avg(r.Rating) as avg_rating "
        . "from tracks t "
        . "natural join Rating r "
        . "where t.ArtistTitle = ? "
        . "group by t.TrackId "
        . "order by avg_rating "
        . "desc limit 25";
    return $sql;
}

function does_user_like_artist() {
    $sql = "SELECT count(ltime) as rec_count from Likes where ArtistTitle = ? AND UName = ?";
    return $sql;
}

function does_user_follow_user1() {
    $sql = "SELECT count(*) as rec_count from Followers where UName = ? AND UFollowing = ? ";
    return $sql;
}

function fetch_artist_likes_count() {
    $sql = "SELECT count(UName) as like_count from Likes where ArtistTitle = ?";
    return $sql;
}

function check_if_artist_exists() {
    $sql = "SELECT ArtistTitle from artists where ArtistTitle = ?";
    return $sql;
}

function fetch_all_tracks_of_artist() {
    $sql = "SELECT t.TrackId, t.TrackName, t.ArtistTitle, t.TrackDuration, ifnull(avg(r.rating),0) as avg_rating from tracks t left outer join Rating r on t.TrackId = r.TrackId where t.ArtistTitle= ? group by t.TrackId";
    return $sql;
}

function fetch_user_bio_details() {
    $sql = "SELECT Email , UName from user where UName = ?";
    return $sql;
}

function fetch_user_followers_count() {
    $sql = "SELECT count(UName) as followers_count from Followers where UFollowing = ?";
    return $sql;
}

function fetch_user_followers() {
    $sql = "SELECT UName as followers from Followers where UFollowing = ?";
    return $sql;
}

function fetch_user_following_count() {
    $sql = "SELECT count(UFollowing) as following_count from Followers where UName = ?";
    return $sql;
}

function fetch_user_following() {
    $sql = "SELECT UFollowing as following from Followers where UName = ?";
    return $sql;
}

function fetch_fav_artists() {
    $sql = "SELECT ArtistTitle as fav_artists from likes where UName = ? limit 25";
    return $sql;
}

function fetch_self_playlists() {
    $sql = "SELECT PlaylistName as self_playlist, PlaylistId from Playlist where UName = ?";
    return $sql;
}

function fetch_public_playlists() {
    $sql = "SELECT PlaylistName as self_playlist, PlaylistId from Playlist where UName = ? and is_private = 0";
    return $sql;
}

function fetch_other_users_playlists() {
    $sql = "SELECT PlaylistName as users_playlist from Playlist where UName = ? and Is_Private='0'";
    return $sql;
}

function fetch_playlistname() {
    $sql = "SELECT PlaylistName FROM Playlist  WHERE PlaylistId= ?";
    return $sql;
}

function fetch_playlisttracks() {
    $sql = "SELECT t.TrackId, t.TrackName, t.TrackDuration, ifnull(avg(r.Rating),0) as avg_rating,t.ArtistTitle from tracks t left outer join Rating r on r.TrackId = t.TrackId where t.TrackId IN (SELECT TrackId FROM Playlist P join PlayTracks PT WHERE P.PlaylistId=? and P.PlaylistId=PT.PlaylistId) group by t.TrackId order by avg_rating desc limit 25";
    return $sql;
}

function fetch_artistfromtracks() {
    $sql = "select ArtistTitle from tracks where TrackId=?";
    return $sql;
}

function fetch_searchtracks() {

    $sql = "SELECT t.TrackId, t.TrackName, t.ArtistTitle, t.TrackDuration, ifnull(avg(r.rating),0) as avg_rating from tracks t left outer join Rating r on r.TrackId = t.TrackId"
        . " left outer join PlayTracks pt on pt.TrackId = t.TrackId "
        . " cross join Playlist pl "
        . "where TrackName like ? and pl.UName = ? group by t.TrackId order by avg_rating desc LIMIT 10";
    return $sql;
}

function fetch_searchtracks_1() {

    $sql = "SELECT t.TrackId, t.TrackName, t.ArtistTitle, t.TrackDuration, ifnull(avg(r.rating),0) as avg_rating from tracks t left outer join Rating r on r.TrackId = t.TrackId "
        . "where TrackName like ? group by t.TrackId order by avg_rating desc LIMIT 10";
    return $sql;
}

function fetch_searchtracks1() {

    $sql = "SELECT t.TrackId, t.TrackName, t.ArtistTitle, t.TrackDuration, avg(rating) as avg_rating from tracks t join Rating r where TrackName like ? group by t.TrackId order by avg_rating desc LIMIT 50";
    return $sql;
}

function fetch_searchalbums() {
    $sql = "select AlbumId,AlbumName from albums where AlbumName like ? limit 12";
    return $sql;
}

function fetch_searchalbums1() {
    $sql = "select AlbumId,AlbumName from albums where AlbumName like ?";
    return $sql;
}

function fetch_searchartists() {
    $sql = "select ArtistTitle from artists where ArtistTitle like ? limit 12";
    return $sql;
}

function fetch_searchartists1() {
    $sql = "select ArtistTitle from artists where ArtistTitle like ?";
    return $sql;
}

function insert_into_followers() {
    $sql = "INSERT INTO `Followers` (UName, UFollowing, Ftime) values(?, ?, now())";
    return $sql;
}

function delete_from_followers() {
    $sql = "DELETE from Followers WHERE UName = ? and UFollowing = ?";
    return $sql;
}

function insert_into_play_history() {
    $sql = "INSERT INTO PlayHistory values(?, ?, ?, now())";
    return $sql;
}

function fetch_playlists_of_users_you_follow() {
    $sql = "select PlaylistId, PlaylistName from Playlist p join Followers f on p.UName = f.UFollowing  where f.UName = :uname and Is_Private = 0 limit :offset , :max_limit";
    return $sql;
}

function login() {
    $sql = "SELECT * FROM login_info WHERE uname=? AND pass=?";
    return $sql;
}

function insert_user() {
    $sql = "INSERT into user (UName,Name,Email,City) VALUES (?,?,?,?)";
    return $sql;
}

function insert_login_info() {
    $sql = "INSERT into login_info (UName,Pass) VALUES (?,?)";
    return $sql;
}

function album_info() {
    $sql = "select t.TrackId, t.TrackName, t.TrackDuration, t.ArtistTitle, a.AlbumName, pt.PlaylistId, ifnull(avg(r.rating),0) as avg_rating "
        . "from tracks t join albums a "
        . "left outer join Rating r on r.TrackId = t.TrackId "
        . "left outer join PlayTracks pt on pt.TrackId = t.TrackId "
        . "cross join Playlist p "
        . "where t.AlbumId=? and t.AlbumId=a.AlbumId and p.UName = ? "
        . "group by t.TrackId";
    return $sql;
}

function album_info_1() {
    $sql = "select t.TrackId, t.TrackName, t.TrackDuration, t.ArtistTitle, a.AlbumName, ifnull(avg(r.rating),0) as avg_rating "
        . "from tracks t join albums a "
        . "left outer join Rating r on r.TrackId = t.TrackId "
        . "where t.AlbumId=? and t.AlbumId=a.AlbumId "
        . "group by t.TrackId";
    return $sql;
}

function album_name() {
    $sql = "select AlbumName from albums where AlbumId=?";
    return $sql;
}

function insert_into_likes() {
    $sql = "INSERT INTO `Likes` (ArtistTitle, UName, ltime) values(?, ?, now())";
    return $sql;
}

function delete_from_likes() {
    $sql = "DELETE from Likes WHERE ArtistTitle = ? and UName = ?";
    return $sql;
}

function fetch_user_play_history() {
    $sql = "SELECT t.TrackId, t.TrackName, t.ArtistTitle, t.TrackDuration, ifnull(avg(r.rating),0) as avg_rating, pt.PlaylistId "
        . "from tracks t join PlayHistory p on t.TrackId = p.TrackId "
        . "left outer join Rating r on r.TrackId = p.TrackId  "
        . "left outer join PlayTracks pt on pt.TrackId = t.TrackId "
        . "cross join Playlist pl "
        . "where p.UName = :uname group by p.PTime "
        . "order by PTime desc LIMIT :offset , :max_limit";

    return $sql;
}

function fetch_user_play_history_1() {
    $sql = "SELECT t.TrackId, t.TrackName, t.ArtistTitle, t.TrackDuration, ifnull(avg(r.rating),0) as avg_rating "
        . "from tracks t join PlayHistory p on t.TrackId = p.TrackId "
        . "left outer join Rating r on r.TrackId = p.TrackId "
        . "where p.UName = :uname group by p.PTime "
        . "order by PTime desc LIMIT :offset , :max_limit";

    return $sql;
}

function fetch_best_songs() {
    $sql = "SELECT t.TrackId, t.TrackName, t.ArtistTitle, t.TrackDuration, avg(rating) as avg_rating, p.PlaylistId
            from tracks t 
            join Rating r on r.TrackId = t.TrackId
            left outer join PlayTracks pt on pt.TrackId = t.TrackId
            left outer join (select PlaylistId from Playlist where UName = :uname) p on p.PlaylistId = pt.PlaylistId
            group by t.TrackId
            order by avg_rating desc 
            LIMIT :offset, :max_limit";
    return $sql;
}

function fetch_best_songs_1() {
    $sql = "SELECT t.TrackId, t.TrackName, t.ArtistTitle, t.TrackDuration, avg(rating) as avg_rating, NULL AS PlaylistId
            from tracks t join Rating r on r.TrackId = t.TrackId
            group by t.TrackId
            order by avg_rating desc 
            LIMIT :offset, :max_limit";
    return $sql;
}

function fetch_songs_by_artist_you_like() {
    $sql = "SELECT x.TrackId, x.TrackName, x.ArtistTitle, x.TrackDuration, ifnull(avg(r.Rating),0) as avg_rating, pt.PlaylistId "
        . "FROM (SELECT t.TrackId, t.TrackName, t.ArtistTitle, t.TrackDuration "
        . "from tracks t join Likes l on (l.ArtistTitle = t.ArtistTitle) "
        . "where l.UName = :uname) as x left outer join Rating r on r.TrackId = x.TrackId "
        . "left outer join PlayTracks pt on pt.TrackId = x.TrackId "
        . "cross join Playlist p "
        . "group by x.TrackId order by avg_rating desc limit :offset , :max_limit";
    return $sql;
}

function fetch_songs_by_artist_you_like_1() {
    $sql = "SELECT x.TrackId, x.TrackName, x.ArtistTitle, x.TrackDuration, ifnull(avg(r.Rating),0) as avg_rating "
        . "FROM (SELECT t.TrackId, t.TrackName, t.ArtistTitle, t.TrackDuration "
        . "from tracks t join Likes l on (l.ArtistTitle = t.ArtistTitle) "
        . "where l.UName = :uname) as x left outer join Rating r on r.TrackId = x.TrackId "
        . "group by x.TrackId order by avg_rating desc limit :offset , :max_limit";
    return $sql;
}

function fetch_recent_albums() {
    $sql = "select AlbumId, AlbumName from albums order by AlbumReleaseDate desc limit :offset , :max_limit";
    return $sql;
}

function fetch_ratings_for_track() {
    $sql = "select TrackId, Rating from Rating where TrackId IN :all_tracks and UName = 'dj'";
    return $sql;
}

function insert_into_ratings_sql() {
    $sql = "insert into Rating values(?, ?, ?, now())";
    return $sql;
}

function insert_or_update_into_ratings_sql() {
    $sql = "insert into Rating values(?, ?, ?, now()) ON DUPLICATE KEY UPDATE Rating = ?, RTime = now()";
    return $sql;
}

function fetch_my_playlists() {
    $sql = "SELECT PlaylistId, PlaylistName from Playlist where UName = ?";
    return $sql;
}

function insert_into_playlist_tracks() {
    $sql = "INSERT into PlayTracks values(?, ?)";
    return $sql;
}

function fetch_search_users() {
    $sql = "SELECT UName, Name from user where UName like ? OR Name like ?";
    return $sql;
}

function insert_new_playlist() {
    $sql = "INSERT INTO Playlist values(?, ?, ?)";
    return $sql;
}