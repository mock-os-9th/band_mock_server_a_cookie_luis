<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_BANDA_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "creatPost":
            if(empty($req->text) && empty($req->media) && empty($req->tag) && empty($req->file)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "형식에 맞지 않은 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
