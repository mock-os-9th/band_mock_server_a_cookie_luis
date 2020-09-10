<?php
function createPost($userId, $bandId, $text, $media, $tag, $file){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO BandPost (userId, bandId, postContent, mediaUrl, fileUrl, tagContent) values (?, ?, ?, ?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$userId, $bandId, $text, $media, $file, $tag]);

    $st = null;
    $pdo = null;
}

function createComment($userId, $postId, $parentCommentId, $text, $media, $file, $emoticonId){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO BandComment (userId, postId, parentCommentId, commentContent, emoticonId, mediaUrl, fileUrl) values (?, ?, ?, ?, ?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->bindParam(1, $userId, PDO::PARAM_INT);
    $st->bindParam(2, $postId, PDO::PARAM_INT);
    $st->bindParam(3, $parentCommentId, PDO::PARAM_INT);
    $st->bindParam(4, $text, PDO::PARAM_STR);
    $st->bindParam(5, $emoticonId, PDO::PARAM_INT);
    $st->bindParam(6, $media, PDO::PARAM_STR);
    $st->bindParam(7, $file, PDO::PARAM_STR);
    $st->execute();

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
    $page *= 10;
    $query = "SELECT bp.postId as postId, u.userId as userId, replace(u.name, '\r', '') as userName, u.profileImg as userProfile, CASE
    WHEN TIMESTAMPDIFF(MINUTE, bp.createdAt, current_timestamp) < 1 THEN '지금 막'
    WHEN TIMESTAMPDIFF(MINUTE, bp.createdAt, current_timestamp) < 60 THEN concat(TIMESTAMPDIFF(MINUTE, bp.createdAt, current_timestamp), '분 전')
    WHEN TIMESTAMPDIFF(HOUR, bp.createdAt, current_timestamp) < 12 THEN concat(TIMESTAMPDIFF(HOUR, bp.createdAt, current_timestamp), '시간 전')
    ELSE DATE_FORMAT(bp.createdAt, '%Y년 %m월%d일 %H:%i')
    END AS postCreatedAt,
        bp.createdAt as postCreatedAt, replace(postContent, '\r', '') as postContent, replace(bp.mediaUrl, '\r', '') as mediaUrl, replace(bp.fileUrl, '\r', '') as fileUrl, replace(tagContent, '\r', '') as tagContent, numOfComment, numOfView, numOfExpression, u.userId as commentUserId, replace(commentUserName, '\r', '') as commentUserName, CASE
    WHEN TIMESTAMPDIFF(MINUTE, firstComment.commentCreatedAt, current_timestamp) < 60 THEN concat(TIMESTAMPDIFF(MINUTE, firstComment.commentCreatedAt, current_timestamp), '분 전')
    WHEN TIMESTAMPDIFF(HOUR, firstComment.commentCreatedAt, current_timestamp) < 12 THEN concat(TIMESTAMPDIFF(HOUR, firstComment.commentCreatedAt, current_timestamp), '시간 전')
    ELSE DATE_FORMAT(firstComment.commentCreatedAt, '%Y년 %m월%d일 %H:%i')
    END AS commentCreatedAt, replace(commentContent, '\r', '') as commentContent
from User as u inner join BandPost as bp on u.userId = bp.userId
left join (SELECT postId, count(postId) as numOfComment from BandComment where isDeleted = 'N' group by postId) as commentNo on bp.postId = commentNo.postId
left join (SELECT postId, count(postId) as numOfView from BandPostView group by postId) as viewNo on bp.postId = viewNo.postId
left join (SELECT postId, count(postId) as numOfExpression from BandPostExpression group by postId) as expressionNo on bp.postId = expressionNo.postId
left join (SELECT bc.postId as postId, u.userId as commentUserId, name as commentUserName, bc.createdAt as commentCreatedAt, commentContent from User as u
left join BandComment as bc on u.userId = bc.userId where commentId in (Select max(commentId) as commentId from BandComment where isDeleted = 'N' group by postId)) as firstComment on bp.postId = firstComment.postId
where bp.bandId = ? and bp.isDeleted = 'N' order by bp.createdAt desc limit ?, 10;";
    $st = $pdo->prepare($query);
    $st->bindParam(1, $bandId, PDO::PARAM_INT);
    $st->bindParam(2, $page, PDO::PARAM_INT);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $query = "SELECT u.userId as userId, name as userName, profileImg, bpe.expressionId as expressionId, replace(expressionImg, '\r', '') as expressionImg from BandPostExpression as bpe inner join Expression as e on bpe.expressionId = e.expressionId inner join User as u on u.userId = bpe.userId where postId = ?;";
    $st = $pdo->prepare($query);
    foreach($res as &$value){
        $st->execute([$value['postId']]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $value['expressionList'] = $st->fetchAll();
    }

    $st = null;
    $pdo = null;

    return $res;
}

function getPostComment($postId, $page){
    $pdo = pdoSqlConnect();
    $page *= 10;
    $query = "SELECT bc.commentId as commentId, parentCommentId, u.userId as userId, replace(u.name, '\r', '') as userName, u.profileImg as userProfile, CASE
    WHEN TIMESTAMPDIFF(MINUTE, bc.createdAt, current_timestamp) < 1 THEN '지금 막'
    WHEN TIMESTAMPDIFF(MINUTE, bc.createdAt, current_timestamp) < 60 THEN concat(TIMESTAMPDIFF(MINUTE, bc.createdAt, current_timestamp), '분 전')
    WHEN TIMESTAMPDIFF(HOUR, bc.createdAt, current_timestamp) < 12 THEN concat(TIMESTAMPDIFF(HOUR, bc.createdAt, current_timestamp), '시간 전')
    ELSE DATE_FORMAT(bc.createdAt, '%Y년 %m월%d일 %H:%i')
    END AS commentCreatedAt,
        replace(commentContent, '\r', '') as commentContent, replace(bc.mediaUrl, '\r', '') as mediaUrl, replace(bc.fileUrl, '\r', '') as fileUrl, replace(emoticonUrl, '\r', '') as emoticonUrl, numOfExpression
from User as u inner join BandComment as bc on u.userId = bc.userId left join Emoticon as e on bc.emoticonId = e.emoticonId
left join (SELECT commentId, count(commentId) as numOfExpression from BandCommentExpression group by commentId) as expressionNo on bc.commentId = expressionNo.commentId
where bc.postId = ? and bc.isDeleted = 'N' order by bc.createdAt desc limit ?, 10;";
    $st = $pdo->prepare($query);
    $st->bindParam(1, $postId, PDO::PARAM_INT);
    $st->bindParam(2, $page, PDO::PARAM_INT);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $query = "SELECT u.userId as userId, name as userName, profileImg, bce.expressionId as expressionId, replace(expressionImg, '\r', '') as expressionImg from BandCommentExpression as bce inner join Expression as e on bce.expressionId = e.expressionId inner join User as u on u.userId = bce.userId where commentId = ?;";
    $st = $pdo->prepare($query);
    foreach($res as &$value){
        $st->execute([$value['commentId']]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $value['expressionList'] = $st->fetchAll();
    }

    $st = null;
    $pdo = null;

    return $res;
}

function modifyPost($postId, $text, $media, $tag, $file){
    $pdo = pdoSqlConnect();
    $query = "update BandPost SET postContent = ?, mediaUrl = ?, fileUrl = ?, tagContent = ? where postId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$text, $media, $file, $tag, $postId]);

    $st = null;
    $pdo = null;
}

function modifyComment($commentId, $text, $media, $file, $emoticonId){
    $pdo = pdoSqlConnect();
    $query = "update BandComment SET commentContent = ?, mediaUrl = ?, fileUrl = ?, emoticonId = ? where commentId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$text, $media, $file, $emoticonId, $commentId]);

    $st = null;
    $pdo = null;
}

function deletePost($postId){
    $pdo = pdoSqlConnect();
    // 게시글 삭제
    $query = "update BandPost SET isDeleted = 'Y' where postId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$postId]);

    // 게시글 표정 삭제
    $query = "DELETE from BandPostExpression where postId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$postId]);

    // 게시글 댓글 표정 삭제
    $query = "SELECT commentId from BandComment where isDeleted = 'N' and postId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$postId]);
    $st->setFetchMode(PDO::FETCH_NUM);
    $res = $st->fetchAll();
    $query = "DELETE from BandCommentExpression where commentId = ?;";
    foreach($res as $value){
        $st = $pdo->prepare($query);
        $st->execute($value);
    }

    // 게시글 댓글 삭제
    $query = "update BandComment SET isDeleted = 'Y' where postId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$postId]);

    $st = null;
    $pdo = null;
}

function deleteComment($commentId){
    $pdo = pdoSqlConnect();
    $query = "update BandComment SET isDeleted = 'Y' where commentId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$commentId]);

    $query = "DELETE from BandCommentExpression where commentId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$commentId]);

    $st = null;
    $pdo = null;
}

function deletePostExpression($postId, $userId){
    $pdo = pdoSqlConnect();
    $query = "DELETE from BandPostExpression where postId = ? and userId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$postId, $userId]);

    $st = null;
    $pdo = null;
}

function deleteCommentExpression($commentId, $userId){
    $pdo = pdoSqlConnect();
    $query = "DELETE from BandCommentExpression where commentId = ? and userId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$commentId, $userId]);

    $st = null;
    $pdo = null;
}

function createBookmark($postId, $userId){
    $pdo = pdoSqlConnect();
    $query = "INSERT into Bookmark (postId, userId) values (?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$postId, $userId]);

    $st = null;
    $pdo = null;
}

function deleteBookmark($postId, $userId){
    $pdo = pdoSqlConnect();
    $query = "DELETE from Bookmark where postId = ? and userId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$postId, $userId]);

    $st = null;
    $pdo = null;
}

function createBlockedBandUser($bandId, $userId, $blockedUserId){
    $pdo = pdoSqlConnect();
    $query = "INSERT into UserBlockedBandUser (bandId, userId, blockedUserId) values (?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$bandId, $userId, $blockedUserId]);

    $st = null;
    $pdo = null;
}

function getNewPostFeed($userId, $page){
    $pdo = pdoSqlConnect();
    $page *= 10;
    $query = "SELECT bp.postId as postId, u.userId as userId, replace(u.name, '\r', '') as userName, u.profileImg as userProfile, CASE
    WHEN TIMESTAMPDIFF(MINUTE, bp.createdAt, current_timestamp) < 1 THEN '지금 막'
    WHEN TIMESTAMPDIFF(MINUTE, bp.createdAt, current_timestamp) < 60 THEN concat(TIMESTAMPDIFF(MINUTE, bp.createdAt, current_timestamp), '분 전')
    WHEN TIMESTAMPDIFF(HOUR, bp.createdAt, current_timestamp) < 12 THEN concat(TIMESTAMPDIFF(HOUR, bp.createdAt, current_timestamp), '시간 전')
    ELSE DATE_FORMAT(bp.createdAt, '%Y년 %m월%d일 %H:%i')
    END AS postCreatedAt,
        replace(postContent, '\r', '') as postContent, replace(bp.mediaUrl, '\r', '') as mediaUrl, replace(bp.fileUrl, '\r', '') as fileUrl, replace(tagContent, '\r', '') as tagContent, numOfComment, numOfView, numOfExpression, u.userId as commentUserId, replace(commentUserName, '\r', '') as commentUserName, CASE
    WHEN TIMESTAMPDIFF(MINUTE, firstComment.commentCreatedAt, current_timestamp) < 60 THEN concat(TIMESTAMPDIFF(MINUTE, firstComment.commentCreatedAt, current_timestamp), '분 전')
    WHEN TIMESTAMPDIFF(HOUR, firstComment.commentCreatedAt, current_timestamp) < 12 THEN concat(TIMESTAMPDIFF(HOUR, firstComment.commentCreatedAt, current_timestamp), '시간 전')
    ELSE DATE_FORMAT(firstComment.commentCreatedAt, '%Y년 %m월%d일 %H:%i')
    END AS commentCreatedAt, replace(commentContent, '\r', '') as commentContent
from User as u inner join BandPost as bp on u.userId = bp.userId
left join (SELECT postId, count(postId) as numOfComment from BandComment where isDeleted = 'N' group by postId) as commentNo on bp.postId = commentNo.postId
left join (SELECT postId, count(postId) as numOfView from BandPostView group by postId) as viewNo on bp.postId = viewNo.postId
left join (SELECT postId, count(postId) as numOfExpression from BandPostExpression group by postId) as expressionNo on bp.postId = expressionNo.postId
left join (SELECT bc.postId as postId, u.userId as commentUserId, name as commentUserName, bc.createdAt as commentCreatedAt, commentContent from User as u
left join BandComment as bc on u.userId = bc.userId where commentId in (Select max(commentId) as commentId from BandComment where isDeleted = 'N' group by postId)) as firstComment on bp.postId = firstComment.postId
where bp.bandId in (SELECT bandId from BandUser where userId = ?) and bp.isDeleted = 'N' order by bp.createdAt desc limit ?, 10;";
    $st = $pdo->prepare($query);
    $st->bindParam(1, $userId, PDO::PARAM_INT);
    $st->bindParam(2, $page, PDO::PARAM_INT);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $query = "SELECT u.userId as userId, name as userName, profileImg, bpe.expressionId as expressionId, replace(expressionImg, '\r', '') as expressionImg from BandPostExpression as bpe inner join Expression as e on bpe.expressionId = e.expressionId inner join User as u on u.userId = bpe.userId where postId = ?;";
    $st = $pdo->prepare($query);
    foreach($res as &$value){
        $st->execute([$value['postId']]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $value['expressionList'] = $st->fetchAll();
    }

    $st = null;
    $pdo = null;

    return $res;
}

function createHiddenPost($postId, $userId){
    $pdo = pdoSqlConnect();
    $query = "INSERT into UserHidePost (postId, userId) values (?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$postId, $userId]);

    $st = null;
    $pdo = null;
}

function createBlockedBand($bandId, $userId){
    $pdo = pdoSqlConnect();
    $query = "INSERT into UserBlockedBand (bandId, userId) values (?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$bandId, $userId]);

    $st = null;
    $pdo = null;
}
