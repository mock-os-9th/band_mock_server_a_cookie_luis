<?php

function getExistUserByNaver($token)
{
    $token = str_replace(" ", "+", $token);
    $header = "Bearer ".$token; // Bearer 다음에 공백 추가
    $url = "https://openapi.naver.com/v1/nid/me";
    $is_post = false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, $is_post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array();
    $headers[] = "Authorization: ".$header;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = json_decode(curl_exec ($ch));
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $res->code = $status_code;
    curl_close ($ch);

    $st = null;
    $pdo = null;

    return $res;
}

function isValidToken($token)
{
    $token = str_replace(" ", "+", $token);
    $header = "Bearer ".$token; // Bearer 다음에 공백 추가
    $url = "https://openapi.naver.com/v1/nid/me";
    $is_post = false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, $is_post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array();
    $headers[] = "Authorization: ".$header;
    //$response = curl_exec ($ch);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = json_decode(curl_exec ($ch));
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);
    
    return $status_code;
}

function getNaverId($token)
{
    $token = str_replace(" ", "+", $token);
    $header = "Bearer ".$token; // Bearer 다음에 공백 추가
    $url = "https://openapi.naver.com/v1/nid/me";
    $is_post = false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, $is_post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array();
    $headers[] = "Authorization: ".$header;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = json_decode(curl_exec ($ch));
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);

    $st = null;
    $pdo = null;
    $naverId = $res->response->id;

    return intval($naverId);
}

function getUserIdPhoneFromNaverId($naverId){
    $pdo = pdoSqlConnect();
    $query = "SELECT userId, phone from User where naverId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$naverId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getExistUserByPhone($phone){
    $pdo = pdoSqlConnect();
    $query = "SELECT profileImg, name from User where phone = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$phone]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function getExistUserByEmail($email){
    $pdo = pdoSqlConnect();
    $query = "SELECT profileImg, name from User where email = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$email]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function getNaverUserInfo($token)
{
    $token = str_replace(" ", "+", $token);
    $header = "Bearer ".$token; // Bearer 다음에 공백 추가
    $url = "https://openapi.naver.com/v1/nid/me";
    $is_post = false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, $is_post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array();
    $headers[] = "Authorization: ".$header;
    //$response = curl_exec ($ch);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = json_decode(curl_exec ($ch));
    $result = (Object)Array();
    $result->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $result->profileImg = $res->response->profile_image;
    $result->gender = $res->response->gender;
    $result->id = $res->response->id;
    $result->name  = $res->response->name;
    curl_close ($ch);

    $st = null;
    $pdo = null;

    return $result;
}

function registerUser($name, $email, $profileImg, $password, $phone, $birthday, $naverId, $gender)
{
    $pdo = pdoSqlConnect();
    $query = "insert into User (name, email, profileImg, password, phone, birthday, naverId, gender)
    values (?, ?, ?, ?, ?, ?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$name, $email, $profileImg, $password, $phone, $birthday, $naverId, $gender]);

    $st = null;
    $pdo = null;
}

function updateFirebaseToken($userId, $fcmToken)
{
    $pdo = pdoSqlConnect();
    $query = "update User
set fcmToken = ?
where userId = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$fcmToken, $userId]);

    $st = null;
    $pdo = null;
}

function sendBirthdayFCM()
{
    $pdo = pdoSqlConnect();
    $query = "select u.userId, u.name
from User as u
where MONTH(now()) = MONTH(u.birthday) and DAY(now()) = DAYOFMONTH(u.birthday);";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $birthday = $st->fetchAll();

    $query = "select fcmToken
from (select distinct bandId
from BandUser
where userId = ? and isDeleted = 'N') as BandList join BandUser inner join User on BandUser.userId = User.userId
where BandList.bandId = BandUser.bandId and BandUser.isDeleted = 'N' and User.userId != ?;";
    $st = $pdo->prepare($query);
    foreach($birthday as $value){
        $notification = $value['name'].'님이 오늘 생일입니다!';
        $st->execute([$value['userId'], $value['userId']]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $fcmToken = $st->fetchAll();
//        if(sendFcm($fcmToken, $notification ,$data, $key, $deviceType) == false){
//            return false;
//        }
    }

    $st = null;
    $pdo = null;

    return true;
}
