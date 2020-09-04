<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "getExistUserInfo":
            // 네이버로 가입할 경우
            if(!empty($_GET['naver'])){
                $result = getExistUserByNaver($_GET['naver']);
                if($result->code == 200) {
                    http_response_code(200);
                    $res->result->name = $result->response->name;
                    $res->result->profile_image = $result->response->profile_image;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "프로필 조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
                else{
                    http_response_code(200);
                    $res->isSuccess = TRUE;
                    $res->code = 206;
                    $res->message = "네이버 로그인 실패";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }
            // 전화번호로 가입할 경우
            else if(!empty($_GET['phone'])){
                // 형식에 맞는 입력 검사
                if(!is_string($_GET['phone'])){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 201;
                    $res->message = "형식에 맞지 않은 입력";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 전화번호 형식 검사
                if(!preg_match(phoneReg, $_GET['phone'])){
                    $res->isSuccess = FALSE;
                    $res->code = 202;
                    $res->message = "형식에 맞지 않는 전화번호";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 가입되지 않은 전화번호 검사
                if(!isExistPhone($_GET['phone'])) {
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 204;
                    $res->message = "가입되지 않은 전화번호";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                http_response_code(200);
                $res->result = getExistUserByPhone($_GET['phone']);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "프로필 조회 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);

            }
            // 이메일로 가입할 경우
            else if(!empty($_GET['email'])){
                // 형식에 맞는 입력 검사
                if(!is_string($_GET['email'])){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 201;
                    $res->message = "형식에 맞지 않은 입력";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 이메일 형식 검사
                if(!preg_match(emailReg, $req->email)){
                    $res->isSuccess = FALSE;
                    $res->code = 203;
                    $res->message = "형식에 맞지 않는 이메일";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 가입되지 않은 이메일 검사
                if(!isExistEmail($_GET['email'])) {
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 205;
                    $res->message = "가입되지 않은 이메일";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                http_response_code(200);
                $res->result = getExistUserByEmail($_GET['email']);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "프로필 조회 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }
            // Query String이 존재하지 않을 경우
            else{
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "네이버 토큰이나 전화번호나 이메일 셋 중 하나는 반드시 입력 필요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;

        case "createUser":
            // 전화번호와 이메일 중 하나만 입력 검사
            if((!empty($req->phone) && !empty($req->email)) || (empty($req->phone) && empty($req->email))){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "전화번호나 이메일 둘 중 하나만 입력 필요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 비밀번호, 전화번호, 생일 입력 검사
            if(empty($req->password) ||  empty($req->birthday)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "비밀번호 혹은 생일 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 형식에 맞는 입력 검사
            if(!is_string($req->password) || !is_string($req->birthday)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 204;
                $res->message = "형식에 맞지 않은 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 비밀번호 형식 검사
            if(!preg_match(passwordReg, $req->password)){
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "특수문자 제외 영어 대소문자, 숫자 포함 8~16자 비밀번호 형식 필요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 생일 형식 검사
            if(!preg_match(birthdayReg, $req->birthday)){
                $res->isSuccess = FALSE;
                $res->code = 204;
                $res->message = "형식에 맞지 않는 생일 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 현재보다 미래로 지정한 생일 검사
            if(strtotime($req->birthday) >= strtotime(date("Y-m-d H:i:s"))){
                $res->isSuccess = FALSE;
                $res->code = 210;
                $res->message = "현재일보다 미래를 생일로 지정할 수 없음";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            // 네이버로 가입할 경우
            if(!empty($_GET['naver'])){
                // 전화번호 입력 검사
                if(empty($req->phone)){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 202;
                    $res->message = "전화번호 입력 누락";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 형식에 맞는 입력 검사
                if(!is_string($req->phone)){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 204;
                    $res->message = "형식에 맞지 않은 입력";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 전화번호 형식 검사
                if(!preg_match(phoneReg, $req->phone)){
                    $res->isSuccess = FALSE;
                    $res->code = 206;
                    $res->message = "형식에 맞지 않는 전화번호";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 이미 가입된 전화번호 검사
                if(isExistPhone($req->phone)) {
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 208;
                    $res->message = "이미 존재하는 전화번호";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                $res->code = registerByNaver($_GET['naver'], $req->password, $req->phone, $req->birthday);
                if($res->code != 200){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->message = "네이버 로그인 실패";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                else{
                    http_response_code(200);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "회원가입 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }
            // 전화번호 혹은 이메일로 가입할 경우
            else{
                // 이름 입력 검사
                if(empty($req->name)){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 203;
                    $res->message = "이름 입력 누락";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 형식에 맞는 입력 검사
                if(!is_string($req->name)){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 204;
                    $res->message = "형식에 맞지 않은 입력";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 프로필 이미지를 지정하지 않았을 경우
                if(empty($req->name)){
                    $req->profileImg = null;
                }
                // 전화번호로 가입할 경우
                if(!empty($req->phone)){
                    // 형식에 맞는 입력 검사
                    if(!is_string($req->phone)){
                        http_response_code(200);
                        $res->isSuccess = FALSE;
                        $res->code = 204;
                        $res->message = "형식에 맞지 않은 입력";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    // 전화번호 형식 검사
                    if(!preg_match(phoneReg, $req->phone)){
                        $res->isSuccess = FALSE;
                        $res->code = 206;
                        $res->message = "형식에 맞지 않는 전화번호";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    // 이미 가입된 전화번호 검사
                    if(isExistPhone($req->phone)) {
                        http_response_code(200);
                        $res->isSuccess = FALSE;
                        $res->code = 208;
                        $res->message = "이미 존재하는 전화번호";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    $req->email = null;
                }
                // 이메일로 가입할 경우
                else{
                    // 형식에 맞는 입력 검사
                    if(!is_string($req->email)){
                        http_response_code(200);
                        $res->isSuccess = FALSE;
                        $res->code = 204;
                        $res->message = "형식에 맞지 않은 입력";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    // 이메일 형식 검사
                    if(!preg_match(emailReg, $req->email)){
                        $res->isSuccess = FALSE;
                        $res->code = 207;
                        $res->message = "형식에 맞지 않는 이메일";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    // 이미 가입된 이메일 검사
                    if(isExistEmail($req->email)) {
                        http_response_code(200);
                        $res->isSuccess = FALSE;
                        $res->code = 209;
                        $res->message = "이미 존재하는 이메일";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    $req->phone = null;
                }
                $res->code = registerByGeneral($req->name, $req->email, $req->profileImg, $req->password, $req->phone, $req->birthday);
            }
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "회원가입 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "validateJwt":
            // jwt 유효성 검사

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            http_response_code(200);
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY); //jwt가 제대로 파싱되는지 확인하기 위해 만든 부분 원래는 유효성 검사만분 진행
            $res->result = $data;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "로그인 성공.";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 1
         * API Name : JWT 생성 테스트 API (로그인)
         * 마지막 수정 날짜 : 19.04.25
         */
        case "createJwt":
            // jwt 유효성 검사
            http_response_code(200);
            if(!preg_match(passwordReg, $req->password)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "패스워드는 특수문자 제외 영어 대소문자, 숫자 포함 8~16자 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            if(!empty($req->email)) { //이메일로 로그인하는 경우
                if (!isValidEmailUser($req->email, $req->password)) {
                    $res->isSuccess = FALSE;
                    $res->code = 201;
                    $res->message = "유효하지 않은 이메일, 패스워드 입니다.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                $userId = getIdFromEmailPw($req->email, $req->password);
            }
            else if(!empty($req->phone)){ //핸드폰 번호로 로그인 하는 경우
                if (!isValidPhoneUser($req->phone, $req->password)) {
                    $res->isSuccess = FALSE;
                    $res->code = 202;
                    $res->message = "유효하지 않은 핸드폰번호, 패스워드 입니다.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                $userId = getIdFromPhonePw($req->phone, $req->password);
            }

            $jwt = getJWToken($userId, $req->email, $req->phone, $req->password, JWT_SECRET_KEY);
            $res->result->jwt = $jwt;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 로그인 성공.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getAutoLogin":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            http_response_code(200);
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY); //jwt가 제대로 파싱되는지 확인하기 위해 만든 부분 원래는 유효성 검사만분 진행
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "자동 로그인 성공.";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
