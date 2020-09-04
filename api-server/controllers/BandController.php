<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_KEY";

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
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $res->result = getUserBand($data->userId);
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

        case "createBand":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $bandId = createBand($req->bandName, $req->bandImg, $req->isOpened);
            $sinceLeaderDate = createFirstBandUser($bandId, $data->userId);
            $res->result->bandId = $bandId;
            $res->result->bandName = $req->bandName;
            $res->result->sinceLeaderDate = $sinceLeaderDate;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저가 가입한 밴드 조회 성공";
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getBandDetail":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $bandId = $vars['bandid'];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!isValidBandID($bandId)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "존재 하지 않는 밴드 id 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandUserLeaderID($bandId, $data->userId)) {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "밴드 리더가 아닙니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = getBandDetail($bandId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "밴드 상세 조회 성공";
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "updateBandProfile":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $bandId = $vars['bandid'];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!isValidBandID($bandId)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "존재 하지 않는 밴드 id 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $original = getOriginalProfile($bandId);

            if (!isValidBandUserLeaderID($bandId, $data->userId)) {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "밴드 리더가 아닙니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if ($req->bandName == "") {
                $bandName = $original[0]["bandName"];
            } else {
                $bandName = $req->bandName;
            }
            if ($req->bandImg == "") {
                $bandImg = $original[0]["bandImg"];
            } else {
                $bandImg = $req->bandImg;
            }
            if ($req->color == "") {
                $color = $original[0]["color"];
            } else {
                $color = $req->color;
            }
            $res->result = updateBandProfile($bandId, $bandName, $bandImg, $color);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "밴드 프로필 변경 성공";
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "createEnterpriseBand":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $bandId = $vars['bandid'];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!isValidBandID($bandId)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "존재 하지 않는 밴드 id 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandUserLeaderID($bandId, $data->userId)) {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "밴드 리더가 아닙니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!preg_match(enterpriseReg, $req->companyRegisterNo)) {
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "형식에 맞지 않는 사업자 등록 번호";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            /*if (!preg_match(communicationSaleReg, $req->saleRegisterNo)) {
                $res->isSuccess = FALSE;
                $res->code = 204;
                $res->message = "형식에 맞지 않는 통신판매 신고번호";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }*/

            $res->result = createEnterpriseBand($bandId, $req->companyName, $req->headName, $req->address, $req->phone, $req->email, $req->companyRegisterNo, $req->saleRegisterNo);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "사업자 밴드 생성 성공";
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "updateBandIntroduction":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $bandId = $vars['bandid'];
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $original = getOriginalProfile($bandId);

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandID($bandId)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "존재 하지 않는 밴드 id 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandUserLeaderID($bandId, $data->userId)) {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "밴드 리더가 아닙니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = updateBandIntroduction($bandId, $req->bandIntroduction);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "밴드 소개 변경 성공";
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
