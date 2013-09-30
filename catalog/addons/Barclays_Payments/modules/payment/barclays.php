<?php
/**  
*  $Id: barclays.php v1.0 2013-01-01 datazen $
*
*  LoadedCommerce, Innovative eCommerce Solutions
*  http://www.loadedcommerce.com
*
*  Copyright (c) 2013 Loaded Commerce, LLC
*
*  @author     Loaded Commerce Team
*  @copyright  (c) 2013 Loaded Commerce Team
*  @license    http://loadedcommerce.com/license.html
*/
include_once(DIR_FS_CATALOG . 'includes/classes/transport.php');

class lC_Payment_barclays extends lC_Payment {     
 /**
  * The public title of the payment module
  *
  * @var string
  * @access protected
  */  
  protected $_title;
 /**
  * The code of the payment module
  *
  * @var string
  * @access protected
  */  
  protected $_code = 'barclays';
 /**
  * The status of the module
  *
  * @var boolean
  * @access protected
  */  
  protected $_status = false;
 /**
  * The sort order of the module
  *
  * @var integer
  * @access protected
  */  
  protected $_sort_order;  
 /**
  * The allowed credit card types (pipe separated)
  *
  * @var string
  * @access protected
  */ 
  protected $_allowed_types;  
 /**
  * The order id
  *
  * @var integer
  * @access protected
  */ 
  protected $_order_id;
 /**
  * The completed order status ID
  *
  * @var integer
  * @access protected
  */   
  protected $_order_status_complete;
 /**
  * The credit card image string
  *
  * @var string
  * @access protected
  */   
  protected $_card_images;
 /**
  * Constructor
  */      
  public function lC_Payment_barclays() {
    global $lC_Language;

    $this->_title = $lC_Language->get('payment_barclays_title');
    $this->_method_title = $lC_Language->get('payment_barclays_method_title');
    $this->_status = (defined('ADDONS_PAYMENT_BARCLAYS_PAYMENTS_STATUS') && (ADDONS_PAYMENT_BARCLAYS_PAYMENTS_STATUS == '1') ? true : false);
    $this->_sort_order = (defined('ADDONS_PAYMENT_BARCLAYS_PAYMENTS_SORT_ORDER') ? ADDONS_PAYMENT_BARCLAYS_PAYMENTS_SORT_ORDER : null);

    if (defined('ADDONS_PAYMENT_BARCLAYS_PAYMENTS_STATUS')) {
      $this->initialize();
    }
  }
 /**
  * Initialize the payment module 
  *
  * @access public
  * @return void
  */
  public function initialize() {
    global $lC_Database, $lC_Language, $order;

    if ((int)ADDONS_PAYMENT_BARCLAYS_PAYMENTS_ORDER_STATUS_ID > 0) {
      $this->order_status = ADDONS_PAYMENT_BARCLAYS_PAYMENTS_ORDER_STATUS_ID;
    }
    
    if ((int)ADDONS_PAYMENT_BARCLAYS_PAYMENTS_ORDER_STATUS_COMPLETE_ID > 0) {
      $this->_order_status_complete = ADDONS_PAYMENT_BARCLAYS_PAYMENTS_ORDER_STATUS_COMPLETE_ID;
    }    

    if (is_object($order)) $this->update_status();
    
    $this->form_action_url = 'https://mdepayments.epdq.co.uk/ncol/test/orderstandard.asp';
    
    $Qcredit_cards = $lC_Database->query('select credit_card_name from :table_credit_cards where credit_card_status = :credit_card_status');
    $Qcredit_cards->bindRaw(':table_credit_cards', TABLE_CREDIT_CARDS);
    $Qcredit_cards->bindInt(':credit_card_status', '1');
    $Qcredit_cards->setCache('credit-cards');
    $Qcredit_cards->execute();

    while ($Qcredit_cards->next()) {
      $this->_card_images .= lc_image('images/cards/cc_' . strtolower(str_replace(" ", "_", $Qcredit_cards->value('credit_card_name'))) . '.png', null, null, null, 'style="vertical-align:middle; margin:0 2px;"');
      $name = strtolower($Qcredit_cards->value('credit_card_name'));
      if (stristr($Qcredit_cards->value('credit_card_name'), 'discover')) $name = 'Discover';
      if (stristr($Qcredit_cards->value('credit_card_name'), 'jcb')) $name = 'JCB';
      $this->_allowed_types .= ucwords($name) . '|';
    }
    if (substr($this->_allowed_types, -1) == '|') $this->_allowed_types = substr($this->_allowed_types, 0, strlen($this->_allowed_types) - 1);
    
    $Qcredit_cards->freeResult();      
  }
 /**
  * Disable module if zone selected does not match billing zone  
  *
  * @access public
  * @return void
  */  
  public function update_status() {
    global $lC_Database, $order;

    if ( ($this->_status === true) && ((int)ADDONS_PAYMENT_BARCLAYS_PAYMENTS_ZONE > 0) ) {
      $check_flag = false;

      $Qcheck = $lC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
      $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
      $Qcheck->bindInt(':geo_zone_id', ADDONS_PAYMENT_BARCLAYS_PAYMENTS_ZONE);
      $Qcheck->bindInt(':zone_country_id', $order->billing['country']['id']);
      $Qcheck->execute();

      while ($Qcheck->next()) {
        if ($Qcheck->valueInt('zone_id') < 1) {
          $check_flag = true;
          break;
        } elseif ($Qcheck->valueInt('zone_id') == $order->billing['zone_id']) {
          $check_flag = true;
          break;
        }
      }

      if ($check_flag == false) {
        $this->_status = false;
      }
    }
  } 
 /**
  * Return the payment selections array
  *
  * @access public
  * @return array
  */   
  public function selection() {
    global $lC_Language;

    $selection = array('id' => $this->_code,
                       'module' => '<div class="payment-selection">' . $this->_method_title . '<span>' . $this->_card_images . '</span></div><div class="payment-selection-title">' . $lC_Language->get('payment_barclays_method_blurb') . '</div>');    
    
    return $selection;
  }
 /**
  * Perform any pre-confirmation logic
  *
  * @access public
  * @return boolean
  */ 
  public function pre_confirmation_check() {
    return false;
  }
 /**
  * Perform any post-confirmation logic
  *
  * @access public
  * @return integer
  */ 
  public function confirmation() {
    
    return false;   
  }


  function build_parm_array($order_id){
    global $lC_ShoppingCart, $lC_Currencies, $lC_Customer, $lC_Language;

    $array = array('PSPID' => ADDONS_PAYMENT_BARCLAYS_PAYMENTS_PSPID,
                   'AMOUNT' => str_replace('.', '', $lC_Currencies->formatRaw($lC_ShoppingCart->getTotal(), $lC_Currencies->getCode())),
                   'CURRENCY' => $_SESSION['currency'],
                   'ORDERID' => $order_id,
                   'LANGUAGE' => $lC_Language->getCode(),
                   'CN' => $lC_ShoppingCart->getBillingAddress('firstname').' '.$lC_ShoppingCart->getBillingAddress('lastname'),
                   'OWNERADDRESS' => $lC_ShoppingCart->getBillingAddress('street_address'),
                   'OWNERZIP' => $lC_ShoppingCart->getBillingAddress('postcode'),
                   'OWNERCITY' => $lC_ShoppingCart->getBillingAddress('country_iso_code_2'),
                   'OWNERTOWN' => $lC_ShoppingCart->getBillingAddress('city'),
                   'OWNERTELNO' => $lC_Customer->getTelephone(),
                   'EMAIL' => $lC_Customer->getEmailAddress(),
                   'BACKURL' => lc_href_link(FILENAME_CHECKOUT, 'cart', 'SSL', true, true, true),
                   'DECLINEURL' => lc_href_link(FILENAME_CHECKOUT, 'process', 'SSL', true, true, true),
                   'CANCELURL' => lc_href_link(FILENAME_CHECKOUT, 'process', 'SSL', true, true, true),
                   'ACCEPTURL' => lc_href_link(FILENAME_CHECKOUT, 'process', 'SSL', true, true, true));

    return $array;
  }
 /**
  * Return the confirmation button logic
  *
  * @access public
  * @return string
  */ 
  public function process_button() {

    $process_button_string = '';
    $order_id = lC_Order::insert();
    $data_array = $this->build_parm_array($order_id);

    foreach($data_array as $key => $val){

      if(isset($val)){
        
        $process_button_string .= lc_draw_hidden_field($key, $val);
      }
    }
    
    return $process_button_string.lc_draw_hidden_field('SHASIGN', $this->sha_sign());
  }
 /**
  * Generate hash
  *
  * @access private
  * @return string
  */ 
   private function generate_sha_from_barclays(){
    // Need to shasign values from the _GET and then compare with the _GET['SHASIGN'] sent from barclays
    $array = array_shift($_GET); // Remove first element

    $string_to_hash = '';
    foreach($array as $key => $val){

      if(isset($val)){
                
       $string_to_hash .= strtoupper($key).$val;
      }
    }

    ksort($string_to_shash);
    $sha_sign = sha1($string_to_hash);
    
    print_r($array);
    echo $string_to_hash.'<br>';
    echo $sha_sign . ' ' . $_GET['SHASIGN'];

    exit();
    return strtoupper($sha_sign);
  }
 /**
  * Parse the response from the processor
  *
  * @access public
  * @return string
  */
  public function process() {
    global $lC_Language, $lC_Database, $lC_MessageStack;

    if(isset($_GET) && !empty($_GET)){

      if(isset($_GET['orderID']) && isset($_GET['STATUS']) && isset($_GET['SHASIGN'])){

        $orderid = $_GET['orderID'];
        $status = $_GET['STATUS'];
        $sha = $_GET['SHASIGN'];

        $sha_gen = $this->generate_sha_from_barclays();

       // if($sha == $sha_gen){ // No fraud detected
        if(true){

          if($status == 2 || $status == 93){ // Error

            lC_Order::remove($orderid);
            $error_message = '&payment_error=' . $lC_Language->get('text_label_error') . ' ' . $lC_Language->get('payment_barclays_declined');
            lc_redirect(lc_href_link(FILENAME_CHECKOUT, 'payment' . $error_message, 'SSL'));
          }elseif($status == 5 || $status == 4 || $status == 9){ // Order accepted

            lC_Order::process($orderid, $this->_order_status_complete);
          }elseif($status == 1){ // Order was canceled

            lc_redirect(lc_href_link(FILENAME_CHECKOUT, 'cart', 'SSL'));
          }
        } else { // Fraud detected

          lc_redirect(lc_href_link(FILENAME_CHECKOUT, 'cart', 'SSL'));
        }
      }
    }
  } 
 /**
  * Check the status of the pasyment module
  *
  * @access public
  * @return boolean
  */ 
  public function check() {
    if (!isset($this->_check)) {
      $this->_check = defined('ADDONS_PAYMENT_BARCLAYS_PAYMENTS_STATUS');
    }

    return $this->_check;
  }

  protected function sha_sign() {
    global $lC_ShoppingCart, $lC_Currencies;

    $order_id = lC_Order::insert();

    $string_to_hash = '';

    $data_array = $this->build_parm_array($order_id);

    ksort($data_array);

    foreach($data_array as $key => $val){

            if(isset($val)){
              
              $string_to_hash .= sprintf("%s=%s%s", $key, $val, ADDONS_PAYMENT_BARCLAYS_PAYMENTS_PASSWORD);
            }
    }

    $sha_sign = sha1($string_to_hash);
    
    return strtoupper($sha_sign);
  }

}

?>