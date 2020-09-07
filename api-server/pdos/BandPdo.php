<?php

function getUserBand($userId)
{
    $pdo = pdoSqlConnect();
    $query = "select BandUser.bandId,
       bandName,
       bandImg,
       userType
from BandUser left join Band on
BandUser.bandId = Band.bandId
where BandUser.userId = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getBandInfo($bandId)
{
    $pdo = pdoSqlConnect();
    $query = "select bandImg,
        bandName,
        isOpened,
        count(userId) as countUser
from BandUser left join Band on Band.bandId = BandUser.bandId
where Band.bandId = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function createBand($bandName, $bandImg, $isOpened)
{
    $pdo = pdoSqlConnect();
    $query = "insert into Band (bandName, bandImg, isOpened)
    values (?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$bandName, $bandImg, $isOpened]);

    $query = "SELECT distinct last_insert_id() as bandId from Band";
    $st = $pdo->prepare($query);
    $res = $st->execute();
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["bandId"]);
}

function createFirstBandUser($bandId, $userId)
{
    $pdo = pdoSqlConnect();
    $query = "insert into BandUser (bandId, userId, userType)
    values (?, ?, '리더');";
    $st = $pdo->prepare($query);
    $st->execute([$bandId, $userId]);

    $query = "SELECT date_format(createdAt, '%Y년 %m월 %d일') as sinceLeaderDate
from BandUser
where bandId = ? and userId = ?;";
    $st = $pdo->prepare($query);
    $res = $st->execute([$bandId, $userId]);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    return $res[0]["sinceLeaderDate"];
}

function getOriginalProfile($bandId)
{
    $pdo = pdoSqlConnect();
    $query = "select bandName,
        bandImg,
        color
from Band
where bandId = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getBandDetail($bandId)
{
    $pdo = pdoSqlConnect();
    $query = "select Band.bandId,
       Band.bandName,
       Band.isOpened,
       Band.color,
       IF(BC.bandMemberNo is NULL, 0, BC.bandMemberNo) as bandMemberNo,
       Band.bandImg,
       IF(Band.bandIntroduction is NULL, 'NULL', Band.bandIntroduction) as bandIntroduction,
       date_format(Band.createdAt, '%Y년 %m월') as createdAt,
       Band.restrictMemberNo,
       IF(BandRegisterQuestion.registerQuestion is NULL, 'NULL', BandRegisterQuestion.registerQuestion) as registerQuestion,
       IF(BandRegisterGender.gender is NULL, 'NULL', BandRegisterGender.gender) as registerGender,
       IF(BandRegisterCondition.minAge is NULL, 0, BandRegisterCondition.minAge) as minAge,
       IF(BandRegisterCondition.maxAge is NULL, 0, BandRegisterCondition.maxAge) as maxAge,
       Band.isRegisterNoticed,
       Band.isSecretAvailable

from Band left join (select bandId, count(*) as bandMemberNo
from BandUser group by bandId) as BC
on Band.bandId = BC.bandId
left join BandRegisterQuestion
on Band.bandId = BandRegisterQuestion.bandId
left join BandRegisterGender
on Band.bandId = BandRegisterGender.bandId
left join BandRegisterCondition
on Band.bandId = BandRegisterCondition.bandId
where Band.bandId = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function updateBandProfile($bandId, $bandName, $bandImg, $color)
{
    $pdo = pdoSqlConnect();
    $query = "update Band
set bandName = ?, bandImg = ?, color = ?
where bandId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$bandName, $bandImg, $color, $bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;

}

function createEnterpriseBand($bandId, $companyName, $headName, $address, $phone, $email, $companyRegisterNo, $saleRegisterNo)
{
    $pdo = pdoSqlConnect();
    $query = "insert into EnterpriseBand (bandId, companyName, headName, address,
            phone, email, companyRegisterNo, saleRegisterNo)
    values (?, ?, ?, ?, ?, ?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$bandId, $companyName, $headName, $address, $phone, $email, $companyRegisterNo, $saleRegisterNo]);

    $st = null;
    $pdo = null;

}

function updateBandIntroduction($bandId, $bandIntroduction)
{
    $pdo = pdoSqlConnect();
    $query = "update Band
set bandIntroduction = ?
where bandId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$bandIntroduction, $bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;

}

function createBandEnter($bandId)
{
    $pdo = pdoSqlConnect();
    $query = "insert into BandEnter (bandId) values (?);";
    $st = $pdo->prepare($query);
    $st->execute([$bandId]);

    $st = null;
    $pdo = null;

}

function updateBandMember($bandId, $restrictMemberNo)
{
    $pdo = pdoSqlConnect();
    $query = "update Band
set restrictMemberNo = ?
where bandId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$restrictMemberNo, $bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;

}

function createBandRestrictAge($bandId, $minAge, $maxAge)
{
    $pdo = pdoSqlConnect();
    $query = "insert into BandRegisterCondition (bandId, minAge, maxAge) values (?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$bandId, $minAge, $maxAge]);

    $st = null;
    $pdo = null;

}

function createBandRestrictGender($bandId, $gender)
{
    $pdo = pdoSqlConnect();
    $query = "insert into BandRegisterGender (bandId, gender) values (?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$bandId, $gender]);

    $st = null;
    $pdo = null;

}

function getBandTag($bandId)
{
    $pdo = pdoSqlConnect();
    $query = "select tagContent
from BandTag
where bandId = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function createBandTag($bandId, $tagContent)
{
    $pdo = pdoSqlConnect();
    $query = "insert into BandTag (bandId, tagContent) values (?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$bandId, $tagContent]);

    $st = null;
    $pdo = null;

}

function isValidBandUser($bandId, $userId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM BandUser WHERE bandId = ? and userId = ?) AS exist;";


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
    $query = "SELECT EXISTS(SELECT * FROM BandUser WHERE bandId = ? and userId = ? and userType = '리더') AS exist;";


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
    $query = "SELECT EXISTS(SELECT * FROM Band WHERE bandId = ? and to_days(now())-to_days(updatedAt) >= 1) AS exist;";


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