<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;
        /*
         * API No. 0
         * API Name : 테스트 API
         * 마지막 수정 날짜 : 19.04.29
         */

        /*
                 * API No. 1
                 * API Name : 메인 페이지 광고 이미지 랜덤 조회 API
                 * 마지막 수정 날짜 : 20.09.01
        */
        case "getAd":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = getAd();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "메인 페이지 광고 이미지 랜덤 조회 성공";
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
