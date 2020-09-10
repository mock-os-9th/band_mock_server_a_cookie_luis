<?php

function getAd()
{
    $pdo = pdoSqlConnect();
    $query = "select adsId,
        adsMainImg,
       adsUrl
from Ads
order by rand() limit 1;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function isValidUser($id){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE userId= ? and isDeleted = 'N') AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}

function isExistPhone($phone){
    $pdo = pdoSqlConnect();
    $query = "SELECT exists(select userId from User where phone = ?) as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$phone]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function isExistEmail($phone){
    $pdo = pdoSqlConnect();
    $query = "SELECT exists(select userId from User where email = ?) as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$phone]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function isValidAdsId($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM Ads WHERE adsId = ?) AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function isValidUsersId($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE userId = ? and isDeleted = 'N') AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);

}

function isValidNaverUser($naverId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE naverId = ?) AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$naverId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);

}


function isValidEmailUser($email, $password)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT password FROM User WHERE email = ? and isDeleted = 'N';";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return password_verify($password, $res[0]["password"]);

}

function getIdFromEmailPw($email)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT userId FROM User WHERE email = ? and isDeleted = 'N';";

    $st = $pdo->prepare($query);
    $st->execute([$email]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["userId"]);
}

function isValidPhoneUser($phone, $password)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT password FROM User WHERE phone = ? and isDeleted = 'N';";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$phone]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return password_verify($password, $res[0]["password"]);

}

function getIdFromPhonePw($phone)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT userId FROM User WHERE phone = ? and isDeleted = 'N';";

    $st = $pdo->prepare($query);
    $st->execute([$phone]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["userId"]);
}

function isValidBandId($bandId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM Band WHERE bandId = ? and isDeleted = 'N') AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function isValidPostId($postId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM BandPost WHERE postId = ? and isDeleted = 'N') AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$postId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function isValidCommentId($commentId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM BandComment WHERE commentId = ? and isDeleted = 'N') AS exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$commentId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function isValidParentCommentId($parentCommentId, $postId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT postId FROM BandComment WHERE commentId = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$parentCommentId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["postId"]) == $postId;
}

function isValidEmoticonId($emoticonId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM Emoticon WHERE emoticonId = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$emoticonId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function isValidExpressionId($expressionId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM Expression WHERE expressionId = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$expressionId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function isExistUserExpressionOnPost($postId, $userId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM BandPostExpression WHERE postId = ? and userId = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$postId, $userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function isExistUserExpressionOnComment($commentId, $userId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM BandCommentExpression WHERE commentId = ? and userId = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$commentId, $userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}


function isValidBandUser($bandId, $userId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM BandUser WHERE bandId = ? and userId = ? and isDeleted = 'N') AS exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$bandId, $userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);

}

function isValidBandUserLeaderID($bandId, $userId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM BandUser WHERE bandId = ? and userId = ? and userType = '리더' and isDeleted = 'N') AS exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$bandId, $userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);

}


function isValidChangeMemberNo($bandId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM Band WHERE bandId = ? and to_days(now())-to_days(updatedAt) >= 1 and isDeleted = 'N') AS exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);

}

function isValidRestrictAge($minAge, $maxAge)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT IF(?-? > 0, 0, 1) as exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$minAge, $maxAge]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);

}

function isAlreadyExistBandIdAge($bandId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM BandRegisterCondition WHERE bandId = ?) AS exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);

}

function isValidRestrictGender($gender)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT IF('F' = ? or 'M' = ?, 1, 0) as exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$gender, $gender]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);

}

function isAlreadyExistBandIdGender($bandId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM BandRegisterGender WHERE bandId = ?) AS exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);

}

function isAlreadyExistBandTag($bandId, $tagContent)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM BandTag WHERE bandId = ? and tagContent = ?) AS exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$bandId, $tagContent]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);

}

function isExistPostExpression($postId, $userId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM BandPostExpression WHERE postId = ? and userId = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$postId, $userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);
}

function isExistCommentExpression($commentId, $userId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM BandCommentExpression WHERE commentId = ? and userId = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$commentId, $userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);
}

function isExistBookmark($postId, $userId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM Bookmark WHERE postId = ? and userId = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$postId, $userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);
}

function isExistUserBandBlockedUser($bandId, $userId, $blockedUserId){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM UserBlockedBandUser WHERE bandId = ? and userId = ? and blockedUserId = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$bandId, $userId, $blockedUserId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);
}

function isExistHiddenPost($postId, $userId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM UserHidePost WHERE postId = ? and userId = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$postId, $userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);
}

function isExistUserBlockedBand($bandId, $userId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM UserBlockedBand WHERE bandId = ? and userId = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$bandId, $userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return intval($res[0]["exist"]);
}

// CREATE
//    function addMaintenance($message){
//        $pdo = pdoSqlConnect();
//        $query = "INSERT INTO MAINTENANCE (MESSAGE) VALUES (?);";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message]);
//
//        $st = null;
//        $pdo = null;
//
//    }


// UPDATE
//    function updateMaintenanceStatus($message, $status, $no){
//        $pdo = pdoSqlConnect();
//        $query = "UPDATE MAINTENANCE
//                        SET MESSAGE = ?,
//                            STATUS  = ?
//                        WHERE NO = ?";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message, $status, $no]);
//        $st = null;
//        $pdo = null;
//    }

// RETURN BOOLEAN
//    function isRedundantEmail($email){
//        $pdo = pdoSqlConnect();
//        $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE EMAIL= ?) AS exist;";
//
//
//        $st = $pdo->prepare($query);
//        //    $st->execute([$param,$param]);
//        $st->execute([$email]);
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res = $st->fetchAll();
//
//        $st=null;$pdo = null;
//
//        return intval($res[0]["exist"]);
//
//    }
