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

function createComment($userId, $postId, $parentCommentId, $text, $media, $file, $emoticonId){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO BandComment (userId, postId, parentCommentId, commentContent, emoticonId) values (?, ?, ?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$userId, $postId, $parentCommentId, $text, $emoticonId]);

    $query = "SELECT max(commentId) as id from BandComment;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $commentId = $st->fetchAll()[0]["id"];

    if(!empty($media)){
        $query = "INSERT INTO BandCommentMedia (commentId, mediaUrl) values (?, ?);";
        $st = $pdo->prepare($query);
        $st->execute([$commentId, $media]);
    }
    if(!empty($file)){
        $query = "INSERT INTO BandCommentFile (commentId, fileUrl) values (?, ?);";
        $st = $pdo->prepare($query);
        $st->execute([$commentId, $file]);
    }

    $st = null;
    $pdo = null;
}

function createExpression($userId, $postId, $commentId, $expressionId){
    $res = (Object)Array();
    $pdo = pdoSqlConnect();
    // 게시글일 경우
    if(!empty($postId)) {
        // 이미 게시글에 해당 유저의 표정이 존재할 경우
        if (isExistUserExpressionOnPost($postId, $userId)) {
            $query = "UPDATE BandPostExpression set expressionId = ? where postId = ? and userId = ?;";
            $st = $pdo->prepare($query);
            $st->execute([$expressionId, $postId, $userId]);
            $res->code = 101;
            $res->message = "이미 존재하는 표정 수정 성공";
        }
        // 존재하지 않을 경우
        else{
            $query = "INSERT INTO BandPostExpression (userId, postId, expressionId) values (?, ?, ?);";
            $st = $pdo->prepare($query);
            $st->execute([$userId, $postId, $expressionId]);
            $res->code = 100;
            $res->message = "표정 생성 성공";
        }
    }
    // 댓글일 경우
    else{
        // 이미 댓글에 해당 유저의 표정이 존재할 경우
        if (isExistUserExpressionOnComment($commentId, $userId)) {
            $query = "UPDATE BandCommentExpression set expressionId = ? where commentId = ? and userId = ?;";
            $st = $pdo->prepare($query);
            $st->execute([$expressionId, $commentId, $userId]);
            $res->code = 101;
            $res->message = "이미 존재하는 표정 수정 성공";
        }
        // 존재하지 않을 경우
        else{
            $query = "INSERT INTO BandCommentExpression (userId, commentId, expressionId) values (?, ?, ?);";
            $st = $pdo->prepare($query);
            $st->execute([$userId, $commentId, $expressionId]);
            $res->code = 100;
            $res->message = "표정 생성 성공";
        }
    }

    $st = null;
    $pdo = null;

    return $res;
}

function getBandPost($bandId, $page){
    $pdo = pdoSqlConnect();
    $query = "SELECT u.userId as userId, replace(u.name, '\r', '') as userName, u.profileImg as userProfile, bp.createdAt as postCreatedAt, replace(postContent, '\r', '') as postContent, replace(mediaUrl, '\r', '') as mediaUrl, replace(fileUrl, '\r', '') as fileUrl, replace(tagContent, '\r', '') as tagContent, numOfComment, numOfView, numOfExpression, u.userId as commentUserId, replace(name, '\r', '') as commentUserName, temp4.commentCreatedAt as commentCreatedAt, replace(commentContent, '\r', '') as commentContent
from User as u inner join BandPost as bp on u.userId = bp.userId
inner join (SELECT postId, count(postId) as numOfComment from BandComment group by postId) as temp on bp.postId = temp.postId
inner join (SELECT postId, count(postId) as numOfView from BandPostView group by postId) as temp2 on bp.postId = temp2.postId
inner join (SELECT postId, count(postId) as numOfExpression from BandPostExpression group by postId) as temp3 on bp.postId = temp3.postId
inner join (SELECT bc.postId as postId, u.userId as commentUserId, name as commentUserName, bc.createdAt as commentCreatedAt, commentContent from User as u
inner join BandComment as bc on u.userId = bc.userId inner join (Select postId, max(commentId) as commentId from BandComment group by postId) as pc on bc.commentId = pc.commentId) as temp4 on bp.postId = temp4.postId
left join BandPostMedia as bpm on bp.postId = bpm.postId left join BandPostFile as bpf on bp.postId = bpf.postId left join BandPostTag as bpt on bp.postId = bpt.postId
where bp.bandId = ? order by bp.createdAt desc limit 0, 20;";
    $st = $pdo->prepare($query);
    $st->execute([$bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}