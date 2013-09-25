<?php


require('../../includes/application_top.php');
require_once($lC_Vqmod->modCheck(DIR_FS_CATALOG . 'includes/classes/order.php'));

if(isset($_POST['M_sid']) && !empty($_POST['M_sid'])){

  if($_POST['transStatus'] == 'Y'){

    $pass = false;
    
    if(isset($_POST['M_hash']) && !empty($_POST['M_hash']) && ($_POST['M_hash'] == md5($_POST['M_sid'] . $_POST['M_cid'] . $_POST['cartId'] . $_POST['M_lang'] . number_format($_POST['amount'], 2) . MODULE_PAYMENT_WORLDPAY_HOSTED_PAYMENT_MD5_PASSWORD))) {
      
      $pass = true;
    }

    if(isset($_POST['callbackPW']) && ($_POST['callbackPW'] != ADDONS_PAYMENT_WORLDPAY_HOSTED_PAYMENT_CALLBACK_PASSWORD)){
      
      $pass = false;
    }

    if(lc_not_null(ADDONS_PAYMENT_WORLDPAY_HOSTED_PAYMENT_CALLBACK_PASSWORD) && !isset($_POST['callbackPW'])){

      $pass = false;
    }


    // Check if all is ok
    if($pass == true){

      $order_id = $_POST['cartId'];
      lC_Order::process($order_id, ADDONS_PAYMENT_WORLDPAY_HOSTED_PAYMENT_ORDER_STATUS_COMPLETE_ID);
    }else{

      $order_id = $_POST['cartId'];
      lC_Order::remove($order_id);
    }
  }
}

?>