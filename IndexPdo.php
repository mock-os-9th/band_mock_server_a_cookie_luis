<?php

//READ
function getAd($adsId)
{
    $pdo = pdoSqlConnect();
    $query = "select adsId as 광고인덱스,
       adsMainImg as 광고대표사진,
       adsUrl as 광고주
from Ads
where adsId = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$adsId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function getUserBand($userId)
{
    $pdo = pdoSqlConnect();
    $query = "select BandUser.bandId as 밴드인덱스,
       bandName as 밴드이름,
       bandImg as 밴드대표사
from BandUser left join Band on
BandUser.bandId = Band.bandId
where BandUser.userId = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//function getProductRanking()
//{
//    $pdo = pdoSqlConnect();
//    $query = "select Product.productIdx as 상품인덱스,
//       Product.productName as 상품명,
//       if(PC.productCount is not null, PC.productCount, 0) as 판매수
//from Product left join (select ProductSale.productIdx, count(*) as productCount from ProductSale group by ProductSale.productIdx) as PC
//on Product.productIdx = PC.productIdx
//order by 판매수 desc, 상품명 asc;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res;
//}
//
//function getProductTypeRanking($productType)
//{
//    $pdo = pdoSqlConnect();
//    $query = "select Product.productIdx as 상품인덱스,
//       Product.productName as 상품명,
//       if(PC.productCount is not null, PC.productCount, 0) as 판매수
//from Product left join (select ProductSale.productIdx, count(*) as productCount from ProductSale group by ProductSale.productIdx) as PC
//on Product.productIdx = PC.productIdx
//where productType=?
//order by 판매수 desc, 상품명 asc;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$productType]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res;
//}
//
//function getCustomer()
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT customerIdx as 고객인덱스, customerName as 고객명 FROM Customer;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res;
//}
//
////READ
//function getCustomerDetail($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT * FROM Customer WHERE customerIdx = ?;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$id]); //꼭 리스트안에 넣기
//    //    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res[0];
//}
//
//function getCustomerInfo($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "select Customer.customerName as 이름,
//       Customer.customerGrade as 등급,
//       concat(date_format(Customer.createdAt, '%Y.%m.%d')) as 가입일,
//       IF(Customer.customerSavedMoney IS NOT NULL, Customer.customerSavedMoney, 0) as 적립금,
//       IF(Customer.customerPoint IS NOT NULL, Customer.customerPoint, 0) as 포인트,
//       IF(CCC.couponCount IS NOT NULL, CCC.couponCount, 0) as 쿠폰수,
//       IF(CRC.reviewCount IS NOT NULL, CRC.reviewCount, 0) as 후기작성수,
//       IF(CCD.countDeposit IS NOT NULL, CCD.countDeposit, 0) as 입금결제수,
//       IF(CCOD.countOnDelivery IS NOT NULL, CCOD.countOnDelivery, 0) as 배송중수,
//       IF(CCDC.countDeliveryComplete IS NOT NULL, CCDC.countDeliveryComplete, 0) as 배송완료수,
//       IF(CCDP.countCustomerDecidePurchase IS NOT NULL, CCDP.countCustomerDecidePurchase, 0) as 구매확정수,
//       IF(CCOE.countOnExchange IS NOT NULL, CCOE.countOnExchange, 0) as 교환수,
//       IF(CCEC.countExchangeComplete IS NOT NULL, CCEC.countExchangeComplete, 0) as 교환완료수,
//       IF(CCOR.countOnRefund IS NOT NULL, CCOR.countOnRefund, 0) as 환불수,
//       IF(CCRC.countRefundComplete IS NOT NULL, CCRC.countRefundComplete, 0) as 환불완료수
//
//from Customer left join (select customerIdx, count(*) as couponCount from CustomerCoupon group by customerIdx) as CCC
//    on Customer.customerIdx = CCC.customerIdx
//    left join (select customerIdx, count(*) as reviewCount from Review group by customerIdx) as CRC
//    on Customer.customerIdx = CRC.customerIdx
//    left join (select customerIdx, count(*) as countDeposit from CustomerDeposit group by customerIdx) as CCD
//    on Customer.customerIdx = CCD.customerIdx
//    left join (select customerIdx, count(*) as countOnDelivery from CustomerOnDelivery where state = '배송중' group by customerIdx) as CCOD
//    on Customer.customerIdx = CCOD.customerIdx
//    left join (select customerIdx, count(*) as countDeliveryComplete from CustomerOnDelivery where state = '배송완료' group by customerIdx) as CCDC
//    on Customer.customerIdx = CCDC.customerIdx
//    left join (select customerIdx, count(*) as countCustomerDecidePurchase from ProductSale group by customerIdx) as CCDP
//    on Customer.customerIdx = CCDP.customerIdx
//    left join (select customerIdx, count(*) as countOnExchange from CustomerExchange where state = '교환중' group by customerIdx) as CCOE
//    on Customer.customerIdx = CCOE.customerIdx
//    left join (select customerIdx, count(*) as countExchangeComplete from CustomerExchange where state = '교환완료' group by customerIdx) as CCEC
//    on Customer.customerIdx = CCEC.customerIdx
//    left join (select customerIdx, count(*) as countOnRefund from CustomerRefund where state = '환불' group by customerIdx) as CCOR
//    on Customer.customerIdx = CCOR.customerIdx
//    left join (select customerIdx, count(*) as countRefundComplete from CustomerRefund where state = '환불완료' group by customerIdx) as CCRC
//    on Customer.customerIdx = CCRC.customerIdx
//
//where Customer.customerIdx = ?;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$id]); //꼭 리스트안에 넣기
//    //    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res[0];
//}
//
//function getCustomerPurchaseCount($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT Customer.customerName, IF(PS.saleCount IS NOT NULL, PS.saleCount, 0) as saleCount
//FROM Customer LEFT JOIN (SELECT ProductSale.customerIdx, COUNT(*) as saleCount FROM ProductSale GROUP BY ProductSale.customerIdx) as PS
//ON Customer.customerIdx = PS.customerIdx where Customer.customerIdx = ?;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$id]); //꼭 리스트안에 넣기
//    //    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res[0];
//}
//
//function getCustomerPrice($customerId, $productId)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT Customer.customerName as 고객명, Customer.customerGrade as 고객등급, price as 가격 from ProductPrice left join Product
//                on ProductPrice.productIdx = Product.productIdx
//                 left join Customer
//                on ProductPrice.customerGrade = Customer.customerGrade
//                where Customer.customerIdx = ? and Product.productIdx = ?;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$customerId, $productId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res[0];
//}
//
//function getCustomerShoppingBasket($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "
//select Customer.customerName as 고객이름,
//       Product.productName as 상품이름,
//       CustomerShoppingBasket.size as 사이즈,
//       IF(ProductSizeCount.stockCount >=5, '재고 5개 이상', '재고 5개 이하') as 재고,
//       CustomerShoppingBasket.productCount as 상품개수,
//       ProductPrice.price as 기존가격,
//       truncate(IF(CustomerShoppingBasket.productIdx in (select ProductGradeSale.productIdx
//           from ProductGradeSale), SP.salePrice, ProductPrice.price) -
//        IF(productDiscountRate = 0, 0, ProductPrice.price*(productDiscountRate/100)), 0) as 할인가격,
//       truncate(IF(CustomerShoppingBasket.productIdx in (select ProductGradeSale.productIdx
//           from ProductGradeSale), SP.salePrice, ProductPrice.price) -
//        IF(productDiscountRate = 0, 0, ProductPrice.price*(productDiscountRate/100)), 0)*CustomerShoppingBasket.productCount as 총가격,
//       Product.deliveryFee as 배송비,
//       IF(CustomerShoppingBasket.productIdx in (select ProductGradeSale.productIdx
//           from ProductGradeSale), '가능', '불가능') as 등급할인,
//       IF(CustomerShoppingBasket.productIdx in (select ProductFirstSavedMoneySale.productIdx
//           from ProductFirstSavedMoneySale),'가능','불가능') as 적립금선할인,
//       concat(concat((-Customer.customerSavedMoney)), '원 가능') as 보유적립금사용,
//       IF(CustomerShoppingBasket.productIdx in (select ProductCoupon.productIdx
//           from ProductCoupon), '사용', '사용불가능') as 쿠폰여부,
//       IF(CustomerShoppingBasket.productIdx in (select Gift.productIdx
//            from Gift), 'O', 'X') as 사은품,
//       IF(CustomerShoppingBasket.productIdx in (select BestProductCount.productIdx
//           from BestProductCount
//           where salesCount >= 7), 'O', 'X') as 베스트상품,
//       IF((DATEDIFF(CURDATE(), Product.createdAt) <= 14), 'O', 'X') as 신상품
//from CustomerShoppingBasket left join Product
//    on CustomerShoppingBasket.productIdx = Product.productIdx
//    left join Customer
//    on CustomerShoppingBasket.customerIdx = Customer.customerIdx
//    left join ProductSizeCount
//    on CustomerShoppingBasket.productIdx = ProductSizeCount.productIdx and CustomerShoppingBasket.size = ProductSizeCount.size
//    left join ProductPrice
//    on CustomerShoppingBasket.productIdx = ProductPrice.productIdx and ProductPrice.customerGrade = '브론즈'
//    left join (select CustomerShoppingBasket.customerIdx, CustomerShoppingBasket.productIdx, price as salePrice
//    from CustomerShoppingBasket left join ProductPrice
//    on CustomerShoppingBasket.productIdx = ProductPrice.productIdx
//    left join Customer
//    on CustomerShoppingBasket.customerIdx = Customer.customerIdx
//    where Customer.customerGrade = ProductPrice.customerGrade) as SP
//    on CustomerShoppingBasket.customerIdx = SP.customerIdx and CustomerShoppingBasket.productIdx = SP.productIdx
//    left join ProductFirstSavedMoneySale
//    on CustomerShoppingBasket.productIdx = ProductFirstSavedMoneySale.productIdx
//    left join ProductGradeSale
//    on CustomerShoppingBasket.productIdx = ProductGradeSale.productIdx
//where CustomerShoppingBasket.customerIdx = ?;
//";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$id]); //꼭 리스트안에 넣기
//    //    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res;
//}
//
//function getProduct()
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT productIdx as 상품인덱스, productName as 상품명 FROM Product;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res;
//}
//
////READ
//function getProductDetail($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT * FROM Product WHERE productIdx = ?;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$id]); //꼭 리스트안에 넣기
//    //    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res[0];
//}
//
//function getProductDetailInfo($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "select distinct Product.productIdx as 상품아이디,
//       concat(Product.productType,' > ', Product.productDetailType) as 세부상품명,
//       Product.productName as 상품명,
//       Product.productEngName as 상품영어이름,
//       IF(round(avgStar,1) IS NOT NULL, round(avgStar,1), 0.0) as 별점,
//       IF(sumReview IS NOT NULL, sumReview, 0) as 후기개수,
//       IF(inquiryCount IS NOT NULL, inquiryCount, 0) as 문의개수,
//       IF(goodCount IS NOT NULL, goodCount, 0) as 좋아요개수,
//       IF(round(avgSatisfaction, 2) IS NOT NULL, round(avgSatisfaction, 2), 0) as 만족도,
//       concat(concat((select distinct price
//       from ProductPrice
//       where customerGrade = '브론즈' and ProductPrice.productIdx = Product.productIdx)
//           ),' ~ ',
//        concat((select distinct price
//            from ProductPrice
//            where customerGrade = '다이아몬드' and ProductPrice.productIdx = Product.productIdx)
//            )
//        ) as 가격,
//        productDiscountRate as 할인율,
//        productAddr as 사이트주소,
//        IF(viewCount IS NOT NULL, viewCount, 0) as 총조회수,
//        IF(saleCount IS NOT NULL, saleCount, 0) as 누적판매,
//        Product.productNum as 품번,
//        Brand.brandName as 브랜드명,
//        IF(Product.productSeason IS NOT NULL, Product.productSeason, '계절없음') as 시즌,
//        (CASE
//            WHEN Product.productSex = 'M' then '남성'
//            WHEN Product.productSex = 'U' then '혼용'
//            WHEN Product.productSex = 'F' then '여성'
//        END) as 성별,
//        (CASE
//            WHEN ProductDelivery.deliveryCompany IS NULL THEN
//            concat(deliveryType, '/', deliveryMethod)
//            WHEN ProductDelivery.deliveryCompany IS NOT NULL THEN
//            concat(deliveryType, '/', deliveryMethod, '/', deliveryCompany)
//        END) as 배송방법,
//        (CASE
//            WHEN ProductRelease.releaseConstraint IS NULL THEN
//            concat(releaseDay)
//            ELSE concat(concat(releaseDay),'/',releaseConstraint)
//        END) as 출고기간,
//        ProductSizeImg.productSizeImg as 사이즈표
//
//from Product left join (select productIdx, avg(starPoint) as avgStar from Review group by productIdx) as GAS
//    on Product.productIdx = GAS.productIdx
//    left join (select productIdx, count(*) as sumReview from Review group by productIdx) as PRC
//    on Product.productIdx = PRC.productIdx
//    left join (select productIdx, avg(satisfaction) as avgSatisfaction from Review group by productIdx) as PSF
//    on Product.productIdx = PSF.productIdx
//    left join (select productIdx, count(*) as viewCount from ProductView group by productIdx) as TVC
//    on Product.productIdx = TVC.productIdx
//    left join (select productIdx, count(*) as inquiryCount from ProductInquiry group by productIdx) as IC
//    on Product.productIdx = IC.productIdx
//    left join (select productIdx, count(*) as goodCount from PressGood group by productIdx) as CG
//    on Product.productIdx = CG.productIdx
//    left join (select productIdx, count(*) as saleCount from ProductSale group by productIdx) as BPC
//    on Product.productIdx = BPC.productIdx
//    left join ProductDelivery
//    on Product.productIdx = ProductDelivery.productIdx
//    left join ProductRelease
//    on Product.productIdx = ProductRelease.productIdx
//    left join ProductSizeImg
//    on Product.productIdx = ProductSizeImg.productIdx,
//    ProductPrice, Brand
//
//where Product.productIdx = ProductPrice.productIdx and
//      Product.brandIdx = Brand.brandIdx and
//      Product.productIdx = ?;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$id]); //꼭 리스트안에 넣기
//    //    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res[0];
//}
//
//function getBrand($keyword)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT brandIdx as 브랜드인덱스, brandName as 브랜드이름 FROM Brand WHERE brandName like concat('%', ?, '%');";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$keyword]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res[0];
//}
//
//function getBrandSaleCount($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "select Brand.brandName as 브랜드명, FN.brandCount as 브랜드판매수
//from Brand left join (select brandIdx, SUM(BC.productCount) as brandCount
//from Product left join (select Product.productIdx, PC.productCount
//from Product left join (select ProductSale.productIdx, count(*) as productCount from ProductSale group by ProductSale.productIdx) as PC
//on Product.productIdx = PC.productIdx) as BC
//on Product.productIdx = BC.productIdx
//group by brandIdx) as FN
//on Brand.brandIdx = FN.brandIdx
//where Brand.brandIdx = ?;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$id]); //꼭 리스트안에 넣기
//    //    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res[0];
//}
//
//function getReview($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "select distinct Customer.customerName as 이름,
//       concat(date_format(Review.createdAt, '%Y-%m-%d %H:%m')) as 날짜,
//       Product.productName as 상품명,
//       Review.reviewSize as 사이즈,
//       (CASE
//            WHEN Customer.customerSex = 'M' then '남성'
//            ELSE '여성'
//        END) as 성별,
//       concat(Customer.customerHeight,'cm', ',', customerWeight, 'kg') as 체형,
//       Review.starPoint as 별점,
//       Review.reviewContent as 내용
//
//from Review left join Product
//    on Review.productIdx = Product.productIdx
//    left join Customer
//    on Review.customerIdx = Customer.customerIdx
//
//where Review.reviewIdx = ?;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$id]); //꼭 리스트안에 넣기
//    //    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res[0];
//}
//
//function createAdvertisement($adIdx, $brandIdx, $adBestImg)
//{
//    $pdo = pdoSqlConnect();
//    $query = "INSERT INTO AdTitle (adIdx, brandIdx, adBestImg) VALUES (?,?,?);";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$adIdx, $brandIdx, $adBestImg]);
//
//    $st = null;
//    $pdo = null;
//
//}
//
//function createAdvertisementImg($adIdx, $adImg)
//{
//    $pdo = pdoSqlConnect();
//    $query = "INSERT INTO AdImg (adIdx, adImg) VALUES (?,?);";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$adIdx, $adImg]);
//
//    $st = null;
//    $pdo = null;
//
//}
//
//function createAdvertisementInfo($adIdx, $adTitle, $adContent, $siteAddr, $adStart, $adEnd)
//{
//    $pdo = pdoSqlConnect();
//    $query = "INSERT INTO AdInfo (adIdx, adTitle, adContent, siteAddr, adStart, adEnd) VALUES (?,?,?,?,?,?);";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$adIdx, $adTitle, $adContent, $siteAddr, $adStart, $adEnd]);
//
//    $st = null;
//    $pdo = null;
//
//}
//
//function createCustomer($id, $name, $grade, $sex, $age, $height, $weight, $savedMoney, $point, $homeNumber, $phoneNumber, $customerId, $customerPw)
//{
//    $pdo = pdoSqlConnect();
//    $query = "INSERT INTO Customer (customerIdx, customerName, customerGrade, customerSex, customerAge,
//    customerHeight, customerWeight, customerSavedMoney, customerPoint, homeNumber, phoneNumber) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?);";
//
//    if ($grade == null) {
//        $grade = "브론즈";
//    }
//    if ($savedMoney == null) {
//        $savedMoney = 0;
//    }
//    if ($point == null) {
//        $point = 0;
//    }
//    if ($homeNumber == null) {
//        $homeNumber = "NULL";
//    }
//    $st = $pdo->prepare($query);
//    $st->execute([$id, $name, $grade, $sex, $age, $height, $weight, $savedMoney, $point, $homeNumber, $phoneNumber, $customerId, $customerPw]);
//
//    $st = null;
//    $pdo = null;
//
//}
//
//function createCustomerShoppingBasket($customerIdx, $productIdx, $productCount, $size)
//{
//    $pdo = pdoSqlConnect();
//    $query = "INSERT INTO CustomerShoppingBasket (customerIdx, productIdx, productCount, size) VALUES (?,?,?,?);";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$customerIdx, $productIdx, $productCount, $size]);
//
//    $st = null;
//    $pdo = null;
//
//}
//
//function createProduct($productIdx, $productName, $productType, $productDetailType, $productBestImg,
//                $productNum, $productAddr, $productDiscountRate, $productVideoAddr,
//                $productSeason, $productSex, $productUniqueImg, $brandIdx, $productDeliveryFee, $productEngName)
//{
//    $pdo = pdoSqlConnect();
//    $query = "INSERT INTO Product (productIdx, productName, productType, productDetailType,
//    productBestImg, productNum, productAddr, productDiscountRate, productVideoAddr, productSeason,
//    productSex, productUniqueImg, brandIdx, deliveryFee, productEngName) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
//
//
//    if ($productDiscountRate == null) {
//        $productDiscountRate = 0;
//    }
//    if ($productVideoAddr == null) {
//        $productVideoAddr = "NULL";
//    }
//    if ($productSex == null) {
//        $productSex = "M";
//    }
//    if ($productUniqueImg == null) {
//        $productUniqueImg = "NULL";
//    }
//    if ($productDeliveryFee == null) {
//        $productDeliveryFee = 0;
//    }
//
//    $st = $pdo->prepare($query);
//    $st->execute([$productIdx, $productName, $productType, $productDetailType, $productBestImg,
//        $productNum, $productAddr, $productDiscountRate, $productVideoAddr,
//        $productSeason, $productSex, $productUniqueImg, $brandIdx, $productDeliveryFee, $productEngName]);
//    $st = null;
//    $pdo = null;
//
//}
//
//function createProductPrice($productIdx, $bronze, $silver, $gold, $platinum, $diamond)
//{
//    $pdo = pdoSqlConnect();
//    $query = "insert into ProductPrice(productIdx, customerGrade, price) VALUES (?,'브론즈',?);
//insert into ProductPrice(productIdx, customerGrade, price) VALUES (?,'실버',?);
//insert into ProductPrice(productIdx, customerGrade, price) VALUES (?,'골드',?);
//insert into ProductPrice(productIdx, customerGrade, price) VALUES (?,'플래티넘',?);
//insert into ProductPrice(productIdx, customerGrade, price) VALUES (?,'다이아몬드',?);";
//
//
//    $st = $pdo->prepare($query);
//    $st->execute([$productIdx, $bronze, $productIdx, $silver, $productIdx, $gold, $productIdx, $platinum, $productIdx, $diamond]);
//
//    $st = null;
//    $pdo = null;
//
//}
//
//function createProductSale($productIdx, $customerIdx, $productCount, $paymentWay)
//{
//    $pdo = pdoSqlConnect();
//    $query = "INSERT INTO ProductSale (productIdx, customerIdx, productCount, paymentWay) VALUES (?,?,?,?);";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$productIdx, $customerIdx, $productCount, $paymentWay]);
//    $st = null;
//    $pdo = null;
//
//}
//
//function createReview($productIdx, $customerIdx, $reviewType, $starPoint, $satisfaction, $reviewSize,$reviewContent)
//{
//    $pdo = pdoSqlConnect();
//    $query = "INSERT INTO Review (productIdx, customerIdx, reviewType, starPoint, satisfaction, reviewSize,reviewContent) VALUES (?,?,?,?,?,?,?);";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$productIdx, $customerIdx, $reviewType, $starPoint, $satisfaction, $reviewSize,$reviewContent]);
//
//    $st = null;
//    $pdo = null;
//
//}
//
//function createReviewImg($reviewIdx, $reviewImg)
//{
//    $pdo = pdoSqlConnect();
//    $query = "INSERT INTO ReviewImg (reviewIdx, reviewImg) VALUES (?,?);";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$reviewIdx, $reviewImg]);
//
//    $st = null;
//    $pdo = null;
//
//}
//
//function putCustomerShoppingBasket($customerIdx, $productIdx, $size, $productCount)
//{
//    $pdo = pdoSqlConnect();
//    $query = "UPDATE CustomerShoppingBasket
//SET productCount = ?
//WHERE customerIdx = ? and productIdx = ?;
//UPDATE CustomerShoppingBasket
//SET size = ?
//WHERE customerIdx = ? and productIdx = ?;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$productCount, $customerIdx, $productIdx, $size, $customerIdx, $productIdx]);
//
//    $st = null;
//    $pdo = null;
//
//}
//
//function patchCustomerShoppingBasketSize($customerIdx, $productIdx, $size)
//{
//    $pdo = pdoSqlConnect();
//    $query = "UPDATE CustomerShoppingBasket
//SET size = ?
//WHERE customerIdx = ? and productIdx = ?;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$size, $customerIdx, $productIdx]);
//
//    $st = null;
//    $pdo = null;
//
//}
//
//function patchCustomerShoppingBasketCount($customerIdx, $productIdx, $productCount)
//{
//    $pdo = pdoSqlConnect();
//    $query = "UPDATE CustomerShoppingBasket
//SET productCount = ?
//WHERE customerIdx = ? and productIdx = ?;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$productCount, $customerIdx, $productIdx]);
//
//    $st = null;
//    $pdo = null;
//
//}
//
//function deleteCustomerShoppingBasket($customerIdx, $productIdx)
//{
//    $pdo = pdoSqlConnect();
//    $query = "DELETE FROM CustomerShoppingBasket WHERE customerIdx = ? and productIdx =?;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$customerIdx, $productIdx]);
//    $st = null;
//    $pdo = null;
//
//}
//
//function isValidAdId($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM AdTitle WHERE adIdx = ?) AS exist;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$id]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidAdInfoId($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM AdInfo WHERE adIdx = ?) AS exist;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$id]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidCustomerId($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM Customer WHERE customerIdx = ?) AS exist;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$id]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidCustomerShoppingCustomerId($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM CustomerShoppingBasket WHERE customerIdx = ?) AS exist;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$id]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidCustomerShoppingId($customerIdx, $productIdx)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM CustomerShoppingBasket WHERE customerIdx = ? and productIdx = ?) AS exist;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$customerIdx, $productIdx]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidProductId($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM Product WHERE productIdx = ?) AS exist;";
//
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$id]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidProductType($type)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM (select distinct productType from Product) as EX WHERE EX.productType = ?) AS exist;";
//
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$type]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidProductSize($id, $size)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM (select ProductSizeCount.productIdx, ProductSizeCount.size, Product.productType
//from ProductSizeCount left join Product on ProductSizeCount.productIdx = Product.productIdx) as PS WHERE PS.productIdx = ? and PS.size = ?) AS exist;";
//
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$id, $size]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidProductNum($brandIdx, $productNum)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM Product WHERE brandIdx = ? and productNum = ?) AS exist;";
//
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$brandIdx, $productNum]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidBrandId($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM Brand WHERE brandIdx = ?) AS exist;";
//
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$id]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidBrandName($keyword)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM Brand WHERE brandName like concat('%', ?, '%')) AS exist;";
//
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$keyword]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidProductPriceId($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM ProductPrice WHERE productIdx = ?) AS exist;";
//
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$id]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidReviewId($id)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM Review WHERE reviewIdx = ?) AS exist;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$id]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidReviewIdImg($id, $img)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM ReviewImg WHERE reviewIdx = ? and reviewImg = ?) AS exist;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$id, $img]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}
//
//function isValidUser($id, $pw)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM Customer WHERE id = ? and pw = ?) AS exist;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$id, $pw]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    return intval($res[0]["exist"]);
//
//}

// CREATE
//    function addMaintenance($message){
//        $pdo = pdoSqlConnect();
//        $query = "INSERT INTO MAINTENANCE (MESSAGE) VALUES (?);";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message]);
//
//        $st = null;
//        $pdo = null;
//
//    }


// UPDATE
//    function updateMaintenanceStatus($message, $status, $no){
//        $pdo = pdoSqlConnect();
//        $query = "UPDATE MAINTENANCE
//                        SET MESSAGE = ?,
//                            STATUS  = ?
//                        WHERE NO = ?";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message, $status, $no]);
//        $st = null;
//        $pdo = null;
//    }

// RETURN BOOLEAN
//    function isRedundantEmail($email){
//        $pdo = pdoSqlConnect();
//        $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE EMAIL= ?) AS exist;";
//
//
//        $st = $pdo->prepare($query);
//        //    $st->execute([$param,$param]);
//        $st->execute([$email]);
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res = $st->fetchAll();
//
//        $st=null;$pdo = null;
//
//        return intval($res[0]["exist"]);
//
//    }
