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

    $encrypted_key = substr(utility::generateUID(), 0, -6);

    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Enable AddOn', 'ADDONS_CONNECTORS_" . strtoupper($this->_code) . "_STATUS', '-1', 'Do you want to enable this addon?', '6', '0', 'lc_cfg_use_get_boolean_value', 'lc_cfg_set_boolean_value(array(1, -1))', now())");
    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Remote Login URL', 'ADDONS_CONNECTORS_" . strtoupper($this->_code) . "_REMOTE_LOGIN_URL', '', 'Put Remote Login URL given in Wordpress Loaded7 SSO Options', '6', '0', 'lc_cfg_set_textarea_field', now())");
    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Remote Logout URL', 'ADDONS_CONNECTORS_" . strtoupper($this->_code) . "_REMOTE_LOGOUT_URL', '', 'Put Remote Logout URL given in Wordpress Loaded7 SSO Options', '6', '0', 'lc_cfg_set_textarea_field', now())");
    $lC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Authentication Token', 'ADDONS_CONNECTORS_" . strtoupper($this->_code) . "_AUTHENTICATION_TOKEN', '".$encrypted_key."', '', '6', '0', 'lc_cfg_set_readonly_input_field', now())");    

    $lC_Database->simpleQuery("alter table " . TABLE_CUSTOMERS . " add external_id int( 11 ) null, add unique (external_id)");
  }
 /**
  * Return the configuration parameter keys an an array
  *
  * @access public
  * @return array
  */
  public function getKeys() {
    if (!isset($this->_keys)) {
      $this->_keys = array('ADDONS_CONNECTORS_' . strtoupper($this->_code) . '_STATUS',
                           'ADDONS_CONNECTORS_' . strtoupper($this->_code) . '_REMOTE_LOGIN_URL',
                           'ADDONS_CONNECTORS_' . strtoupper($this->_code) . '_REMOTE_LOGOUT_URL',
                           'ADDONS_CONNECTORS_' . strtoupper($this->_code) . '_AUTHENTICATION_TOKEN');
    }

    return $this->_keys;
  }
}
?>