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
      $Qcheck->bindValue(':customers_external_id', $external_id);
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
      $Qcheck->bindValue(':customers_external_id', $external_id);
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
          $redirect = true;
        } else if(lC_Account::checkDuplicateEntry($_GET['email']) === false) {        
          // update email for existing customers_external_id
          $Qupdate = $lC_Database->query('update :table_customers set customers_email_address = :customers_email_address where customers_external_id = :customers_external_id');
          $Qupdate->bindTable(':table_customers', TABLE_CUSTOMERS);
          $Qupdate->bindValue(':customers_email_address', $_GET['email']);
          $Qupdate->bindInt(':customers_external_id', $_GET['external_id']);
          $Qupdate->execute();
          $redirect = true;
        }
      } else if (lC_Account::checkEntry($_GET['email'])) {
             
        // update customers_external_id for existing email_address
          $Qupdate = $lC_Database->query('update :table_customers set customers_external_id = :customers_external_id where customers_email_address = :customers_email_address');
          $Qupdate->bindTable(':table_customers', TABLE_CUSTOMERS);
          $Qupdate->bindValue(':customers_email_address', $_GET['email']);
          $Qupdate->bindInt(':customers_external_id', $_GET['external_id']);
          $Qupdate->execute();
          $redirect = true;
      } else {
           
        $error = false;
        $customers_name = explode('-',$_GET['name'],2);
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
      
        if ( $error == false && lC_Account::createEntry($data)) {
          $redirect == true;
        }                  
      }
 
      if($redirect == true) {     
        lc_redirect(lc_href_link($_GET['redirect']));        
      }      
    }
  }
?>