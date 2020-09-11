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
where BandUser.userId = ? and BandUser.isDeleted = 'N';";

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
    $query = "select Band.bandId,
       Band.bandName,
       Band.isOpened,
       Band.color,
       IF(BC.bandMemberNo is NULL, 0, BC.bandMemberNo) as bandMemberNo,
       Band.bandImg,
       IF(Band.bandIntroduction is NULL, 'NULL', Band.bandIntroduction) as bandIntroduction,
       date_format(Band.createdAt, '%Y년 %m월') as createdAt

from Band left join (select bandId, count(*) as bandMemberNo
from BandUser where isDeleted = 'N' group by bandId) as BC
on Band.bandId = BC.bandId
where Band.bandId = ? and Band.isDeleted = 'N';";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
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
where bandId = ? and Band.isDeleted = 'N';";

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
from BandUser where isDeleted = 'N' group by bandId) as BC
on Band.bandId = BC.bandId
left join BandRegisterQuestion
on Band.bandId = BandRegisterQuestion.bandId
left join BandRegisterGender
on Band.bandId = BandRegisterGender.bandId
left join BandRegisterCondition
on Band.bandId = BandRegisterCondition.bandId
where Band.bandId = ? and Band.isDeleted = 'N';";

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
where bandId = ? and isDeleted = 'N';";

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

function getBandUser($bandId)
{
    $pdo = pdoSqlConnect();
    $query = "select User.userId,
       name,
       profileImg,
       phone,
       userType

from BandUser left join User
on BandUser.userId = User.userId
where BandUser.bandId = ? and BandUser.isDeleted = 'N';";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$bandId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function updateBandLeader($bandId, $leaderId, $normalId)
{
    $pdo = pdoSqlConnect();
    $query = "update BandUser
set userType = '리더'
where bandId = ? and userId = ?;
update BandUser
set userType = '일반'
where bandId = ? and userId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$bandId, $normalId, $bandId, $leaderId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;

}

function getBandSearch($content, $page)
{
    $pdo = pdoSqlConnect();
    $query = "select Band.bandId,
       Band.bandName,
       IF(BC.bandMemberNo is NULL, 0, BC.bandMemberNo) as bandMemberNo,
       Band.bandImg,
       IF(Band.bandIntroduction is NULL, 'NULL', Band.bandIntroduction) as bandIntroduction,
       LN.name as leaderName

from Band left join (select bandId, count(*) as bandMemberNo
from BandUser group by bandId) as BC
on Band.bandId = BC.bandId
left join (select BandUser.bandId, User.userId, User.name
from BandUser left join User
on BandUser.userId = User.userId
where userType = '리더') as LN
on Band.bandId = LN.bandId

where Band.isOpened = 'Y' and Band.isDeleted = 'N' and Band.bandName like concat('%',?,'%')
order by Band.createdAt limit 10 offset ?;";

    $st = $pdo->prepare($query);
    $st->bindParam(1, $content, PDO::PARAM_STR);
    $st->bindParam(2, $page, PDO::PARAM_INT);
    $st->execute();
    //    $st->execute([$param,$param]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getBestBand()
{
    $pdo = pdoSqlConnect();
    $query = "select Band.bandId,
       Band.bandName,
       IF(BN.bandMemberNo is NULL, 0, BN.bandMemberNo) as bandMemberNo,
       Band.bandImg,
       IF(Band.bandIntroduction is NULL, 'NULL', Band.bandIntroduction) as bandIntroduction

from Band left join (select bandId, count(*) as bandMemberNo
from BandUser group by bandId) as BN
on Band.bandId = BN.bandId
left join (select bandId, count(*) as enterCount
from BandEnter where datediff(now(), createdAt) <= 14
group by bandId) as BE
on Band.bandId = BE.bandId
where Band.isDeleted = 'N'
order by enterCount desc limit 4;";

    $st = $pdo->prepare($query);
    $st->execute();
    //    $st->execute([$param,$param]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function deleteBandUser($bandId, $userId)
{
    $pdo = pdoSqlConnect();
    $query = "update BandUser
set isDeleted = 'Y'
where bandId = ? and userId = ?";
    $st = $pdo->prepare($query);
    $st->execute([$bandId, $userId]);

    $st = null;
    $pdo = null;

}

function deleteBand($bandId, $userId)
{
    $pdo = pdoSqlConnect();
    $query = "update BandUser
set isDeleted = 'Y'
where bandId = ? and userId = ?";
    $st = $pdo->prepare($query);
    $st->execute([$bandId, $userId]);

    $st = null;
    $pdo = null;

}

function sendBandAnnualFCM()
{
    $pdo = pdoSqlConnect();
    $query = "select User.fcmToken,
        Band.bandName,
       IF(YEAR(now())-YEAR(Band.createdAt) >= 1, concat(concat(Band.bandName, ' 밴드가 '), concat(YEAR(now())-YEAR(Band.createdAt), '주년 입니다.')), 0) as data

from BandUser left join Band
on BandUser.bandId = Band.bandId
left join User
on BandUser.userId = User.userId

where if((MONTH(now()) = MONTH(Band.createdAt)) and (DAY(now()) = DAY(Band.createdAt)), YEAR(now())-YEAR(Band.createdAt), 0) = 1;";

    $st = $pdo->prepare($query);
    $st->execute();
    //    $st->execute([$param,$param]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}