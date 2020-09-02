<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (object)array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "index":
            echo "API Server";
            break;
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
                 * API No. 1
                 * API Name : 메인 페이지 광고 이미지 조회 API
                 * 마지막 수정 날짜 : 20.09.01
        */
        case "getAd":
            $adsId = $vars['adsid'];
            $res->result = getAd($adsId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "메인 페이지 광고 이미지 조회 성공";
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
                 * API No. 2
                 * API Name : 유저가 가입한 밴드 조회 API
                 * 마지막 수정 날짜 : 20.09.01
        */
        case "getUserBand":
            $userId = $vars['userid'];
            $res->result = getUserBand($userId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저가 가입한 밴드 조회 성공";
            http_response_code(200);
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
//        case "getAdvertisement":
//            $res->result = getAdvertisement();
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "광고중인 브랜드 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "getProductRanking":
//            $res->result = getProductRanking();
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "상품 전체 판매 순위 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "getProductTypeRanking":
//            $productType = $_GET['productType'];
//
//            if (!isValidProductType($productType)) {
//                $res->isSuccess = FALSE;
//                $res->code = 228;
//                $res->message = "존재하지 않은 상품 타입입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = getProductTypeRanking($productType);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "상품 타입별 판매 순위 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "getCustomer":
//            $res->result = getCustomer();
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "간단 유저 목록 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//        /*
//         * API No. 0
//         * API Name : 테스트 Path Variable API
//         * 마지막 수정 날짜 : 19.04.29
//         */
//        case "getCustomerDetail":
//            $id = $vars['id'];
//            if (!isValidCustomerId($id)) {
//                $res->isSuccess = FALSE;
//                $res->code = 204;
//                $res->message = "존재하지 않은 고객 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = getCustomerDetail($vars["id"]);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "유저 세부 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "getCustomerInfo":
//            $id = $vars['id'];
//            if (!isValidCustomerId($id)) {
//                $res->isSuccess = FALSE;
//                $res->code = 204;
//                $res->message = "존재하지 않은 고객 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = getCustomerInfo($vars["id"]);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "유저 상품 관련 조회 성송";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "getCustomerPurchaseCount":
//            $id = $vars['id'];
//            if (!isValidCustomerId($id)) {
//                $res->isSuccess = FALSE;
//                $res->code = 204;
//                $res->message = "존재하지 않은 고객 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = getCustomerPurchaseCount($vars["id"]);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "유저 구매 상품 개수 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//        /*
//         * API No. 0
//         * API Name : 테스트 Body & Insert API
//         * 마지막 수정 날짜 : 19.04.29
//         */
//
//        case "getCustomerPrice":
//            $customerId = $_GET['customerId'];
//            $productId = $_GET['productId'];
//
//            if (!isValidCustomerId($customerId)) {
//                $res->isSuccess = FALSE;
//                $res->code = 204;
//                $res->message = "존재하지 않은 고객 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidProductId($productId)) {
//                $res->isSuccess = FALSE;
//                $res->code = 210;
//                $res->message = "존재하지 않은 상품 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = getCustomerPrice($customerId, $productId);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "유저 상품 구매 가격 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "getCustomerShoppingBasket":
//            $id = $vars['id'];
//            if (!isValidCustomerShoppingCustomerId($id)) {
//                $res->isSuccess = FALSE;
//                $res->code = 206;
//                $res->message = "존재하지 않은 고객 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = getCustomerShoppingBasket($id);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "유저 장바구니에 있는 상품 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "getProduct":
//            $res->result = getProduct();
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "간단 상품 목록 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//        /*
//         * API No. 0
//         * API Name : 테스트 Path Variable API
//         * 마지막 수정 날짜 : 19.04.29
//         */
//        case "getProductDetail":
//            $id = $vars['id'];
//            if (!isValidProductId($id)) {
//                $res->isSuccess = FALSE;
//                $res->code = 210;
//                $res->message = "존재하지 않은 상품 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = getProductDetail($vars["id"]);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "상품 세부 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//        /*
//         * API No. 0
//         * API Name : 테스트 Body & Insert API
//         * 마지막 수정 날짜 : 19.04.29
//         */
//
//        case "getProductDetailInfo":
//            $id = $vars['id'];
//            if (!isValidProductId($id)) {
//                $res->isSuccess = FALSE;
//                $res->code = 210;
//                $res->message = "존재하지 않은 상품 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = getProductDetailInfo($vars["id"]);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "상품 구매 및 리뷰 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "getBrand" :
//            $keyword = $_GET['keyword'];
//            if (!isValidBrandName($keyword)) {
//                $res->isSuccess = FALSE;
//                $res->code = 218;
//                $res->message = "존재하지 않은 브랜드 이름입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = getBrand($keyword);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "브랜드 이름 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "getBrandSaleCount":
//            $id = $vars['id'];
//            if (!isValidBrandId($id)) {
//                $res->isSuccess = FALSE;
//                $res->code = 216;
//                $res->message = "존재하지 않은 브랜드 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = getBrandSaleCount($vars["id"]);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "브랜드 별 판매 수 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "getReview":
//            $id = $vars['id'];
//            if (!isValidReviewId($id)) {
//                $res->isSuccess = FALSE;
//                $res->code = 222;
//                $res->message = "존재하지 않은 리뷰 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = getReview($vars["id"]);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "리뷰 조회 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "createAdvertisement":
//            //echo "req: " . $req->customerIdx. "\n";
//            if(!is_integer($req->adIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 240;
//                $res->message = "광고 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->brandIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 230;
//                $res->message = "브랜드 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->adBestImg)){
//                $res->isSuccess = FALSE;
//                $res->code = 241;
//                $res->message = "광고 대표 사진이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (isValidAdId($req->adIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 201;
//                $res->message = "이미 존재 하는 광고 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidBrandId($req->brandIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 216;
//                $res->message = "존재하지 않은 브랜드 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = createAdvertisement($req->adIdx, $req->brandIdx, $req->adBestImg);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "광고 생성 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "createAdvertisementImg":
//            //echo "req: " . $req->customerIdx. "\n";
//
//            if(!is_integer($req->adIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 240;
//                $res->message = "광고 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->adImg)){
//                $res->isSuccess = FALSE;
//                $res->code = 242;
//                $res->message = "광고 이미지가 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidAdId($req->adIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 200;
//                $res->message = "존재하지 않은 광고 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = createAdvertisementImg($req->adIdx, $req->adImg);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "광고 이미지 생성 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "createAdvertisementInfo":
//            //echo "req: " . $req->customerIdx. "\n";
//
//            if(!is_integer($req->adIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 240;
//                $res->message = "광고 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->adTitle)){
//                $res->isSuccess = FALSE;
//                $res->code = 243;
//                $res->message = "광고 제목이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->adContent)){
//                $res->isSuccess = FALSE;
//                $res->code = 244;
//                $res->message = "광고 내용이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->siteAddr)){
//                $res->isSuccess = FALSE;
//                $res->code = 245;
//                $res->message = "사이트 주소가 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->adStart)){
//                $res->isSuccess = FALSE;
//                $res->code = 246;
//                $res->message = "광고 시작일이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->adEnd)){
//                $res->isSuccess = FALSE;
//                $res->code = 247;
//                $res->message = "광고 종료일이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidAdId($req->adIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 200;
//                $res->message = "존재하지 않은 광고 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (isValidAdInfoId($req->adIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 203;
//                $res->message = "이미 존재하는 광고정보 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = createAdvertisementInfo($req->adIdx, $req->adTitle, $req->adContent, $req->siteAddr, $req->adStart, $req->adEnd);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "광고 내용 생성 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "createCustomer":
//            //echo "req: " . $req->customerIdx. "\n";
//
//            if(!is_integer($req->customerIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 250;
//                $res->message = "고객 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->customerName)){
//                $res->isSuccess = FALSE;
//                $res->code = 251;
//                $res->message = "고객 이름이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->customerGrade)){
//                $res->isSuccess = FALSE;
//                $res->code = 252;
//                $res->message = "고객 등급이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->customerSex)){
//                $res->isSuccess = FALSE;
//                $res->code = 253;
//                $res->message = "고객 성별이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->customerAge)){
//                $res->isSuccess = FALSE;
//                $res->code = 254;
//                $res->message = "고객 나이가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->customerHeight)){
//                $res->isSuccess = FALSE;
//                $res->code = 255;
//                $res->message = "고객 키가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->customerWeight)){
//                $res->isSuccess = FALSE;
//                $res->code = 256;
//                $res->message = "고객 몸무게가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->customerSavedMoney)){
//                $res->isSuccess = FALSE;
//                $res->code = 257;
//                $res->message = "고객 적립금이 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->customerPoint)){
//                $res->isSuccess = FALSE;
//                $res->code = 258;
//                $res->message = "고객 포인트가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->homeNumber)){
//                $res->isSuccess = FALSE;
//                $res->code = 259;
//                $res->message = "고객 집번호가 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->phoneNumber)){
//                $res->isSuccess = FALSE;
//                $res->code = 260;
//                $res->message = "고객 핸드폰 번호가 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->customerId)){
//                $res->isSuccess = FALSE;
//                $res->code = 261;
//                $res->message = "고객 Id가 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->customerPw)){
//                $res->isSuccess = FALSE;
//                $res->code = 262;
//                $res->message = "고객 Pw가 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (isValidCustomerId($req->customerIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 205;
//                $res->message = "이미 존재하는 고객 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if ($req->phoneNumber == NULL){
//                $res->isSuccess = FALSE;
//                $res->code = 226;
//                $res->message = "전화번호가 비었습니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = createCustomer($req->customerIdx, $req->customerName, $req->customerGrade, $req->customerSex, $req->customerAge,
//                $req->customerHeight, $req->customerWeight, $req->customerSavedMoney, $req->customerPoint,
//                $req->homeNumber, $req->phoneNumber, $req->customerId, $req->customerPw);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "유저 회원가입 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "createCustomerShoppingBasket":
//            //echo "req: " . $req->customerIdx. "\n";
//
//            if(!is_integer($req->customerIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 250;
//                $res->message = "고객 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->productIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 270;
//                $res->message = "상품 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->productCount)){
//                $res->isSuccess = FALSE;
//                $res->code = 284;
//                $res->message = "상품 개수가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->size)){
//                $res->isSuccess = FALSE;
//                $res->code = 285;
//                $res->message = "상품 사이즈가 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidCustomerId($req->customerIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 204;
//                $res->message = "존재하지 않은 고객 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidProductId($req->productIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 210;
//                $res->message = "존재하지 않은 상품 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidProductSize($req->productIdx, $req->size)) {
//                $res->isSuccess = FALSE;
//                $res->code = 212;
//                $res->message = "존재하지 않은 상품 사이즈입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (isValidCustomerShoppingId($req->customerIdx, $req->productIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 209;
//                $res->message = "이미 존재하는 장바구니 상품 입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = createCustomerShoppingBasket($req->customerIdx, $req->productIdx, $req->productCount, $req->size);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "유저 장바구니 상품 생성 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "createProduct":
//            if(!is_integer($req->productIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 270;
//                $res->message = "상품 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->productNum)){
//                $res->isSuccess = FALSE;
//                $res->code = 271;
//                $res->message = "상품 품번이 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->productDiscountRate)){
//                $res->isSuccess = FALSE;
//                $res->code = 272;
//                $res->message = "상품 할인율이 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->brandIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 230;
//                $res->message = "브랜드 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->productDeliveryFee)){
//                $res->isSuccess = FALSE;
//                $res->code = 273;
//                $res->message = "상품 배송비가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->productName)){
//                $res->isSuccess = FALSE;
//                $res->code = 274;
//                $res->message = "상품 이름이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->productType)){
//                $res->isSuccess = FALSE;
//                $res->code = 275;
//                $res->message = "상품 유형이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->productDetailType)){
//                $res->isSuccess = FALSE;
//                $res->code = 276;
//                $res->message = "상품 세부 유형이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->productBestImg)){
//                $res->isSuccess = FALSE;
//                $res->code = 277;
//                $res->message = "상품 대표 사진이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->productAddr)){
//                $res->isSuccess = FALSE;
//                $res->code = 278;
//                $res->message = "상품 사이트 주소가 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->productVideoAddr)){
//                $res->isSuccess = FALSE;
//                $res->code = 279;
//                $res->message = "상품 비디오 주소가 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->productSeason)){
//                $res->isSuccess = FALSE;
//                $res->code = 280;
//                $res->message = "상품 시즌이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->productSex)){
//                $res->isSuccess = FALSE;
//                $res->code = 281;
//                $res->message = "상품 성별이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->productUniqueImg)){
//                $res->isSuccess = FALSE;
//                $res->code = 282;
//                $res->message = "상품 한정판 유무가 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->productEngName)){
//                $res->isSuccess = FALSE;
//                $res->code = 283;
//                $res->message = "상품 영어 이름이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (isValidProductId($req->productIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 211;
//                $res->message = "이미 존재하는 상품 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidBrandId($req->brandIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 216;
//                $res->message = "존재하지 않은 브랜드 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (isValidProductNum($req->brandIdx, $req->productNum)) {
//                $res->isSuccess = FALSE;
//                $res->code = 215;
//                $res->message = "브랜드에서 이미 사용중인 품번입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = createProduct($req->productIdx, $req->productName, $req->productType, $req->productDetailType, $req->productBestImg,
//                $req->productNum, $req->productAddr, $req->productDiscountRate, $req->productVideoAddr,
//                $req->productSeason, $req->productSex, $req->productUniqueImg, $req->brandIdx, $req->productDeliveryFee, $req->productEngName);
//
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "상품 생성 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "createProductPrice":
//
//            if(!is_integer($req->productIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 270;
//                $res->message = "상품 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->bronze)){
//                $res->isSuccess = FALSE;
//                $res->code = 290;
//                $res->message = "브론즈등급 가격이 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->silver)){
//                $res->isSuccess = FALSE;
//                $res->code = 291;
//                $res->message = "실버등급 가격이 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->gold)){
//                $res->isSuccess = FALSE;
//                $res->code = 292;
//                $res->message = "골드등급 가격이 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->platinum)){
//                $res->isSuccess = FALSE;
//                $res->code = 293;
//                $res->message = "플래티넘등급 가격이 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->diamond)){
//                $res->isSuccess = FALSE;
//                $res->code = 294;
//                $res->message = "다이아몬드등급 가격이 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (isValidProductPriceId($req->productIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 221;
//                $res->message = "이미 가격이 매겨진 상품 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidProductId($req->productIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 210;
//                $res->message = "존재하지 않은 상품 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = createProductPrice($req->productIdx, $req->bronze, $req->silver, $req->gold, $req->platinum, $req->diamond);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "상품 가격 생성 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "createProductSale":
//
//            if(!is_integer($req->productIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 270;
//                $res->message = "상품 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->customerIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 250;
//                $res->message = "고객 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->productCount)){
//                $res->isSuccess = FALSE;
//                $res->code = 284;
//                $res->message = "상품 개수가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->paymentWay)){
//                $res->isSuccess = FALSE;
//                $res->code = 295;
//                $res->message = "결제 수단이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidProductId($req->productIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 210;
//                $res->message = "존재하지 않은 상품 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidCustomerId($req->customerIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 204;
//                $res->message = "존재하지 않은 고객 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = createProductSale($req->productIdx, $req->customerIdx, $req->productCount, $req->paymentWay);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "유저 상품 구매완료 정보 생성 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "createReview":
//            //echo "req: " . $req->customerIdx. "\n";
//
//            if(!is_integer($req->productIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 270;
//                $res->message = "상품 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->customerIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 250;
//                $res->message = "고객 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->reviewType)){
//                $res->isSuccess = FALSE;
//                $res->code = 301;
//                $res->message = "리뷰 유형이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_float($req->starPoint)){
//                $res->isSuccess = FALSE;
//                $res->code = 302;
//                $res->message = "별점이 소수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_float($req->satisfaction)){
//                $res->isSuccess = FALSE;
//                $res->code = 303;
//                $res->message = "만족도가 소수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->reviewSize)){
//                $res->isSuccess = FALSE;
//                $res->code = 304;
//                $res->message = "리뷰 크기가 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->reviewContent)){
//                $res->isSuccess = FALSE;
//                $res->code = 305;
//                $res->message = "리뷰 내용이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidProductId($req->productIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 210;
//                $res->message = "존재하지 않은 상품 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidCustomerId($req->customerIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 204;
//                $res->message = "존재하지 않은 고객 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidProductSize($req->productIdx, $req->reviewSize)) {
//                $res->isSuccess = FALSE;
//                $res->code = 212;
//                $res->message = "존재하지 않은 상품 사이즈입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//
//            $res->result = createReview($req->productIdx, $req->customerIdx, $req->reviewType, $req->starPoint,
//                $req->satisfaction, $req->reviewSize,$req->reviewContent);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "유저 리뷰 생성 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "createReviewImg":
//            //echo "req: " . $req->customerIdx. "\n";
//
//            if(!is_integer($req->reviewIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 300;
//                $res->message = "리뷰 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->reviewImg)){
//                $res->isSuccess = FALSE;
//                $res->code = 306;
//                $res->message = "리뷰 사진이 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidReviewId($req->reviewIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 222;
//                $res->message = "존재하지 않은 리뷰 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (isValidReviewIdImg($req->reviewIdx, $req->reviewImg)) {
//                $res->isSuccess = FALSE;
//                $res->code = 225;
//                $res->message = "이미 존재하는 리뷰 이미지입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = createReviewImg($req->reviewIdx, $req->reviewImg);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "유저 리뷰의 이미지 생성 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "deleteCustomerShoppingBasket":
//
//            $customerIdx = $_GET['customerIdx'];
//            $productIdx = $_GET['productIdx'];
//
//            if(!is_integer(customerIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 250;
//                $res->message = "고객 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer(productIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 270;
//                $res->message = "상품 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidCustomerShoppingId($customerIdx, $productIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 208;
//                $res->message = "존재하지 않은 장바구니 고객, 상품 ID입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = deleteCustomerShoppingBasket($customerIdx, $productIdx);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "장바구니 목록에 있는 상품 삭제 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "putCustomerShoppingBasket":
//            //echo "req: " . $req->customerIdx. "\n";
//
//            if(!is_integer($req->customerIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 250;
//                $res->message = "고객 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->productIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 270;
//                $res->message = "상품 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->productCount)){
//                $res->isSuccess = FALSE;
//                $res->code = 284;
//                $res->message = "상품 개수가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->size)){
//                $res->isSuccess = FALSE;
//                $res->code = 285;
//                $res->message = "상품 사이즈가 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidProductSize($req->productIdx, $req->size)) {
//                $res->isSuccess = FALSE;
//                $res->code = 212;
//                $res->message = "존재하지 않은 상품 사이즈입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidCustomerShoppingId($req->customerIdx, $req->productIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 208;
//                $res->message = "존재하지 않는 장바구니 상품 입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = putCustomerShoppingBasket($req->customerIdx, $req->productIdx, $req->size, $req->productCount);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "장바구니 목록에 있는 상품 전체 옵션 변경 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "patchCustomerShoppingBasketSize":
//            //echo "req: " . $req->customerIdx. "\n";
//
//            if(!is_integer($req->customerIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 250;
//                $res->message = "고객 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->productIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 260;
//                $res->message = "상품 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_string($req->size)){
//                $res->isSuccess = FALSE;
//                $res->code = 285;
//                $res->message = "상품 사이즈가 문자열이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidProductSize($req->productIdx, $req->size)) {
//                $res->isSuccess = FALSE;
//                $res->code = 212;
//                $res->message = "존재하지 않은 상품 사이즈입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidCustomerShoppingId($req->customerIdx, $req->productIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 208;
//                $res->message = "존재하지 않는 장바구니 상품 입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = patchCustomerShoppingBasketSize($req->customerIdx, $req->productIdx, $req->size);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "장바구니 목록에 있는 상품 크기 변경 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
//
//        case "patchCustomerShoppingBasketCount":
//            //echo "req: " . $req->customerIdx. "\n";
//
//            if(!is_integer($req->customerIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 250;
//                $res->message = "고객 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->productIdx)){
//                $res->isSuccess = FALSE;
//                $res->code = 270;
//                $res->message = "상품 인덱스가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if(!is_integer($req->productCount)){
//                $res->isSuccess = FALSE;
//                $res->code = 284;
//                $res->message = "상품 개수가 정수형이 아닙니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidCustomerShoppingId($req->customerIdx, $req->productIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 208;
//                $res->message = "존재하지 않는 장바구니 상품 입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $res->result = patchCustomerShoppingBasketCount($req->customerIdx, $req->productIdx, $req->productCount);
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "장바구니 목록에 있는 상품 개수 변경 성공";
//            http_response_code(200);
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
