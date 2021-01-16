<rn:meta title="#rn:msg:SHP_TITLE_HDG#" login_required="true" template="basic.php" login_required="false" clickstream="payment" />
<?


// function _logToFile($lineNum, $message){
//     $hundredths = ltrim(microtime(), "0");
    
//     $fp = fopen('/tmp/esgLogPayCron/refundNewPay_'.date("Ymd").'.log', 'a');
//     fwrite($fp,  date('H:i:s.').$hundredths.": success_paymethod Controller @ $lineNum : ".$message."\n");
//     fclose($fp);
    
// }

$this->model('custom/log_model')->log(__FILE__, __FUNCTION__, 0, 0, 14, "*********Begin Refund PayMethod***********","PayMethod"); /*_logToFile(14, "*********Begin Refund PayMethod***********");*/
//have to use this header re write for frontstream redirect in an iframe to work.
//RNT write the header to Deny all redirects in an iframe for click jack purposes.
//if this does not conform to security standards we will need to investigate a different method.
header('X-Frame-Options: PLACEHOLDER');

$baseURL = \RightNow\Utils\Url::getShortEufBaseUrl();

$messagesArr = array();
$this -> load -> helper('constants');

$cleanGetData = array();
foreach ($_GET as $key => $value) {
    $cleanGetData[addslashes($key)] = addslashes($value);
}

$dataString = print_r($cleanGetData, true);
if(is_string($dataString))
    $this->model('custom/log_model')->log(__FILE__, __FUNCTION__, 0, 0, 32, print_r($dataString, true),"PayMethod"); /*_logToFile(36, $dataString);*/

$newpaymeth = explode("-", $cleanGetData['InvoiceNumber']);
$this->model('custom/log_model')->log(__FILE__, __FUNCTION__, 0, 0, 35, print_r($newpaymeth, true),"PayMethod"); /*_logToFile(41, print_r($newpaymeth, true));*/

$c_id = $this -> session -> getSessionData('theRealContactID');
if(empty($c_id)){
    $c_id = $newpaymeth[2];
}

$this->model('custom/log_model')->log(__FILE__, __FUNCTION__, $c_id, 0, 42,"Contact:$c_id","PayMethod"); /*_logToFile(29, "Contact:$c_id");*/

//this condition is if a customer only enters their card as a payment method on /app/paymentmethods
if ($cleanGetData['StatusCode'] == 0 && $newpaymeth[0] == "NewPM") {

    $this->model('custom/log_model')->log(__FILE__, __FUNCTION__, $c_id, 0, 47,"Creating New Pay method","PayMethod"); /*_logToFile(47, "Creating New Pay method");*/
    if ($cleanGetData['CardType'] != "Checking") {
        $newPayment = $this -> model('custom/paymentMethod_model') -> createPaymentMethod(intval($c_id), $cleanGetData['CardType'], $cleanGetData['PNRef'], "Credit Card", $cleanGetData['AccountExpMonth'], $cleanGetData['AccountExpYear'], $cleanGetData['AccountLastFour']);
        $transType = "card";
    } else {
        $newPayment = $this -> model('custom/paymentMethod_model') -> createPaymentMethod(intval($c_id), $cleanGetData['CardType'], $cleanGetData['PNRef'], "EFT", null, null, $cleanGetData['AccountLastFour']);
        $transType = "check";
    }

    $this->model('custom/log_model')->log(__FILE__, __FUNCTION__, $c_id, 0, 56,"Post Create New Pay method ID:".$newPayment->ID,"PayMethod"); /*_logToFile(47, "Post Create New Pay method ID:".$newPayment -> ID);*/

    if ($newPayment -> ID < 1) {
        $messagesArr[] = "There was a problem saving your payment information.";
        $status = TRANSACTION_SALE_ERROR_STATUS;
    } else {
        $this->model('custom/log_model')->log(__FILE__, __FUNCTION__, $c_id, 0, 62,"Reversing Pay Method PNRef:".$cleanGetData['PNRef']." TransType:".$transType,"PayMethod"); /*_logToFile(62, "Reversing Pay Method PNRef:".$cleanGetData['PNRef']." TransType:".$transType);*/
        $success = $this -> model('custom/frontstream_model') -> ReversePayment($cleanGetData['PNRef'], $transType);
        if ($success) {
            $headerRedirect = (getUrlParm('p_id') > 0) ? "/app/account/pledges/c_id/$c_id/action/updateConfirm/p_id/".getUrlParm('p_id') : "/app/account/transactions/c_id/".$c_id."/action/updateConfirm/";
        }

    }
    
    $this->model('custom/log_model')->log(__FILE__, __FUNCTION__, $c_id, 0, 70,"Post Reverse Pay method:".$newPayment -> ID,"PayMethod"); /*_logToFile(47, "Post Reverse Pay method:".$newPayment -> ID);*/

    if(getUrlParm('p_id') > 0){ //if we are updating the paymethod on a pledge, assign that pledge
        $success = $this -> model('custom/donation_model') -> savePayMethodToPledge(getUrlParm('p_id') );
    }

}
?>

<div class="responseMessage">
    <br />Your New Payment Method has been saved. If you are updating your pledge it has been associated to your pledge. <br />
    </br>You will be redirected to your pledge list now. If this doesn't happen, please refresh your page.
</div>
<?
    if ($headerRedirect) {
        echo '<script type="text/javascript">window.top.location.href="' . $baseURL . $headerRedirect . '"</script>';
    }
?>