<?php

//  && isset($_POST['callbackPW']) && !empty($_POST['callbackPW']) && defined(ADDONS_PAYMENT_WORLDPAY_HOSTED_PAYMENT_CALLBACK_PASSWORD) && isset($_POST['transStatus']) && !empty($_POST['transStatus']) && isset($_POST['cartId']) && !empty($_POST['cartId'])

require('../../includes/application_top.php');
require_once($lC_Vqmod->modCheck(DIR_FS_CATALOG . 'includes/classes/order.php'));

function meta_redirect($url){

 // echo '<meta http-equiv="refresh" content="0;url='.$url.'">';
}







$M_hash = (isset($_POST['M_hash']) && $_POST['M_hash'] != NULL) ? preg_replace('/[^A-Za-z0-9\s]/', '', $_POST['M_hash']) : NULL;
$M_sid = (isset($_POST['M_sid']) && $_POST['M_sid'] != NULL) ? preg_replace('/[^A-Za-z0-9\s]/', '', $_POST['M_sid']) : NULL;
$M_cid = (isset($_POST['M_cid']) && $_POST['M_cid'] != NULL) ? preg_replace('/[^A-Za-z0-9\s]/', '', $_POST['M_cid']) : NULL;
$M_lang = (isset($_POST['M_lang']) && $_POST['M_lang'] != NULL) ? preg_replace('/[^A-Za-z0-9\s]\_/', '', $_POST['M_lang']) : NULL;
$cartId = (isset($_POST['cartId']) && $_POST['cartId'] != NULL) ? preg_replace('/[^A-Za-z0-9\s]\-/', '', $_POST['cartId']) : NULL;
$amount = (isset($_POST['amount']) && $_POST['amount'] != NULL) ? preg_replace('/[^A-Za-z0-9\s]\./', '', $_POST['amount']) : 0;
$md5_pass = (defined('ADDONS_PAYMENT_WORLDPAY_HOSTED_PAYMENT_MD5_PASSWORD') && ADDONS_PAYMENT_WORLDPAY_HOSTED_PAYMENT_MD5_PASSWORD != NULL) ? ADDONS_PAYMENT_WORLDPAY_HOSTED_PAYMENT_MD5_PASSWORD : NULL;

/*
echo $M_hash.'<br><br>';

echo $M_sid.'<br>';
echo $M_cid.'<br>';
echo $cartId.'<br>';
echo $M_lang.'<br>';
echo number_format($_POST['amount'], 2).'<br>';
echo $md5_pass.'<br>';
echo md5($M_sid . $M_cid . $cartId . $M_lang . number_format($_POST['amount'], 2) . $md5_pass);
*/

if($M_hash == md5($M_sid . $M_cid . $cartId . $M_lang . number_format($_POST['amount'], 2) . $md5_pass)){

  $pass = true;
}


if($pass){

  $status = $_POST['transStatus'];
  $order_id = $_POST['cartId'];
  
    if($status == 'Y'){ // Transaction successfull

      lC_Order::process($order_id, ADDONS_PAYMENT_WORLDPAY_HOSTED_PAYMENT_ORDER_STATUS_COMPLETE_ID);
      $redirect_url = lc_href_link(FILENAME_CHECKOUT, 'success', 'SSL', true, true, true);
    }elseif($status == 'C'){ // Order canceled
      
      $redirect_url = lc_href_link(FILENAME_CHECKOUT, 'cart', 'SSL', true, true, true);
    }else{ // Something else went wrong, send back to payment page

      $error_message = '&payment_error=' . $lC_Language->get('text_label_error') . ' ' . $_POST['rawAuthMessage'];
      $redirect_url = lc_href_link(FILENAME_CHECKOUT, 'payment'.$error_message, 'SSL', true, true, true);
    }
}else{

  $redirect_url = lc_href_link(FILENAME_CHECKOUT, 'cart', '', true, true, true); // Default redirect
}

meta_redirect($redirect_url);

?>