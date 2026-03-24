
<?php

class ReturnData{
var $aCSUrl;//string
var $acquirerAddress;//string
var $acquirerAuthCode;//string
var $acquirerAuthResponseCode;//string
var $acquirerCity;//string
var $acquirerConsumerLimit;//string
var $acquirerErrorDescription;//string
var $acquirerFirstName;//string
var $acquirerLastName;//string
var $acquirerMerchantLimit;//string
var $acquirerZipCode;//string
var $amount;//long
var $errorMsg;//string
var $infoCode;//string
var $infoDescription;//string
var $pAReqMsg;//string
var $resultCode;//int
var $resultText;//string
var $verifyID;//long
}
class MPInitFormReturnData{
var $amount;//string
var $initFormURL;//string
var $oAuthToken;//string
var $resultCode;//int
var $resultText;//string
var $verifyID;//long
}
class MPValidationReturnData{
var $amount;//long
var $cardBrandId;//string
var $cardExpMonth;//int
var $cardExpYear;//int
var $ccPart;//string
var $ccType;//string
var $consumerEmail;//string
var $consumerPhone;//string
var $email;//string
var $phone;//string
var $resultCode;//int
var $resultText;//string
var $shippingAddressLine1;//string
var $shippingAddressLine2;//string
var $shippingCity;//string
var $shippingCountry;//string
var $shippingFirstName;//string
var $shippingLastName;//string
var $shippingZipCode;//string
var $verifyID;//long
}
class ShoppingCartItem{
var $description;//string
var $quantity;//long
var $value;//long
var $imageURL;//string
}
class ShoppingCart{
var $currencyCode;//string
var $subtotal;//long
var $shoppingCartItem;//ShoppingCartItem
}
class checkSwedishPersNo{
var $shopName;//string
var $userName;//string
var $password;//string
var $persNo;//string
}
class checkSwedishPersNoResponse{
var $return;//boolean
}
class accountTransactionAuthorize{
var $shopName;//string
var $userName;//string
var $password;//string
var $billingFirstName;//string
var $billingLastName;//string
var $billingAddress;//string
var $billingCity;//string
var $billingCountry;//string
var $bankCode;//string
var $accountCode;//string
var $eMail;//string
var $ip;//string
var $data;//string
var $currency;//string
var $method;//string
var $referenceNo;//string
var $extra;//string
}
class accountTransactionAuthorizeResponse{
var $return;//ReturnData
}
class askIf3DSEnrolled{
var $shopName;//string
var $userName;//string
var $password;//string
var $billingFirstName;//string
var $billingLastName;//string
var $billingAddress;//string
var $billingCity;//string
var $billingCountry;//string
var $cc;//string
var $expM;//int
var $expY;//int
var $eMail;//string
var $ip;//string
var $data;//string
var $currency;//string
var $httpAcceptHeader;//string
var $httpUserAgentHeader;//string
var $method;//string
var $referenceNo;//string
var $extra;//string
}
class askIf3DSEnrolledResponse{
var $return;//ReturnData
}
class authReversal{
var $shopName;//string
var $userName;//string
var $password;//string
var $verifyID;//long
var $amount;//long
var $extra;//string
}
class authReversalResponse{
var $return;//ReturnData
}
class authorize{
var $shopName;//string
var $userName;//string
var $password;//string
var $billingFirstName;//string
var $billingLastName;//string
var $billingAddress;//string
var $billingCity;//string
var $billingCountry;//string
var $cc;//string
var $expM;//int
var $expY;//int
var $eMail;//string
var $ip;//string
var $data;//string
var $currency;//string
var $method;//string
var $referenceNo;//string
var $extra;//string
}
class authorizeResponse{
var $return;//ReturnData
}
class authorize3DS{
var $shopName;//string
var $userName;//string
var $password;//string
var $verifyID;//long
var $paRes;//string
var $extra;//string
}
class authorize3DSResponse{
var $return;//ReturnData
}
class authorizeAndSettle{
var $shopName;//string
var $userName;//string
var $password;//string
var $billingFirstName;//string
var $billingLastName;//string
var $billingAddress;//string
var $billingCity;//string
var $billingCountry;//string
var $cc;//string
var $expM;//int
var $expY;//int
var $eMail;//string
var $ip;//string
var $data;//string
var $currency;//string
var $method;//string
var $referenceNo;//string
var $extra;//string
}
class authorizeAndSettleResponse{
var $return;//ReturnData
}
class authorizeAndSettle3DS{
var $shopName;//string
var $userName;//string
var $password;//string
var $verifyID;//long
var $paRes;//string
var $extra;//string
}
class authorizeAndSettle3DSResponse{
var $return;//ReturnData
}
class customerCheck{
var $shopName;//string
var $userName;//string
var $password;//string
var $billingFirstName;//string
var $billingLastName;//string
var $billingAddress;//string
var $billingZipCode;//string
var $billingCity;//string
var $billingCountry;//string
var $eMail;//string
var $ip;//string
var $data;//string
var $currency;//string
var $method;//string
var $referenceNo;//string
var $extra;//string
}
class customerCheckResponse{
var $return;//ReturnData
}
class eInvoice{
var $shopName;//string
var $userName;//string
var $password;//string
var $verifyID;//long
var $extra;//string
}
class eInvoiceResponse{
var $return;//ReturnData
}
class fundsTransfer{
var $shopName;//string
var $userName;//string
var $password;//string
var $billingFirstName;//string
var $billingLastName;//string
var $billingAddress;//string
var $billingCity;//string
var $billingCountry;//string
var $cc;//string
var $expM;//int
var $expY;//int
var $eMail;//string
var $ip;//string
var $data;//string
var $currency;//string
var $method;//string
var $referenceNo;//string
var $extra;//string
}
class fundsTransferResponse{
var $return;//ReturnData
}
class invoice{
var $shopName;//string
var $userName;//string
var $password;//string
var $billingFirstName;//string
var $billingLastName;//string
var $billingAddress;//string
var $billingZipCode;//string
var $billingCity;//string
var $billingCountry;//string
var $eMail;//string
var $persNo;//string
var $ip;//string
var $data;//string
var $currency;//string
var $method;//string
var $referenceNo;//string
var $extra;//string
}
class invoiceResponse{
var $return;//ReturnData
}
class refund{
var $shopName;//string
var $userName;//string
var $password;//string
var $verifyID;//long
var $amount;//long
var $extra;//string
}
class refundResponse{
var $return;//ReturnData
}
class settle{
var $shopName;//string
var $userName;//string
var $password;//string
var $verifyID;//long
var $amount;//long
var $extra;//string
}
class settleResponse{
var $return;//ReturnData
}
class subscribe{
var $shopName;//string
var $userName;//string
var $password;//string
var $verifyID;//long
var $data;//string
var $ip;//string
var $currency;//string
var $extra;//string
}
class subscribeResponse{
var $return;//ReturnData
}
class subscribeAndSettle{
var $shopName;//string
var $userName;//string
var $password;//string
var $verifyID;//long
var $data;//string
var $ip;//string
var $currency;//string
var $extra;//string
}
class subscribeAndSettleResponse{
var $return;//ReturnData
}
class mpInitForm{
var $shopName;//string
var $userName;//string
var $password;//string
var $data;//string
var $currency;//string
var $ip;//string
var $suppressShippingAddress;//boolean
var $test;//boolean
var $acceptableCards;//string
var $shippingLocationProfile;//string
var $shippingFirstName;//string
var $shippingLastName;//string
var $shippingAddressLine1;//string
var $shippingAddressLine2;//string
var $shippingCountry;//string
var $shippingCity;//string
var $shippingZipCode;//string
var $consumerPhone;//string
var $consumerEmail;//string
var $referenceNo;//string
var $extra;//string
}
class mpInitFormResponse{
var $return;//MPInitFormReturnData
}
class mpValidate{
var $shopName;//string
var $userName;//string
var $password;//string
var $verifyID;//long
var $oAuthToken;//string
var $oAuthVerifier;//string
var $checkoutResourceUrl;//string
var $extra;//string
}
class mpValidateResponse{
var $return;//MPValidationReturnData
}
class mpAuthorize{
var $shopName;//string
var $userName;//string
var $password;//string
var $verifyID;//long
var $method;//string
var $extra;//string
}
class mpAuthorizeResponse{
var $return;//ReturnData
}
class dibs 
 {
 var $soapClient;
 
private static $classmap = array('ReturnData'=>'ReturnData'
,'MPInitFormReturnData'=>'MPInitFormReturnData'
,'MPValidationReturnData'=>'MPValidationReturnData'
,'ShoppingCartItem'=>'ShoppingCartItem'
,'ShoppingCart'=>'ShoppingCart'
,'checkSwedishPersNo'=>'checkSwedishPersNo'
,'checkSwedishPersNoResponse'=>'checkSwedishPersNoResponse'
,'accountTransactionAuthorize'=>'accountTransactionAuthorize'
,'accountTransactionAuthorizeResponse'=>'accountTransactionAuthorizeResponse'
,'askIf3DSEnrolled'=>'askIf3DSEnrolled'
,'askIf3DSEnrolledResponse'=>'askIf3DSEnrolledResponse'
,'authReversal'=>'authReversal'
,'authReversalResponse'=>'authReversalResponse'
,'authorize'=>'authorize'
,'authorizeResponse'=>'authorizeResponse'
,'authorize3DS'=>'authorize3DS'
,'authorize3DSResponse'=>'authorize3DSResponse'
,'authorizeAndSettle'=>'authorizeAndSettle'
,'authorizeAndSettleResponse'=>'authorizeAndSettleResponse'
,'authorizeAndSettle3DS'=>'authorizeAndSettle3DS'
,'authorizeAndSettle3DSResponse'=>'authorizeAndSettle3DSResponse'
,'customerCheck'=>'customerCheck'
,'customerCheckResponse'=>'customerCheckResponse'
,'eInvoice'=>'eInvoice'
,'eInvoiceResponse'=>'eInvoiceResponse'
,'fundsTransfer'=>'fundsTransfer'
,'fundsTransferResponse'=>'fundsTransferResponse'
,'invoice'=>'invoice'
,'invoiceResponse'=>'invoiceResponse'
,'refund'=>'refund'
,'refundResponse'=>'refundResponse'
,'settle'=>'settle'
,'settleResponse'=>'settleResponse'
,'subscribe'=>'subscribe'
,'subscribeResponse'=>'subscribeResponse'
,'subscribeAndSettle'=>'subscribeAndSettle'
,'subscribeAndSettleResponse'=>'subscribeAndSettleResponse'
,'mpInitForm'=>'mpInitForm'
,'mpInitFormResponse'=>'mpInitFormResponse'
,'mpValidate'=>'mpValidate'
,'mpValidateResponse'=>'mpValidateResponse'
,'mpAuthorize'=>'mpAuthorize'
,'mpAuthorizeResponse'=>'mpAuthorizeResponse'

);

 function __construct($url='https://securedt.dibspayment.com/axis2/services/DTServerModuleService_v2?wsdl')
 {
  $this->soapClient = new SoapClient($url,array("classmap"=>self::$classmap,"trace" => true,"exceptions" => true));
 }
 
function authReversal(authReversal $authReversal)
{

$authReversalResponse = $this->soapClient->authReversal($authReversal);
return $authReversalResponse;

}
function checkSwedishPersNo(checkSwedishPersNo $checkSwedishPersNo)
{

$checkSwedishPersNoResponse = $this->soapClient->checkSwedishPersNo($checkSwedishPersNo);
return $checkSwedishPersNoResponse;

}
function fundsTransfer(fundsTransfer $fundsTransfer)
{

$fundsTransferResponse = $this->soapClient->fundsTransfer($fundsTransfer);
return $fundsTransferResponse;

}
function invoice(invoice $invoice)
{

$invoiceResponse = $this->soapClient->invoice($invoice);
return $invoiceResponse;

}
function customerCheck(customerCheck $customerCheck)
{

$customerCheckResponse = $this->soapClient->customerCheck($customerCheck);
return $customerCheckResponse;

}
function authorizeAndSettle3DS(authorizeAndSettle3DS $authorizeAndSettle3DS)
{

$authorizeAndSettle3DSResponse = $this->soapClient->authorizeAndSettle3DS($authorizeAndSettle3DS);
return $authorizeAndSettle3DSResponse;

}
function accountTransactionAuthorize(accountTransactionAuthorize $accountTransactionAuthorize)
{

$accountTransactionAuthorizeResponse = $this->soapClient->accountTransactionAuthorize($accountTransactionAuthorize);
return $accountTransactionAuthorizeResponse;

}
function authorize3DS(authorize3DS $authorize3DS)
{

$authorize3DSResponse = $this->soapClient->authorize3DS($authorize3DS);
return $authorize3DSResponse;

}
function subscribeAndSettle(subscribeAndSettle $subscribeAndSettle)
{

$subscribeAndSettleResponse = $this->soapClient->subscribeAndSettle($subscribeAndSettle);
return $subscribeAndSettleResponse;

}
function mpInitForm(mpInitForm $mpInitForm)
{

$mpInitFormResponse = $this->soapClient->mpInitForm($mpInitForm);
return $mpInitFormResponse;

}
function mpValidate(mpValidate $mpValidate)
{

$mpValidateResponse = $this->soapClient->mpValidate($mpValidate);
return $mpValidateResponse;

}
function mpAuthorize(mpAuthorize $mpAuthorize)
{

$mpAuthorizeResponse = $this->soapClient->mpAuthorize($mpAuthorize);
return $mpAuthorizeResponse;

}
function authorize(authorize $authorize)
{

$authorizeResponse = $this->soapClient->authorize($authorize);
return $authorizeResponse;

}
function eInvoice(eInvoice $eInvoice)
{

$eInvoiceResponse = $this->soapClient->eInvoice($eInvoice);
return $eInvoiceResponse;

}
function subscribe(subscribe $subscribe)
{

$subscribeResponse = $this->soapClient->subscribe($subscribe);
return $subscribeResponse;

}
function askIf3DSEnrolled(askIf3DSEnrolled $askIf3DSEnrolled)
{

$askIf3DSEnrolledResponse = $this->soapClient->askIf3DSEnrolled($askIf3DSEnrolled);
return $askIf3DSEnrolledResponse;

}
function authorizeAndSettle(authorizeAndSettle $authorizeAndSettle)
{

$authorizeAndSettleResponse = $this->soapClient->authorizeAndSettle($authorizeAndSettle);
return $authorizeAndSettleResponse;

}
function refund(refund $refund)
{

$refundResponse = $this->soapClient->refund($refund);
return $refundResponse;

}
function settle(settle $settle)
{

$settleResponse = $this->soapClient->settle($settle);
return $settleResponse;

}
function authReversal(authReversal $authReversal)
{

$authReversalResponse = $this->soapClient->authReversal($authReversal);
return $authReversalResponse;

}
function checkSwedishPersNo(checkSwedishPersNo $checkSwedishPersNo)
{

$checkSwedishPersNoResponse = $this->soapClient->checkSwedishPersNo($checkSwedishPersNo);
return $checkSwedishPersNoResponse;

}
function fundsTransfer(fundsTransfer $fundsTransfer)
{

$fundsTransferResponse = $this->soapClient->fundsTransfer($fundsTransfer);
return $fundsTransferResponse;

}
function invoice(invoice $invoice)
{

$invoiceResponse = $this->soapClient->invoice($invoice);
return $invoiceResponse;

}
function customerCheck(customerCheck $customerCheck)
{

$customerCheckResponse = $this->soapClient->customerCheck($customerCheck);
return $customerCheckResponse;

}
function authorizeAndSettle3DS(authorizeAndSettle3DS $authorizeAndSettle3DS)
{

$authorizeAndSettle3DSResponse = $this->soapClient->authorizeAndSettle3DS($authorizeAndSettle3DS);
return $authorizeAndSettle3DSResponse;

}
function accountTransactionAuthorize(accountTransactionAuthorize $accountTransactionAuthorize)
{

$accountTransactionAuthorizeResponse = $this->soapClient->accountTransactionAuthorize($accountTransactionAuthorize);
return $accountTransactionAuthorizeResponse;

}
function authorize3DS(authorize3DS $authorize3DS)
{

$authorize3DSResponse = $this->soapClient->authorize3DS($authorize3DS);
return $authorize3DSResponse;

}
function subscribeAndSettle(subscribeAndSettle $subscribeAndSettle)
{

$subscribeAndSettleResponse = $this->soapClient->subscribeAndSettle($subscribeAndSettle);
return $subscribeAndSettleResponse;

}
function mpInitForm(mpInitForm $mpInitForm)
{

$mpInitFormResponse = $this->soapClient->mpInitForm($mpInitForm);
return $mpInitFormResponse;

}
function mpValidate(mpValidate $mpValidate)
{

$mpValidateResponse = $this->soapClient->mpValidate($mpValidate);
return $mpValidateResponse;

}
function mpAuthorize(mpAuthorize $mpAuthorize)
{

$mpAuthorizeResponse = $this->soapClient->mpAuthorize($mpAuthorize);
return $mpAuthorizeResponse;

}
function authorize(authorize $authorize)
{

$authorizeResponse = $this->soapClient->authorize($authorize);
return $authorizeResponse;

}
function eInvoice(eInvoice $eInvoice)
{

$eInvoiceResponse = $this->soapClient->eInvoice($eInvoice);
return $eInvoiceResponse;

}
function subscribe(subscribe $subscribe)
{

$subscribeResponse = $this->soapClient->subscribe($subscribe);
return $subscribeResponse;

}
function askIf3DSEnrolled(askIf3DSEnrolled $askIf3DSEnrolled)
{

$askIf3DSEnrolledResponse = $this->soapClient->askIf3DSEnrolled($askIf3DSEnrolled);
return $askIf3DSEnrolledResponse;

}
function authorizeAndSettle(authorizeAndSettle $authorizeAndSettle)
{

$authorizeAndSettleResponse = $this->soapClient->authorizeAndSettle($authorizeAndSettle);
return $authorizeAndSettleResponse;

}
function refund(refund $refund)
{

$refundResponse = $this->soapClient->refund($refund);
return $refundResponse;

}
function settle(settle $settle)
{

$settleResponse = $this->soapClient->settle($settle);
return $settleResponse;

}}


?>