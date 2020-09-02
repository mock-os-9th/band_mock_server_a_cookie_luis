<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "getNaverUser":
            $token = $_GET['naver'];
            $result = getNaverUser($token);
            $res->result->name = $result->response->name;
            $res->result->profile_image = $result->response->profile_image;
            $res->result->gender = $result->response->gender;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "네이버 프로필 조회 성공";
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "createUser":
            if(!empty($_GET['naver'])){
                if(isAlreadyExist($req->phone)){
                    $res->result = updateNaverId($req->phone, $_GET['naver']);
                    if($res->result != 200){
                        http_response_code(200);
                        $res->isSuccess = FALSE;
                        $res->code = 200;
                        $res->message = "네이버 로그인 실패";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                }
                $res->result = naverRegister($_GET['naver'], $req->password, $req->phone, $req->birthday);
                if($res->result != 200){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 200;
                    $res->message = "네이버 로그인 실패";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }
            else if(!empty($_GET['facebook'])){
                $res->result = facebookRegister($_GET['facebook']);
            }
            else{
                if(empty($req->email)){
                    $req->email = null;
                }
                else if(empty($req->phone)){
                    $req->phone = null;
                }
                $res->result = generalRegister($req->name, $req->email, $req->profileImg, $req->password, $req->gender, $req->phone, $req->birthday);
            }
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "updateUser":
            if(!empty($_GET['naver'])){
                $res->result = updateNaverId($vars['userid'], $_GET['naver']);
                if($res->result != 200){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 200;
                    $res->message = "네이버 로그인 실패";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }
            else if(!empty($_GET['facebook'])){
                $res->result = facebookRegister($_GET['facebook']);
            }
            else{
                if(empty($req->email)){
                    $req->email = null;
                }
                else if(empty($req->phone)){
                    $req->phone = null;
                }
                $res->result = generalRegister($req->name, $req->email, $req->profileImg, $req->password, $req->gender, $req->phone, $req->birthday);
            }
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 연동 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
