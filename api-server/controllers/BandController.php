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
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $result = getUserBand($data->userId);

            if($result == null){
                $res->result = null;
            }
            else {
                    $res->result->bandInfo = $result;
            }
            $res = returnMake($res, TRUE, 100, "유저가 가입한 밴드 조회 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "createBand":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if(!is_string($req->bandName)){
                $res = returnMake($res, FALSE, 201, "밴드 이름이 문자열이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            if(!is_string($req->bandImg)){
                $res = returnMake($res, FALSE, 202, "밴드 이미지가 문자열이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            if(!is_string($req->isOpened)){
                $res = returnMake($res, FALSE, 203, "밴드 공개타입이 문자열이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $bandId = createBand($req->bandName, $req->bandImg, $req->isOpened);
            $sinceLeaderDate = createFirstBandUser($bandId, $data->userId);

            $res->result->bandId = $bandId;
            $res->result->bandName = $req->bandName;
            $res->result->sinceLeaderDate = $sinceLeaderDate;
            $res = returnMake($res, TRUE, 100, "유저가 가입한 밴드 조회 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getBandDetail":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $bandId = $vars['bandId'];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!isValidBandID($bandId)) {
                $res = returnMake($res, FALSE, 201, "존재 하지 않는 밴드 id 입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandUserLeaderID($bandId, $data->userId)) {
                $res = returnMake($res, FALSE, 202, "밴드 리더가 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = getBandDetail($bandId);
            $res = returnMake($res, TRUE, 100, "밴드 리더 상세 정보 조회 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "updateBandProfile":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!is_int($req->bandId)) {
                $res = returnMake($res, FALSE, 204, "형");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isValidBandID($req->bandId)) {
                $res = returnMake($res, FALSE, 201, "존재 하지 않는 밴드 id 입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $original = getOriginalProfile($req->bandId);

            if (!isValidBandUserLeaderID($req->bandId, $data->userId)) {
                $res = returnMake($res, FALSE, 202, "밴드 리더가 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if(!empty($req->bandName)) {
                if (!is_string($req->bandName)) {
                    $res = returnMake($res, FALSE, 203, "밴드 이름이 문자열이 아닙니다.");
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

            }

            if(!empty($req->bandImg)) {
                if (!is_string($req->bandImg)) {
                    $res = returnMake($res, FALSE, 204, "밴드 이미지가 문자열이 아닙니다.");
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }

            if(!empty($req->color)) {
                if (!is_string($req->color)) {
                    $res = returnMake($res, FALSE, 205, "밴드 색상이 문자열이 아닙니다.");
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }

            if ($req->bandName == "") {
                $bandName = $original[0]["bandName"];
            }
            else {
                $bandName = $req->bandName;
            }
            if ($req->bandImg == "") {
                $bandImg = $original[0]["bandImg"];
            }
            else {
                $bandImg = $req->bandImg;
            }
            if ($req->color == "") {
                $color = $original[0]["color"];
            }
            else {
                $color = $req->color;
            }

            $res->result = updateBandProfile($req->bandId, $bandName, $bandImg, $color);
            $res = returnMake($res, TRUE, 100, "밴드 프로필 변경 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "createEnterpriseBand":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!is_int($req->bandId)) {
                $res = returnMake($res, FALSE, 212, "밴드 id가 정수형이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isValidBandID($req->bandId)) {
                $res = returnMake($res, FALSE, 201, "존재 하지 않는 밴드 id 입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandUserLeaderID($req->bandId, $data->userId)) {
                $res = returnMake($res, FALSE, 202, "밴드 리더가 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!is_string($req->companyName)) {
                $res = returnMake($res, FALSE, 205, "상호명이 문자열이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!is_string($req->headName)) {
                $res = returnMake($res, FALSE, 206, "대표자 성명이 문자열이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!is_string($req->address)) {
                $res = returnMake($res, FALSE, 207, "주소가 문자열이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!is_string($req->phone)) {
                $res = returnMake($res, FALSE, 208, "전화번호가 문자열이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!is_string($req->email)) {
                $res = returnMake($res, FALSE, 209, "메일이 문자열이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!is_string($req->companyRegisterNo)) {
                $res = returnMake($res, FALSE, 210, "사업자 등록 번호가 문자열이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!is_string($req->saleRegisterNo)) {
                $res = returnMake($res, FALSE, 211, "통신판매 신고번호가 문자열이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!preg_match(enterpriseReg, $req->companyRegisterNo)) {
                $res = returnMake($res, FALSE, 203, "형식에 맞지 않는 사업자 등록 번호.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

//            if (!preg_match(communicationSaleReg, $req->saleRegisterNo)) {
//                $res = returnMake($res, FALSE, 204, "형식에 맞지 않는 통신판매 신고번호.");
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                return;
//            }

            $res->result = createEnterpriseBand($req->bandId, $req->companyName, $req->headName, $req->address, $req->phone, $req->email, $req->companyRegisterNo, $req->saleRegisterNo);
            $res = returnMake($res, TRUE, 100, "사업자 밴드 생성 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "updateBandIntroduction":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!is_int($req->bandId)) {
                $res = returnMake($res, FALSE, 204, "밴드 id가 정수형이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isValidBandID($req->bandId)) {
                $res = returnMake($res, FALSE, 201, "존재 하지 않는 밴드 id 입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandUserLeaderID($req->bandId, $data->userId)) {
                $res = returnMake($res, FALSE, 202, "밴드 리더가 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!is_string($req->bandIntroduction)) {
                $res = returnMake($res, FALSE, 203, "밴드 소개가 문자열이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $res->result = updateBandIntroduction($req->bandId, $req->bandIntroduction);
            $res = returnMake($res, TRUE, 100, "밴드 소개 변경 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "createBandEnter":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!isValidBandID($req->bandId)) {
                $res = returnMake($res, FALSE, 201, "존재 하지 않는 밴드 id 입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = createBandEnter($req->bandId);
            $res = returnMake($res, TRUE, 100, "밴드 들어가기 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "updateBandMember":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!is_int($req->bandId)) {
                $res = returnMake($res, FALSE, 201, "밴드 id가 정수형이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isValidBandID($req->bandId)) {
                $res = returnMake($res, FALSE, 202, "존재 하지 않는 밴드 id 입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandUserLeaderID($req->bandId, $data->userId)) {
                $res = returnMake($res, FALSE, 203, "밴드 리더가 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!is_int($req->restrictMemberNo)) {
                $res = returnMake($res, FALSE, 204, "밴드 인원수 제한이 정수형이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isValidChangeMemberNo($req->bandId)) {
                $res = returnMake($res, FALSE, 205, "멤버수 설정은 하루에 한번만 변경할 수 있습니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = updateBandMember($req->bandId, $req->restrictMemberNo);
            $res = returnMake($res, TRUE, 100, "밴드 멤버수 변경 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "createBandRestrictAge":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!is_int($req->bandId)) {
                $res = returnMake($res, FALSE, 201, "밴드 id가 정수형이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isValidBandID($req->bandId)) {
                $res = returnMake($res, FALSE, 202, "존재 하지 않는 밴드 id 입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandUserLeaderID($req->bandId, $data->userId)) {
                $res = returnMake($res, FALSE, 203, "밴드 리더가 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (isAlreadyExistBandIdAge($req->bandId)) {
                $res = returnMake($res, FALSE, 204, "이미 나이 제한이 존재합니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!is_int($req->minAge)) {
                $res = returnMake($res, FALSE, 205, "최소나이가 정수형이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!is_int($req->maxAge)) {
                $res = returnMake($res, FALSE, 206, "최대나이가 정수형이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isValidRestrictAge($req->minAge, $req->maxAge)) {
                $res = returnMake($res, FALSE, 207, "최소 나이가 최대 나이보다 큽니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = createBandRestrictAge($req->bandId, $req->minAge, $req->maxAge);
            $res = returnMake($res, TRUE, 100, "밴드 나이 제한 설정 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "createBandRestrictGender":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!is_int($req->bandId)) {
                $res = returnMake($res, FALSE, 201, "밴드 id가 정수형이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isValidBandID($req->bandId)) {
                $res = returnMake($res, FALSE, 202, "존재 하지 않는 밴드 id 입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (isAlreadyExistBandIdGender($req->bandId)) {
                $res = returnMake($res, FALSE, 203, "이미 성별 제한이 존재합니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandUserLeaderID($req->bandId, $data->userId)) {
                $res = returnMake($res, FALSE, 204, "밴드 리더가 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!is_string($req->gender)) {
                $res = returnMake($res, FALSE, 205, "성별이 문자열이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isValidRestrictGender($req->gender)) {
                $res = returnMake($res, FALSE, 206, "성별이 F 또는 M이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = createBandRestrictGender($req->bandId, $req->gender);
            $res = returnMake($res, TRUE, 100, "밴드 성별 제한 설정 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getBandTag":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $bandId = intval($vars['bandId']);

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!is_int($bandId)) {
                $res = returnMake($res, FALSE, 201, "밴드 id가 정수형이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isValidBandID($bandId)) {
                $res = returnMake($res, FALSE, 202, "존재 하지 않는 밴드 id 입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandUser($bandId, $data->userId)) {
                $res = returnMake($res, FALSE, 203, "밴드 유저가 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $result = getBandTag($bandId);

            if($result == null){
                $res->result = null;
            }
            else {
                $res->result->bandTag = $result;
            }

            $res = returnMake($res, TRUE, 100, "밴드 태그 조회 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "createBandTag":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!is_int($req->bandId)) {
                $res = returnMake($res, FALSE, 201, "밴드 id가 정수형이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isValidBandID($req->bandId)) {
                $res = returnMake($res, FALSE, 202, "존재 하지 않는 밴드 id 입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandUserLeaderID($req->bandId, $data->userId)) {
                $res = returnMake($res, FALSE, 203, "밴드 리더가 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!is_string($req->tagContent)) {
                $res = returnMake($res, FALSE, 204, "태그 내용이 문자열이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            $tagContent = str_replace(" ", "", $req->tagContent);
            if (isAlreadyExistBandTag($req->bandId, $tagContent)) {
                $res = returnMake($res, FALSE, 205, "이미 똑같은 태그가 존재합니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = createBandTag($req->bandId, $tagContent);
            $res = returnMake($res, TRUE, 100, "밴드 태그 생성 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getBandInfo":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $bandId = $vars['bandId'];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!isValidBandID($bandId)) {
                $res = returnMake($res, FALSE, 201, "존재 하지 않는 밴드 id 입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandUser($bandId, $data->userId)) {
                $res = returnMake($res, FALSE, 202, "밴드 유저가 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = getBandInfo($bandId);
            $res = returnMake($res, TRUE, 100, "밴드 일반 유저 간단 정보 조회 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getBandUser":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $bandId = intval($vars['bandId']);

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandID($bandId)) {
                $res = returnMake($res, FALSE, 201, "존재 하지 않는 밴드 id 입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!isValidBandUser($bandId, $data->userId)) {
                $res = returnMake($res, FALSE, 202, "밴드 유저가 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $result = getBandUser($bandId);

            if($result == null){
                $res->result = null;
            }
            else {
                $res->result->userInfo = $result;
            }
            $res = returnMake($res, TRUE, 100, "밴드에 가입한 유저 조회 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "updateBandLeader":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!is_int($req->bandId)) {
                $res = returnMake($res, FALSE, 201, "밴드 id가 정수형이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isValidBandID($req->bandId)) {
                $res = returnMake($res, FALSE, 202, "존재 하지 않는 밴드 id 입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isValidBandUserLeaderID($req->bandId, $data->userId)) {
                $res = returnMake($res, FALSE, 203, "밴드 리더가 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!is_int($req->userId)) {
                $res = returnMake($res, FALSE, 204, "유저 id가 정수형이 아닙니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isValidBandUser($req->bandId, $req->userId)) {
                $res = returnMake($res, FALSE, 205, "리더로 변경할 유저가 밴드에 가입되어있지 않습니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = updateBandLeader($req->bandId, $data->userId, $req->userId);
            $res = returnMake($res, TRUE, 100, "밴드 리더 변경 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getBandSearch":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $content = $_GET['content'];
            $page = $_GET['page'];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);

            if(empty($content)){
                $res = returnMake($res, FALSE, 201, "검색 내용이 비었습니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if($page == null){
                $res = returnMake($res, FALSE, 202, "페이지 번호가 비었습니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $page = intval($page);
            $page = $page*10;
            $result = getBandSearch($content, $page);

            if($result == null){
                $res->result = null;
            }
            else {
                $res->result->bandSearchInfo = $result;
            }
            $res = returnMake($res, TRUE, 100, "밴드 검색 결과 조회 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getBestBand":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $result = getBestBand();

            if($result == null){
                $res->result = null;
            }
            else {
                $res->result->bestBandInfo = $result;
            }
            $res = returnMake($res, TRUE, 100, "인기 밴드 조회 성공.");
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
