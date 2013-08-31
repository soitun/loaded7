<?php
/*
  $Id: controller.php v1.0 2013-04-20 datazen $

  Loaded Commerce, Innovative eCommerce Solutions
  http://www.loadedcommerce.com

  Copyright (c) 2013 Loaded Commerce, LLC

  @author     Loaded Commerce Team
  @copyright  (c) 2013 LoadedCommerce Team
  @license    http://loadedcommerce.com/license.html
*/
class Loaded7_SSO_Connector extends lC_Addon { // your addon must extend lC_Addon
  /*
  * Class constructor
  */
  public function Loaded7_SSO_Connector() {    
    global $lC_Language;    
   /**
    * The addon type (category)
    * valid types; payment, shipping, themes, checkout, catalog, admin, reports, connectors, other 
    */    
    $this->_type = 'connectors';
   /**
    * The addon class name
    */    
    $this->_code = 'Loaded7_SSO_Connector';       
   /**
    * The addon title used in the addons store listing
    */     
    $this->_title = $lC_Language->get('addon_loaded7_sso_connector_title');
   /**
    * The addon description used in the addons store listing
    */     
    $this->_description = $lC_Language->get('addon_loaded7_sso_connector_description');
   /**
    * The developers name
    */    
    $this->_author = 'Loaded Commerce, LLC';
   /**
    * The developers web address
    */    
    $this->_authorWWW = 'http://www.loadedcommerce.com';    
   /**
    * The addon version
    */     
    $this->_version = '1.0.0'; 
   /**
    * The addon image used in the addons store listing
    */     
    $this->_thumbnail = lc_image(DIR_WS_CATALOG . 'addons/' . $this->_code . '/images/logo.png');
   /**
    * The addon enable/disable switch
    */    
    $this->_enabled = (defined('ADDONS_CONNECTORS_' . strtoupper($this->_code) . '_STATUS') && @constant('ADDONS_CONNECTORS_' . strtoupper($this->_code) . '_STATUS') == '1') ? true : false;
  }
 /**
  * Checks to see if the addon has been installed
  *
  * @access public
  * @return boolean
  */
  public function isInstalled() {
    return (bool)defined('ADDONS_CONNECTORS_' . strtoupper($this->_code) . '_STATUS');
  }
 /**
  * Install the addon
  *
  * @access public
  * @return void
  */
  public function install() {
    global $lC_Database;

    $encrypted_key = hash('sha256', mktime());

    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Enable AddOn', 'ADDONS_CONNECTORS_" . strtoupper($this->_code) . "_STATUS', '-1', 'Do you want to enable this addon?', '6', '0', 'lc_cfg_use_get_boolean_value', 'lc_cfg_set_boolean_value(array(1, -1))', now())");
    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Wordpress URL', 'ADDONS_CONNECTORS_" . strtoupper($this->_code) . "_WORDPRESS_URL', '', '\"http://www.mywordepress.com\" for top level domain, \"http://wordepress.mystore.com\" for subdomains, \"http://www.mystore.com/wordepress/\" for a folder on a top level domain', '6', '0', now())");
    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Authentication Token', 'ADDONS_CONNECTORS_" . strtoupper($this->_code) . "_AUTHENTICATION_TOKEN', '".$encrypted_key."', '', '6', '0', 'lc_cfg_set_readonly_input_field', now())");    

    $lC_Database->simpleQuery("ALTER TABLE " . TABLE_CUSTOMERS . " ADD `customers_external_id` INT( 11 ) NULL , ADD UNIQUE (`customers_external_id`)");
  }
 /**
  * Return the configuration parameter keys an an array
  *
  * @access public
  * @return array
  */
  public function getKeys() {
    if (!isset($this->_keys)) {
      $this->_keys = array('ADDONS_CONNECTORS_' . strtoupper($this->_code) . '_STATUS', 'ADDONS_CONNECTORS_' . strtoupper($this->_code) . '_WORDPRESS_URL', 'ADDONS_CONNECTORS_' . strtoupper($this->_code) . '_AUTHENTICATION_TOKEN');
    }

    return $this->_keys;
  }  
}
?>