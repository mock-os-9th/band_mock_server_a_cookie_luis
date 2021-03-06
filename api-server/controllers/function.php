<?php

use Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

const passwordReg = "/^[0-9A-Za-z]{8,16}$/",
emailReg = "/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/",
phoneReg = "/^01([016789]?)-?([0-9]{3,4})-?([0-9]{4})$/",
birthdayReg = "/^(19|20)\d{2}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[0-1])$/",
enterpriseReg = "/^[0-9]{3}-[0-9]{2}-[0-9]{5}$/",
communicationSaleReg = "/^[0-9]{4}-[가-힣]{12}-[0-9]{5}[가-]{3}$/";

function getSQLErrorException($errorLogs, $e, $req)
{
    $res = (Object)Array();
    http_response_code(500);
    $res->code = 500;
    $res->message = "SQL Exception -> " . $e->getTraceAsString();
    echo json_encode($res);
    addErrorLogs($errorLogs, $res, $req);
}

function isValidHeader($jwt, $key)
{
    try {
        $data = getDataByJWToken($jwt, $key);
        if(!empty($data->naverId)){ //네이버 아이디로 로그인 할 경우
            return isValidNaverUser($data->naverId);
        }
        else {
            if (!empty($data->email)) { //email이 유효한지 검사
                return isValidEmailUser($data->email, $data->password);
            } else if (!empty($data->phone)) { //phone번호가 유효한지 검사
                return isValidPhoneUser($data->phone, $data->password);
            }
        }
        //로그인 함수 직접 구현 요함
    } catch (\Exception $e) {
        return false;
    }
}

function sendFcm($fcmToken, $notification ,$data, $key, $deviceType)
{
    $url = 'https://fcm.googleapis.com/fcm/send';
    $headers = array(
        'Authorization: key=' . $key,
        'Content-Type: application/json'
    );

    if (is_array($fcmToken)) { //array인지 체크해서 다중으로 보낼건지, 아닌지 결정하는 부분
        $fields['registration_ids'] = $fcmToken;
    } else {
        $fields['to'] = $fcmToken; //한명에게만 보낼 경우
    }
    $fields['notification'] = $notification; //상대방에게 갈 알람을 저장해줌
    $fields['data'] = $data;
    $fields['content_available'] = true;
    $fields['delay_while_idle'] = true;
    $fields['priority'] = "high";
    $fields = json_encode($fields, JSON_NUMERIC_CHECK);
//    echo $fields;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    $result = curl_exec($ch);
    if ($result === FALSE) { //실패했을 경우
        //die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result; //성공했을 경우 true리턴
}

//function sendFcm($fcmToken, $data, $key, $deviceType)
//{
//    $url = 'https://fcm.googleapis.com/fcm/send';
//
//    $headers = array(
//        'Authorization: key=' . $key,
//        'Content-Type: application/json'
//    );
//
//    $fields['data'] = $data;
//
//    if ($deviceType == 'IOS') {
//        $notification['title'] = $data['title'];
//        $notification['body'] = $data['body'];
//        $notification['sound'] = 'default';
//        $fields['notification'] = $notification;
//    }
//
//    $fields['to'] = $fcmToken;
//    $fields['content_available'] = true;
//    $fields['priority'] = "high";
//
//    $fields = json_encode($fields, JSON_NUMERIC_CHECK);
//
////    echo $fields;
//
//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_URL, $url);
//    curl_setopt($ch, CURLOPT_POST, true);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
//
//    $result = curl_exec($ch);
//    if ($result === FALSE) {
//        //die('FCM Send Error: ' . curl_error($ch));
//    }
//    curl_close($ch);
//    return $result;
//}

function getTodayByTimeStamp()
{
    return date("Y-m-d H:i:s");
}

function getJWToken($userId, $naverId, $email, $phone, $password, $secretKey)
{
    $data = array(
        'date' => (string)getTodayByTimeStamp(),
        'userId' => (int)$userId,
        'naverId' => (int)$naverId,
        'email' => (string)$email,
        'phone' => (string)$phone,
        'password' => (string)$password
    );

//    echo json_encode($data);

    return $jwt = JWT::encode($data, $secretKey);

//    echo "encoded jwt: " . $jwt . "n";
//    $decoded = JWT::decode($jwt, $secretKey, array('HS256'))
//    print_r($decoded);
}

function getDataByJWToken($jwt, $secretKey)
{
    try{
        $decoded = JWT::decode($jwt, $secretKey, array('HS256'));
    }catch(\Exception $e){
        return "";
    }

//    print_r($decoded);
    return $decoded;

}

function returnMake($res, $isSuccess, $code, $message)
{
    $res->isSuccess = $isSuccess;
    $res->code = $code;
    $res->message = $message;

    return $res;
}


function checkAndroidBillingReceipt($credentialsPath, $token, $pid)
{

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);
    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->addScope("https://www.googleapis.com/auth/androidpublisher");
    $client->setSubject("USER_ID.iam.gserviceaccount.com");


    $service = new Google_Service_AndroidPublisher($client);
    $optParams = array('token' => $token);

    return $service->purchases_products->get("PACKAGE_NAME", $pid, $token);
}


function addAccessLogs($accessLogs, $body)
{
    if (isset($_SERVER['HTTP_X_ACCESS_TOKEN']))
        $logData["JWT"] = getDataByJWToken($_SERVER['HTTP_X_ACCESS_TOKEN'], JWT_SECRET_KEY);
    $logData["GET"] = $_GET;
    $logData["BODY"] = $body;
    $logData["REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
    $logData["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
//    $logData["SERVER_SOFTWARE"] = $_SERVER["SERVER_SOFTWARE"];
    $logData["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
    $logData["HTTP_USER_AGENT"] = $_SERVER["HTTP_USER_AGENT"];
    $accessLogs->addInfo(json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

}

function addErrorLogs($errorLogs, $res, $body)
{
    if (isset($_SERVER['HTTP_X_ACCESS_TOKEN']))
        $req["JWT"] = getDataByJWToken($_SERVER['HTTP_X_ACCESS_TOKEN'], JWT_SECRET_KEY);
    $req["GET"] = $_GET;
    $req["BODY"] = $body;
    $req["REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
    $req["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
//    $req["SERVER_SOFTWARE"] = $_SERVER["SERVER_SOFTWARE"];
    $req["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
    $req["HTTP_USER_AGENT"] = $_SERVER["HTTP_USER_AGENT"];

    $logData["REQUEST"] = $req;
    $logData["RESPONSE"] = $res;

    $errorLogs->addError(json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

//        sendDebugEmail("Error : " . $req["REQUEST_METHOD"] . " " . $req["REQUEST_URI"] , "<pre>" . json_encode($logData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "</pre>");
}


function getLogs($path)
{
    $fp = fopen($path, "r", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$fp) echo "error";

    while (!feof($fp)) {
        $str = fgets($fp, 10000);
        $arr[] = $str;
    }
    for ($i = sizeof($arr) - 1; $i >= 0; $i--) {
        echo $arr[$i] . "<br>";
    }
//        fpassthru($fp);
    fclose($fp);
}
