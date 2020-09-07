<?php
function createPost($userId, $bandId, $text, $media, $tag, $file){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO BandPost (userId, bandId, postContent) values (?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$userId, $bandId, $text]);

    $query = "SELECT max(postId) as id from BandPost;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $postId = $st->fetchAll()[0]["id"];

    if(!empty($media)){
        $query = "INSERT INTO BandPostMedia (postId, mediaUrl) values (?, ?);";
        $st = $pdo->prepare($query);
        $st->execute([$postId, $media]);
    }
    if(!empty($file)){
        $query = "INSERT INTO BandPostFile (postId, fileUrl) values (?, ?);";
        $st = $pdo->prepare($query);
        $st->execute([$postId, $file]);
    }
    if(!empty($tag)){
        $query = "INSERT INTO BandPostTag (postId, tagContent) values (?, ?);";
        $st = $pdo->prepare($query);
        $st->execute([$postId, $tag]);
    }

    $st = null;
    $pdo = null;
}