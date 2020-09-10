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
            // 밴드 입력 누락 확인
            if(empty($req->bandId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "밴드 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 빈 게시물인지 확인
            if (empty($req->text) && empty($req->media) && empty($req->tag) && empty($req->file)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "빈 게시물 생성 불가";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 202, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 유효한 밴드 id 검사
            if (!isValidBandId($req->bandId)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "존재하지 않는 밴드 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 형식에 맞는 입력 검사
            if (!is_int($req->bandId) || (!empty($req->text) && !is_string($req->text)) || (!empty($req->media) && !is_string($req->media))
                || (!empty($req->tag) && !is_string($req->tag)) || (!empty($req->file) && !is_string($req->file))) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 209;
                $res->message = "형식에 맞지 않는 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            createPost($data->userId, $req->bandId, $req->text, $req->media, $req->tag, $req->file);
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "게시글 생성 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        case "createComment":
            // 게시글 입력 누락 확인
            if(empty($req->postId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "게시글 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 빈 댓글인지 확인
            if (empty($req->text) && empty($req->media) && empty($req->file)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "빈 댓글 생성 불가";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 203, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 유효한 게시글 id 검사
            if (!isValidPostId($req->postId)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "존재하지 않는 게시글 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 미디어와 파일 둘 중 하나만 입력 검사
            if(!empty($req->media) && !empty($req->file)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 210;
                $res->message = "미디어와 파일 둘 중 하나만 입력 가능";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 부모 댓글을 입력했을 경우
            if(!empty($req->parentCommentId)){
                // 형식에 맞는 입력 검사
                if (!is_int($req->parentCommentId)) {
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 209;
                    $res->message = "형식에 맞지 않는 입력";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 존재하는 댓글 id 검사
                if (!isValidCommentId($req->parentCommentId)) {
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 206;
                    $res->message = "존재하지 않는 댓글 ID";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 게시글 ID와 일치하는 부모 댓글의 게시물 ID 검사
                if (!isValidParentCommentId($req->parentCommentId, $req->postId)) {
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 208;
                    $res->message = "게시글 ID와 일치하지 않는 부모 댓글의 게시물 ID";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }
            // 이모티콘을 입력했을 경우
            if(!empty($req->emoticonId)){
                // 형식에 맞는 입력 검사
                if (!is_int($req->emoticonId)) {
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 209;
                    $res->message = "형식에 맞지 않는 입력";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 존재하는 이모티콘 id 검사
                if (!isValidEmoticonId($req->emoticonId)) {
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 207;
                    $res->message = "존재하지 않는 이모티콘 ID";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }
            // 형식에 맞는 입력 검사
            if (!is_int($req->postId) || (!empty($req->text) && !is_string($req->text)) || (!empty($req->media) && !is_string($req->media))
                || (!empty($req->file) && !is_string($req->file))) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 209;
                $res->message = "형식에 맞지 않는 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            createComment($data->userId, $req->postId, $req->parentCommentId, $req->text, $req->media, $req->file, $req->emoticonId);
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "댓글 생성 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        case "createExpression" :
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 게시글 id와 댓글 id 입력 누락
            if(empty($req->postId) && empty($req->commentId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "게시글 id와 댓글 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 게시글 id와 댓글 id 둘 다 입력
            if(!empty($req->postId) && !empty($req->commentId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "게시글 id와 댓글 id 둘 다 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 표정 id 입력 검사
            if(empty($req->expressionId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "표정 id 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 형식에 맞는 입력 검사
            if (!is_int($req->expressionId) || (!empty($req->postId) && !is_int($req->postId)) || (!empty($req->commentId) && !is_int($req->commentId))){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 204;
                $res->message = "형식에 맞지 않는 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 게시글 ID 검사
            if(!empty($req->postId) && !isValidPostId($req->postId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "존재하지 않는 게시글 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 댓글 ID 검사
            if(!empty($req->commentId) && !isValidCommentId($req->commentId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 206;
                $res->message = "존재하지 않는 댓글 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 표정 ID 검사
            if(!empty($req->expressionId) && !isValidExpressionId($req->expressionId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 207;
                $res->message = "존재하지 않는 표정 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            $res = createExpression($data->userId, $req->postId, $req->commentId, $req->expressionId);
            http_response_code(200);
            $res->isSuccess = TRUE;
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        case "getBandPost":
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 밴드 입력 누락 확인
            if(empty($vars['bandId'])){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "밴드 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 밴드 id 검사
            if (!isValidBandId($vars['bandId'])) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "존재하지 않는 밴드 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // paging을 쿼리 스트링으로 넣어줬을 경우
            if(!empty($_GET['paging'])){
                if(intval($_GET['paging']) < 0){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 202;
                    $res->message = "paging 값은 음수일 수 없음";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                $page = intval($_GET['paging']);
            }
            else{
                $page = 0;
            }
            $res->result->postInfo = getBandPost($vars['bandId'], $page);
            if(empty($res->result->postInfo)){
                http_response_code(200);
                unset($res->result);
                $res->isSuccess = TRUE;
                $res->code = 101;
                $res->message = "게시물이 없음";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                http_response_code(200);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "밴드 게시물 조회 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

        case "getPostComment":
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 게시물 입력 누락 확인
            if(empty($vars['postId'])){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "게시글 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 게시글 id 검사
            if (!isValidPostId($vars['postId'])) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "존재하지 않는 게시글 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // paging을 쿼리 스트링으로 넣어줬을 경우
            if(!empty($_GET['paging'])){
                if(intval($_GET['paging']) < 0){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 202;
                    $res->message = "paging 값은 음수일 수 없음";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                $page = intval($_GET['paging']);
            }
            else{
                $page = 0;
            }
            $res->result->commentInfo = getPostComment(intval($vars['postId']), $page);
            if(empty($res->result->commentInfo)){
                http_response_code(200);
                unset($res->result);
                $res->isSuccess = TRUE;
                $res->code = 101;
                $res->message = "댓글이 없음";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                http_response_code(200);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "게시글 댓글 조회 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

        case "modifyPost":
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 밴드 입력 누락 확인
            if(empty($req->postId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "게시글 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 게시글 id 검사
            if (!isValidPostId($req->postId)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 게시글 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 빈 게시물인지 확인
            if (empty($req->text) && empty($req->media) && empty($req->tag) && empty($req->file)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "빈 게시물 생성 불가";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 형식에 맞는 입력 검사
            if (!is_int($req->postId) || (!empty($req->text) && !is_string($req->text)) || (!empty($req->media) && !is_string($req->media))
                || (!empty($req->tag) && !is_string($req->tag)) || (!empty($req->file) && !is_string($req->file))) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 209;
                $res->message = "형식에 맞지 않는 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            modifyPost($req->postId, $req->text, $req->media, $req->tag, $req->file);
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "게시글 수정 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        case "modifyComment":
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 댓글 입력 누락 확인
            if(empty($req->commentId)){
                http_response_code(201);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "댓글 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 댓글 id 검사
            if (!isValidCommentId($req->commentId)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 댓글 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 빈 댓글인지 확인
            if (empty($req->text) && empty($req->media) && empty($req->file)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "빈 댓글 생성 불가";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 미디어와 파일 둘 중 하나만 입력 검사
            if(!empty($req->media) && !empty($req->file)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 204;
                $res->message = "미디어와 파일 둘 중 하나만 입력 가능";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 이모티콘을 입력했을 경우
            if(!empty($req->emoticonId)){
                // 형식에 맞는 입력 검사
                if (!is_int($req->emoticonId)) {
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 206;
                    $res->message = "형식에 맞지 않는 입력";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 존재하는 이모티콘 id 검사
                if (!isValidEmoticonId($req->emoticonId)) {
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 205;
                    $res->message = "존재하지 않는 이모티콘 ID";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }
            // 형식에 맞는 입력 검사
            if (!is_int($req->commentId) || (!empty($req->text) && !is_string($req->text)) || (!empty($req->media) && !is_string($req->media))
                || (!empty($req->file) && !is_string($req->file))) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 206;
                $res->message = "형식에 맞지 않는 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            modifyComment($req->commentId, $req->text, $req->media, $req->file, $req->emoticonId);
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "댓글 수정 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        case "deleteComment":
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 댓글 입력 누락 확인
            if(empty(intval($vars['commentId']))){
                http_response_code(201);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "댓글 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 댓글 id 검사
            if (!isValidCommentId(intval($vars['commentId']))) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 댓글 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            deleteComment(intval($vars['commentId']));
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "댓글 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        case "deletePost":
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 게시글 id 입력 누락 확인
            if(empty(intval($vars['postId']))){
                http_response_code(201);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "게시글 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 게시글 id 검사
            if (!isValidPostId(intval($vars['postId']))) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 게시글 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            deletePost(intval($vars['postId']));
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "게시글 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        case "deleteExpression":
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 게시글 id 혹은 댓글 id 입력 누락 확인
            if(empty($req->postId) && empty($req->commentId)){
                http_response_code(201);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "게시글 id, 댓글 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 게시글 id와 댓글 id 이중 입력 확인
            if(!empty($req->postId) && !empty($req->commentId)){
                http_response_code(201);
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "게시글 id, 댓글 id 둘 중 하나만 입력해야함";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 게시글 id를 입력했을 경우
            if(!empty($req->postId)){
                // 형식에 맞는 입력 검사
                if(!is_int($req->postId)){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 206;
                    $res->message = "형식에 맞지 않은 입력";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 유효한 게시글 id 검사
                if (!isValidPostId($req->postId)) {
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 203;
                    $res->message = "존재하지 않는 게시글 ID";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 존재하는 표정인지 검사
                if(!isExistPostExpression($req->postId, $data->userId)){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 205;
                    $res->message = "존재하지 않는 유저의 표정";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                deletePostExpression($req->postId, $data->userId);
            }
            // 댓글 id를 입력했을 경우
            else{
                // 형식에 맞는 입력 검사
                if(!is_int($req->commentId)){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 206;
                    $res->message = "형식에 맞지 않은 입력";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 유효한 댓글 id 검사
                if (!empty($req->commentId) && !isValidCommentId($req->commentId)) {
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 204;
                    $res->message = "존재하지 않는 댓글 ID";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                // 존재하는 표정인지 검사
                if(!isExistCommentExpression($req->commentId, $data->userId)){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 205;
                    $res->message = "존재하지 않는 유저의 표정";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                deleteCommentExpression($req->commentId, $data->userId);
            }
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "표정 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        case "createBookmark":
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 게시글 id 입력 누락 확인
            if(empty($req->postId)){
                http_response_code(201);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "게시글 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 형식에 맞는 입력 검사
            if(!is_int($req->postId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 204;
                $res->message = "형식에 맞지 않은 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 게시글 id 검사
            if (!isValidPostId($req->postId)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 게시글 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            if(isExistBookmark($req->postId, $data->userId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "이미 추가된 북마크";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            createBookmark($req->postId, $data->userId);
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "북마크 생성 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        case "deleteBookmark":
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 게시글 id 입력 누락 확인
            if(empty($req->postId)){
                http_response_code(201);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "게시글 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 형식에 맞는 입력 검사
            if(!is_int($req->postId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "형식에 맞지 않은 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            if(!isExistBookmark($req->postId, $data->userId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 북마크";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            deleteBookmark($req->postId, $data->userId);
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "북마크 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        case "createBlockedBandUser":
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 밴드 id 입력 누락 확인
            if(empty($req->bandId)){
                http_response_code(201);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "밴드 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 차단 유저 id 입력 누락 확인
            if(empty($req->blockedUserId)){
                http_response_code(201);
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "차단 유저 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 형식에 맞는 입력 검사
            if(!is_int($req->bandId) || !is_int($req->blockedUserId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 207;
                $res->message = "형식에 맞지 않은 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 게시글 id 검사
            if (!isValidBandId($req->bandId)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "존재하지 않는 밴드 id";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유저 id 유효성 검사
            if(!isValidBandUser($req->bandId, $data->userId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 204;
                $res->message = "밴드에 가입되지 않는 유저 id";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 차단할 유저 id 유효성 검사
            if(!isValidBandUser($req->bandId, $req->blockedUserId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "밴드에 가입되지 않는 차단할 유저 id";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 이미 추가된 차단 유저인지 검사
            if(isExistUserBandBlockedUser($req->bandId, $data->userId, $req->blockedUserId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 206;
                $res->message = "이미 존재하는 차단 유저 id";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            createBlockedBandUser($req->bandId, $data->userId, $req->blockedUserId);
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "차단 유저 생성 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        case "getNewPostFeed":
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // paging을 쿼리 스트링으로 넣어줬을 경우
            if(!empty($_GET['paging'])){
                if(intval($_GET['paging']) < 0){
                    http_response_code(200);
                    $res->isSuccess = FALSE;
                    $res->code = 201;
                    $res->message = "paging 값은 음수일 수 없음";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                $page = intval($_GET['paging']);
            }
            else{
                $page = 0;
            }
            $res->result->postInfo = getNewPostFeed($data->userId, $page);
            if(empty($res->result->postInfo)){
                http_response_code(200);
                unset($res->result);
                $res->isSuccess = TRUE;
                $res->code = 101;
                $res->message = "게시물이 없음";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                http_response_code(200);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "새글 피드 게시물 조회 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

        case "createHiddenPost":
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 게시글 id 입력 누락 확인
            if(empty($req->postId)){
                http_response_code(201);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "게시글 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 형식에 맞는 입력 검사
            if(!is_int($req->postId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 204;
                $res->message = "형식에 맞지 않은 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 게시글 id 검사
            if (!isValidPostId($req->postId)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 게시글 ID";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            if(isExistHiddenPost($req->postId, $data->userId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "이미 추가된 숨긴 게시물";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            createHiddenPost($req->postId, $data->userId);
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "숨긴 게시물 생성 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        case "createBlockedBand":
            // 유효한 토큰 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res = returnMake($res, FALSE, 200, "유효하지 않은 토큰");
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            // 밴드 id 입력 누락 확인
            if(empty($req->bandId)){
                http_response_code(201);
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "밴드 id 입력 누락";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 형식에 맞는 입력 검사
            if(!is_int($req->bandId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 204;
                $res->message = "형식에 맞지 않은 입력";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 유효한 밴드 id 검사
            if (!isValidBandId($req->bandId)) {
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 밴드 id";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            // 이미 차단된 밴드인지 검사
            if(isExistUserBlockedBand($req->bandId, $data->userId)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "이미 차단된 밴드";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            createBlockedBand($req->bandId, $data->userId);
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "차단 밴드 생성 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;
    }


} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
