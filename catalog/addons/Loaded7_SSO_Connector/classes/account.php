<?php
/*
  $Id: account.php v1.0 2013-01-01 datazen $

  LoadedCommerce, Innovative eCommerce Solutions
  http://www.loadedcommerce.com

  Copyright (c) 2013 Loaded Commerce, LLC

  @author     LoadedCommerce Team
  @copyright  (c) 2013 LoadedCommerce Team
  @license    http://loadedcommerce.com/license.html
*/

/**
 * The lC_Account class manages customer accounts
 */

  class lC_Account_sso_connector extends lC_Account_Login {
  
 /**
 * Checks if a customer account record exists with the provided external_id
 *
 * @param string $email_address The e-mail address to check for
 * @access public
 * @return boolean
 */

    public static function checkExternalID($external_id) {
      global $lC_Database;

      $Qcheck = $lC_Database->query('select customers_id from :table_customers where customers_external_id = :customers_external_id limit 1');
      $Qcheck->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcheck->bindInt(':customers_external_id', $external_id);
      $Qcheck->execute();

      return ( $Qcheck->numberOfRows() === 1 );
    }

 /**
 * Checks if a customer account record exists with the provided e-mail address and external_id
 *
 * @param string $email_address The e-mail address to check for
 * @access public
 * @return boolean
 */

    public static function checkEmailforExternalID($email_address,$external_id) {
      global $lC_Database;

      $Qcheck = $lC_Database->query('select customers_id from :table_customers where customers_email_address = :customers_email_address and customers_external_id = :customers_external_id limit 1');
      $Qcheck->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcheck->bindValue(':customers_email_address', $email_address);
      $Qcheck->bindInt(':customers_external_id', $external_id);
      $Qcheck->execute();

      return ( $Qcheck->numberOfRows() === 1 );
    }

      /* Private methods */   
    function _processSSO() {
      global $lC_Database, $lC_Session, $lC_Language, $lC_ShoppingCart, $lC_MessageStack, $lC_Customer, $lC_NavigationHistory, $lC_Vqmod;
      
      require($lC_Vqmod->modCheck('includes/classes/account.php'));
      
      $redirect = false;
      $data = array();

      if (self::checkExternalID($_GET['external_id'])) {
      
        if (self::checkEmailforExternalID($_GET['email'],$_GET['external_id'])) {          
          self::_loginSSO();
          $redirect = true;
        } else if(lC_Account::checkDuplicateEntry($_GET['email']) === false) {        
          // update email for existing customers_external_id
          $Qupdate = $lC_Database->query('update :table_customers set customers_email_address = :customers_email_address where customers_external_id = :customers_external_id');
          $Qupdate->bindTable(':table_customers', TABLE_CUSTOMERS);
          $Qupdate->bindValue(':customers_email_address', $_GET['email']);
          $Qupdate->bindInt(':customers_external_id', $_GET['external_id']);
          $Qupdate->execute();
          self::_loginSSO();
          $redirect = true;

        }
      } else if (lC_Account::checkEntry($_GET['email'])) {
             
        // update customers_external_id for existing email_address
          $Qupdate = $lC_Database->query('update :table_customers set customers_external_id = :customers_external_id where customers_email_address = :customers_email_address');
          $Qupdate->bindTable(':table_customers', TABLE_CUSTOMERS);
          $Qupdate->bindValue(':customers_email_address', $_GET['email']);
          $Qupdate->bindInt(':customers_external_id', $_GET['external_id']);
          $Qupdate->execute();
          self::_loginSSO();
          $redirect = true;
      } else {           
        $error = false;
        $customers_name = explode(' ',$_GET['name'],2);
        $data['external_id'] = $_GET['external_id'];
        $data['password'] = mktime();

        if (isset($customers_name[0]) && (strlen(trim($customers_name[0])) >= ACCOUNT_FIRST_NAME)) {
          $data['firstname'] = $customers_name[0];
        } else {
          $error = true;
        }

        if (isset($customers_name[1]) && (strlen(trim($customers_name[1])) >= ACCOUNT_LAST_NAME)) {
          $data['lastname'] = $customers_name[1];
        } else {
          $error = true;
        }

        if (lC_Account::checkDuplicateEntry($_GET['email']) === false) {
          $data['email_address'] = $_GET['email'];
        } else {
          $error = true;
        } 
      
        if ( $error == false && self::createSSOEntry($data)) {
          $redirect == true;
        }                  
      }
 
      if($redirect == true) {
        lc_redirect(lc_href_link($_GET['redirect'], null, 'AUTO'));
      }      
    }

    /* Private methods */
    function _loginSSO() {
      global $lC_Database, $lC_Session, $lC_Language, $lC_ShoppingCart, $lC_MessageStack, $lC_Customer, $lC_NavigationHistory, $lC_Vqmod;

      require_once($lC_Vqmod->modCheck('includes/classes/account.php'));

      if (lC_Account::checkEntry($_GET['email'])) {

          if (SERVICE_SESSION_REGENERATE_ID == '1') {
            $lC_Session->recreate();
          }

          $lC_Customer->setCustomerData(lC_Account::getID($_GET['email']));

          $Qupdate = $lC_Database->query('update :table_customers set date_last_logon = :date_last_logon, number_of_logons = number_of_logons+1 where customers_id = :customers_id');
          $Qupdate->bindTable(':table_customers', TABLE_CUSTOMERS);
          $Qupdate->bindRaw(':date_last_logon', 'now()');
          $Qupdate->bindInt(':customers_id', $lC_Customer->getID());
          $Qupdate->execute();
          
          if ($lC_ShoppingCart->hasContents() === false) {
            $lC_ShoppingCart->synchronizeWithDatabase();
          }

          $lC_NavigationHistory->removeCurrentPage();

          if ($lC_NavigationHistory->hasSnapshot()) {
            $lC_NavigationHistory->redirectToSnapshot();
          }         
      } 
    }
     public static function createSSOEntry($data) {
      global $lC_Database, $lC_Session, $lC_Language, $lC_ShoppingCart, $lC_Customer, $lC_NavigationHistory;

      $Qcustomer = $lC_Database->query('insert into :table_customers (customers_firstname, customers_lastname, customers_email_address, customers_newsletter, customers_status, customers_ip_address, customers_password, customers_gender, customers_dob, number_of_logons, date_account_created, customers_external_id) values (:customers_firstname, :customers_lastname, :customers_email_address, :customers_newsletter, :customers_status, :customers_ip_address, :customers_password, :customers_gender, :customers_dob, :number_of_logons, :date_account_created, :customers_external_id)');
      $Qcustomer->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomer->bindValue(':customers_firstname', $data['firstname']);
      $Qcustomer->bindValue(':customers_lastname', $data['lastname']);
      $Qcustomer->bindValue(':customers_email_address', $data['email_address']);
      $Qcustomer->bindValue(':customers_newsletter', (isset($data['newsletter']) && ($data['newsletter'] == '1') ? '1' : ''));
      $Qcustomer->bindValue(':customers_status', '1');
      $Qcustomer->bindValue(':customers_ip_address', lc_get_ip_address());
      $Qcustomer->bindValue(':customers_password', lc_encrypt_string($data['password']));
      $Qcustomer->bindValue(':customers_gender', (((ACCOUNT_GENDER > -1) && isset($data['gender']) && (($data['gender'] == 'm') || ($data['gender'] == 'f'))) ? $data['gender'] : ''));
      $Qcustomer->bindValue(':customers_dob', ((ACCOUNT_DATE_OF_BIRTH == '1') ? @date('Ymd', $data['dob']) : '0000-00-00 00:00:00'));
      $Qcustomer->bindInt(':number_of_logons', 0);
      $Qcustomer->bindRaw(':date_account_created', 'now()');
      $Qcustomer->bindInt(':customers_external_id', $data['external_id']);
      $Qcustomer->execute();

      if ( $Qcustomer->affectedRows() === 1 ) {
        $customer_id = $lC_Database->nextID();

        if ( SERVICE_SESSION_REGENERATE_ID == '1' ) {
          $lC_Session->recreate();
        }

        $lC_Customer->setCustomerData($customer_id);

// restore cart contents
        $lC_ShoppingCart->synchronizeWithDatabase();

        $lC_NavigationHistory->removeCurrentPage();

// build the welcome email content
        if ( (ACCOUNT_GENDER > -1) && isset($data['gender']) ) {
           if ( $data['gender'] == 'm' ) {
             $email_text = sprintf($lC_Language->get('email_addressing_gender_male'), $lC_Customer->getLastName()) . "\n\n";
           } else {
             $email_text = sprintf($lC_Language->get('email_addressing_gender_female'), $lC_Customer->getLastName()) . "\n\n";
           }
        } else {
          $email_text = sprintf($lC_Language->get('email_addressing_gender_unknown'), $lC_Customer->getName()) . "\n\n";
        }

        $email_text .= sprintf($lC_Language->get('email_create_account_body'), STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);
        
        lc_email($lC_Customer->getName(), $lC_Customer->getEmailAddress(), sprintf($lC_Language->get('email_create_account_subject'), STORE_NAME), $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

        return true;
      }

      return false;
    }


  }
?>