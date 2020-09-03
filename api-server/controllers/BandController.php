<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 0
         * API Name : JWT 유효성 검사 테스트 API
         * 마지막 수정 날짜 : 19.04.25
         */
        case "getUserBand":
            if (!isValidUsersId($vars['userid'])) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "존재하지 않은 유저 id입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserBand($vars['userid']);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저가 가입한 밴드 조회 성공";
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getBandInfo":
            $res->result = getBandInfo($vars['bandid']);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "밴드 정보 조회 성공";
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
