<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "createPost":
            // 빈 게시물인지 확인
            if (empty($req->text) && empty($req->media) && empty($req->tag) && empty($req->file)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "빈 게시물 생성 불가";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 201, "유효하지 않은 토큰입니다.");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 유효한 밴드 id 검사
            if (!isValidBandId($vars['bandId'])) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 밴드 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 형식에 맞는 입력 검사
            if ((!empty($req->text) && !is_string($req->text)) || (!empty($req->media) && !is_string($req->media))
                || (!empty($req->tag) && !is_string($req->tag)) || (!empty($req->file) && !is_string($req->file))) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "형식에 맞지 않는 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            createPost($data->userId, $vars['bandId'], $req->text, $req->media, $req->tag, $req->file);
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "게시글 생성 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
