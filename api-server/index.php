<?php
require './pdos/DatabasePdo.php';
require './pdos/IndexPdo.php';
require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

date_default_timezone_set('Asia/Seoul');
ini_set('default_charset', 'utf8mb4');

//에러출력하게 하는 코드
<<<<<<< HEAD
error_reporting(E_ALL); ini_set("display_errors", 1);
=======
//error_reporting(E_ALL); ini_set("display_errors", 1);
>>>>>>> bd944f95dabec54ba35790856cee031a056072a2

//Main Server API
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    /* ******************   Test   ****************** */
<<<<<<< HEAD
    $r->addRoute('GET', '/users', ['UserController', 'getNaverUser']);
    $r->addRoute('POST', '/users', ['UserController', 'createUser']);
    $r->addRoute('PATCH', '/users/{userid}', ['UserController', 'updateUser']);
    $r->addRoute('GET', '/ads', ['IndexController', 'getAd']);
    $r->addRoute('GET', '/bands/{userid}', ['BandController', 'getUserBand']);
    $r->addRoute('GET', '/bands/{bandid}/info', ['BandController', 'getBandInfo']);
    $r->addRoute('GET', '/jwt', ['MainController', 'validateJwt']);
    $r->addRoute('POST', '/jwt', ['MainController', 'createJwt']);
=======
    $r->addRoute('GET', '/', ['IndexController', 'index']);
    $r->addRoute('GET', '/ads/{adsid}', ['IndexController', 'getAd']);
    $r->addRoute('GET', '/bands/{userid}', ['IndexController', 'getUserBand']);
//    $r->addRoute('GET', '/productRanking', ['IndexController', 'getProductRanking']);
//    $r->addRoute('GET', '/productTypeRanking', ['IndexController', 'getProductTypeRanking']);
//    $r->addRoute('GET', '/customer', ['IndexController', 'getCustomer']);
//    $r->addRoute('GET', '/customer/{id}', ['IndexController', 'getCustomerDetail']);
//    $r->addRoute('GET', '/customerInfo/{id}', ['IndexController', 'getCustomerInfo']);
//    $r->addRoute('GET', '/customerPurchaseCount/{id}', ['IndexController', 'getCustomerPurchaseCount']);
//    $r->addRoute('GET', '/customerPrice', ['IndexController', 'getCustomerPrice']);
//    $r->addRoute('GET', '/customerShoppingBasket/{id}', ['IndexController', 'getCustomerShoppingBasket']);
//    $r->addRoute('GET', '/product', ['IndexController', 'getProduct']);
//    $r->addRoute('GET', '/product/{id}', ['IndexController', 'getProductDetail']);
//    $r->addRoute('GET', '/productDetailInfo/{id}', ['IndexController', 'getProductDetailInfo']);
//    $r->addRoute('GET', '/brand', ['IndexController', 'getBrand']);
//    $r->addRoute('GET', '/brandCount/{id}', ['IndexController', 'getBrandSaleCount']);
//    $r->addRoute('GET', '/review/{id}', ['IndexController', 'getReview']);
//    $r->addRoute('POST', '/advertisement', ['IndexController', 'createAdvertisement']);
//    $r->addRoute('POST', '/advertisementImg', ['IndexController', 'createAdvertisementImg']);
//    $r->addRoute('POST', '/advertisementInfo', ['IndexController', 'createAdvertisementInfo']);
//    $r->addRoute('POST', '/customer', ['IndexController', 'createCustomer']);
//    $r->addRoute('POST', '/customerShoppingBasket', ['IndexController', 'createCustomerShoppingBasket']);
//    $r->addRoute('POST', '/product', ['IndexController', 'createProduct']);
//    $r->addRoute('POST', '/productPrice', ['IndexController', 'createProductPrice']);
//    $r->addRoute('POST', '/purchaseProduct', ['IndexController', 'createProductSale']);
//    $r->addRoute('POST', '/review', ['IndexController', 'createReview']);
//    $r->addRoute('POST', '/reviewImg', ['IndexController', 'createReviewImg']);
//    $r->addRoute('PUT', '/customerShoppingBasket', ['IndexController', 'putCustomerShoppingBasket']);
//    $r->addRoute('PATCH', '/customerShoppingBasketSize', ['IndexController', 'patchCustomerShoppingBasketSize']);
//    $r->addRoute('PATCH', '/customerShoppingBasketCount', ['IndexController', 'patchCustomerShoppingBasketCount']);
//    $r->addRoute('DELETE', '/customerShoppingBasket', ['IndexController', 'deleteCustomerShoppingBasket']);
//    $r->addRoute('POST', '/customerJwt', ['MainController', 'createJwt']);
//    $r->addRoute('GET', '/customerJwtData', ['MainController', 'getDataList']);



//    $r->addRoute('GET', '/users', 'get_all_users_handler');
//    // {id} must be a number (\d+)
//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//    // The /{title} suffix is optional
//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
>>>>>>> bd944f95dabec54ba35790856cee031a056072a2
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'IndexController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/IndexController.php';
                break;
            case 'MainController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MainController.php';
                break;
<<<<<<< HEAD
            case 'BandController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/BandController.php';
                break;
            case 'UserController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/UserController.php';
                break;
            case 'SocialController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/SocialController.php';
                break;
=======
>>>>>>> bd944f95dabec54ba35790856cee031a056072a2
            /*case 'EventController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/EventController.php';
                break;
            case 'ProductController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ProductController.php';
                break;
            case 'SearchController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/SearchController.php';
                break;
            case 'ReviewController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ReviewController.php';
                break;
            case 'ElementController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ElementController.php';
                break;
            case 'AskFAQController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/AskFAQController.php';
                break;*/
        }

        break;
}
